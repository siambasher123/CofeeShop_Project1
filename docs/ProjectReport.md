# Project | Coffee Shop | Information System Design Lab

## Project Objectives

- To implement a cohesive web platform that supports customer ordering, reservations, and service inquiries for a café environment.
- To provide administrators with streamlined interfaces for catalog maintenance, order fulfillment, and financial oversight.
- To safeguard user interactions through authenticated sessions, validated inputs, and structured database access.
- To document the system using maintainable diagrams and modular source code for academic replication.

## Introduction

This Information Systems Lab project presents a full-stack coffee shop portal developed with PHP, MySQL, and Bootstrap. The platform simulates the daily workflow of a café by integrating customer interactions, seat reservations, payment handling, and administrative operations into a single online solution. It is designed as a teaching artifact that demonstrates how web technologies can be orchestrated for a service-oriented organization.

## Project Details

**System Architecture**  
The application adopts a modular PHP layout with focused scripts for authentication (`login.php`, `signup.php`), catalog delivery (`menu.php`), checkout (`cart.php` and `order_tracking.php`), and management dashboards (`admin_dashboard.php`). Shared configuration in `config.php` standardizes session handling and database connectivity, while helper classes such as `SeatReservation.php` encapsulate reservation rules and prevent double booking.

Figure 1. Level 0 Data Flow Diagram summarizing the Coffee Shop Information System. (Insert diagram generated from `diagrams/dfd_level0.puml`.)

**Functional Modules**  
Customer-facing capabilities include browsing the menu, managing carts, completing orders, and reserving seats, supported by contextual pages like `contact.php` and `about.php`. Administrative tools cover product maintenance (`add_products.php`), discount management (`give_discount.php`), order oversight (`order_list.php`, `transaction_history.php`), and message tracking (`contact_list.php`). These modules share session-aware navigation to distinguish between customer and administrator workflows.

Figure 2. Level 1 Data Flow Diagram illustrating processes 1.1–1.5. (Insert diagram generated from `diagrams/dfd_level1.puml`.)

**Data Management**  
The database script `mycoffeshop.sql` provisions normalized tables for users, products, cart contents, orders, payments, and seat reservations. CRUD operations rely on mysqli prepared statements to sanitize inputs and maintain referential integrity. Aggregated records such as transaction history and payment logs provide traceability for financial audits and lab assessments.

Figure 3. Use Case Diagram highlighting customer and administrator roles. (Insert diagram generated from `diagrams/use_case_diagram.puml`.)

**Process Visualization and Scheduling**  
Supporting diagrams capture behavioral views of the system: `sequence_diagram.puml` models a typical order, `activity_diagram.puml` depicts end-to-end fulfillment, `class_diagram.puml` outlines entity relationships, and `gantt_chart.puml` presents a project timeline. These artifacts reinforce the lab narrative and guide future enhancements.

Figure 4. Sequence Diagram for the order placement workflow. (Insert diagram generated from `diagrams/sequence_diagram.puml`.)

## Discussion

Collaboration was organized through Git and GitHub, where we cycled feature branches through pull requests, traced issues in the commit history (e.g., `704a2b1` for seat reservation fixes, `f665312` for hardened uploads), and merged shared work such as the `shifat_067` branch. Project planning ran on Jira with a Scrum board: epics captured customer experience, administration, and documentation tracks, while stories like “Maintain Architecture Diagrams” and “Publish Project Gantt” guided sprint increments and reviews. Database design began with entity brainstorming on the Jira backlog, moved into iterative ER sketches, and resulted in the normalized schema released in `mycoffeshop.sql`, with refinements captured alongside sequence and class diagrams. PlantUML diagrams (Level 0/1 DFDs, use case, sequence, activity, class, Gantt) became the communication backbone during stand-ups and demos, ensuring that architecture decisions stayed visible. Our workflow included frequent checkpoints where we surfaced missteps—such as double-booked seats or missing validation—logged them as defects, and resolved them through pair debugging and follow-up commits, reinforcing a culture of learning from mistakes.

## Conclusion

Delivering this project taught us how to blend agile planning, disciplined version control, and full-stack implementation into a unified lab artifact. The experience strengthens our readiness as computer science and engineering students to tackle future industry and academic projects that demand collaborative tooling, resilient data models, and visually documented system designs.

## References

- [PHP Manual](https://www.php.net/manual/en/)
- [Bootstrap Documentation](https://getbootstrap.com/docs/5.0/getting-started/introduction/)
- [PlantUML Reference Guide](https://plantuml.com/)
