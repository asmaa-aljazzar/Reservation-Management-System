# Study Nook

Study Nook is a PHP and MySQL application for reserving library study rooms. Users can find rooms and upcoming slots, request reservations, and manage their reservation history. Admins can manage rooms and slots, review reservations, and view dashboard summaries and charts.

## Project flow

The system flow and planning diagram are on the [Miro board](https://miro.com/app/board/uXjVEWpdB7w=/?share_link_id=790097679790).

## Requirements

- PHP with PDO MySQL enabled
- MySQL or MariaDB
- Apache (XAMPP is suitable)

## Local setup

1. Place the project in your web server's document root, such as `C:\xampp\htdocs\projects\library-study-room-reservation`.
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Import [`database/schema.sql`](database/schema.sql) using phpMyAdmin or the MySQL command line. The script creates and selects the `library_study_room_reservation` database.
4. Set the `$dsn`, `$dbUser`, and `$dbPassword` values in `config/config.php` for your local MySQL connection.
5. Open `http://localhost/projects/library-study-room-reservation/` in a browser.

The login page includes demo account shortcuts when the corresponding demo accounts exist in the database. Do not use demo credentials for a public deployment.

## Main features

- User registration and login, with role-based access
- Browse active rooms and available upcoming slots
- Request a slot reservation and view or cancel eligible reservations
- Admin room and slot management, including active/inactive status
- Admin reservation review: confirm, reject, or cancel requests
- Dashboard summary cards, booking charts, and reservation lists

## Project layout

- `pages/` — public, user, and admin pages
- `actions/` — POST handlers for forms and status changes
- `includes/` — database access, validation, business logic, and shared layout
- `assets/` — stylesheets, JavaScript, and room images
- `database/schema.sql` — MySQL schema and database constraints
