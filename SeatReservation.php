<?php

class SeatReservation
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function createTable(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS `seat_reservations` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `user_id` INT NOT NULL,
                `seat_row` INT NOT NULL,
                `seat_col` INT NOT NULL,
                `reserved_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                CONSTRAINT `fk_seat_reservations_user`
                    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
                UNIQUE KEY `uniq_seat` (`seat_row`, `seat_col`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        $this->conn->query($sql);
    }

    public function reserveSeats(int $userId, array $seats): void
    {
        $this->createTable();

        $checkStmt = $this->conn->prepare("SELECT id FROM seat_reservations WHERE seat_row = ? AND seat_col = ? LIMIT 1");
        $insertStmt = $this->conn->prepare("INSERT INTO seat_reservations (user_id, seat_row, seat_col) VALUES (?, ?, ?)");

        if (!$checkStmt || !$insertStmt) {
            return;
        }

        foreach ($seats as $seat) {
            $row = (int)($seat['row'] ?? -1);
            $col = (int)($seat['col'] ?? -1);
            if ($row < 0 || $col < 0) {
                continue;
            }

            $checkStmt->bind_param("ii", $row, $col);
            $checkStmt->execute();
            $checkStmt->store_result();

            if ($checkStmt->num_rows === 0) {
                $insertStmt->bind_param("iii", $userId, $row, $col);
                $insertStmt->execute();
            }
        }

        $checkStmt->close();
        $insertStmt->close();
    }

    public function getReservedSeats(): array
    {
        $this->createTable();
        $reserved = [];

        $result = $this->conn->query("SELECT seat_row, seat_col FROM seat_reservations");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $reserved[] = [
                    'row' => (int)$row['seat_row'],
                    'col' => (int)$row['seat_col'],
                ];
            }
            $result->close();
        }

        return $reserved;
    }
}
