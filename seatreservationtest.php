<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../src/SeatReservation.php';

class SeatReservationTest extends TestCase {
    private $conn;
    private $reservation;

    protected function setUp(): void {
        // Use in-memory SQLite for testing (no real DB needed)
        $this->conn = new mysqli('localhost', 'root', '', 'test_db'); // adjust if needed
        $this->conn->query("DROP TABLE IF EXISTS seat_reservations");
        $this->reservation = new SeatReservation($this->conn);
        $this->reservation->createTable();
    }