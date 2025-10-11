# Project | Coffee Shop | Information System Design Lab

## Project Objectives

- To implement a cohesive web platform that supports customer ordering, reservations, and service inquiries for a café environment.
- To provide administrators with streamlined interfaces for catalog maintenance, order fulfillment, and financial oversight.
- To safeguard user interactions through authenticated sessions, validated inputs, and structured database access.
- To document the system using maintainable diagrams and modular source code for academic replication.

## Introduction

This Information Systems Lab project presents a full-stack coffee shop portal developed with PHP, MySQL, and Bootstrap. The platform simulates the daily workflow of a café by integrating customer interactions, seat reservations, payment handling, and administrative operations into a single online solution. It is designed as a teaching artifact that demonstrates how web technologies can be orchestrated for a service-oriented organization.

## Details

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

The project demonstrates how a lightweight technology stack can replicate the service channels of a modern café without introducing framework overhead. Modular PHP pages map directly to business responsibilities, making the architecture approachable for students who are still mastering web development. Bootstrap styling, vanilla JavaScript interactions, and prepared SQL statements collectively deliver a balanced focus on usability and security. Encapsulated components such as the seat reservation helper showcase practical object-oriented techniques within a procedural codebase. Diagram placeholders ensure that the written report can be paired with visual documentation for presentations or academic submissions.

## Conclusion

The Integrated Coffee Shop Information System consolidates customer experiences and administrative controls in a maintainable academic project. Its architecture, code structure, and diagram set provide a transferable template for future information systems lab cohorts.

## References

- [PHP Manual](https://www.php.net/manual/en/)
- [Bootstrap Documentation](https://getbootstrap.com/docs/5.0/getting-started/introduction/)
- [PlantUML Reference Guide](https://plantuml.com/)
