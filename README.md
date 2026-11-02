# Ticketing - Lightweight Community Event & Seat Reservation System

Ticketing is a lightweight, web-based platform designed specifically for community events. Built completely without heavy frameworks, it is fully portable and can run directly on a laptop. Powered online effortlessly using ngrok, it is easy to set up anywhere. Designed for event ushers and community committees, it features a live display list of available movies/events right on TV screens! 🎬📺✨

Feel free to use it, study it, or contribute to making it even better! 🎉

# Key Features

💺 Interactive Seat Map: Visually select seats in real-time with responsive grid layouts and status indicators (Available vs. Taken).
📺 Live TV Display: Designed for event ushers and committees to display "Now Showing" event/movie listings directly on external screens or TV displays.
⚡ Portable & Framework-Free: Pure PHP, HTML/CSS, and JS implementation for maximum performance without heavy framework dependencies.
🌐 Instant Deployment via Ngrok: Run locally on any laptop and expose it safely online using ngrok for hassle-free community setup.
📅 Attendance & Session History: Look up past event attendance and historical seat layouts by date using dynamic AJAX queries.
🔌 JSON API Endpoint: Includes built-in API handlers (/api/attendance.php) for easy attendance integration.

# Prerequisites
Before running this project locally, ensure you have the following tools installed:
PHP (Version 8.0+ recommended)
MySQL or MariaDB
Local server tool (XAMPP, Laragon, or PHP built-in server)
ngrok (Optional, for making your local server accessible online)

# Installation & Setup Guide (Step-by-Step)
Follow these simple steps to get Ticketing up and running on your local machine:

1. Clone the Repository
Open your terminal or command prompt and run:
git clone https://github.com/username-anda/ticketing.git
cd ticketing

2. Configure Database
Create a new MySQL database named kompa (or change the database name in public/db.php).
Import your database tables/schema into MySQL.

3. Verify Connection Settings
Check public/db.php to ensure local database credentials match your setup:
$pdo = new PDO(
    "mysql:host=localhost;dbname=kompa;charset=utf8",
    "root",
    "",
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

4. Run the Local Server 🚀
Launch the built-in PHP development server targeting the public directory:
php -S localhost:8000 -t public

5. Expose Online with Ngrok (Optional)
To share your local instance online with event committees or ushers:
ngrok http 8000

Use the generated public https://...ngrok-free.app URL!

# Want to Contribute?
Contributions are very welcome and appreciated! If you have ideas for new features, find a bug, or want to improve the codebase, feel free to join in:
Fork this repository.
Create your feature branch (git checkout -b feature/awesome-feature).
Commit your changes (git commit -m 'Add awesome feature').
Push to the branch (git push origin feature/awesome-feature).
Open a Pull Request and let's build together!

# License
This project is open-source and available under the MIT License. Use it, modify it, and share it freely!