🏋️ FitPulse — Gym Management System

FitPulse is a gym management system built with Laravel to manage members, instructors, and gym operations in a centralized platform.

This project was developed in an academic context, focusing on database modeling, backend development, system architecture, and collaborative version control.

📚 Project Overview

FitPulse centralizes the administrative, financial, and operational processes of a gym into a single platform.

With this system it is possible to manage members, monitor attendance, control memberships, and organize training and evaluation data efficiently.

🎯 Project Objectives

The system aims to:

• Manage gym members and staff roles
• Control memberships and monthly payments
• Monitor student attendance
• Manage personalized workout plans
• Register physical evaluations
• Control internal store sales
• Maintain audit logs for system security

🛠 Technologies
Back-end

PHP 8+

Laravel 12

MySQL

Eloquent ORM

Front-end

Blade

CSS

JavaScript

Vite

Tools

Git

GitHub

Trello

Figma

DBDiagram.io

Draw.io / Diagrams.net

📂 Project Structure
fitpulse-api
│
├── docs/           # Project documentation (DER, diagrams, PDFs)
│
└── src/            # Laravel application
    ├── app/
    ├── routes/
    ├── resources/
    ├── database/
    ├── public/
    └── ...
⚙️ Requirements

Before running the project, make sure you have installed:

PHP 8+

Composer

MySQL

Node.js & NPM

Git

Recommended environments:

XAMPP

Laragon

🚀 Installation & Setup
1️⃣ Clone the repository
git clone https://github.com/gustavomrq/fitpulse-api.git
2️⃣ Enter the project directory
cd fitpulse-api/src
3️⃣ Install PHP dependencies
composer install
4️⃣ Install JavaScript dependencies
npm install
5️⃣ Create the environment file

Copy the example file:

cp .env.example .env
6️⃣ Configure the database

Open .env and update:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fitpulse
DB_USERNAME=root
DB_PASSWORD=
7️⃣ Generate the application key
php artisan key:generate
8️⃣ Run the migrations
php artisan migrate
▶️ Running the Project

Start the Laravel server:

php artisan serve

Start the Vite development server:

npm run dev
🌐 Access the Application

Open in your browser:

http://127.0.0.1:8000
🔐 Authentication

The project uses Laravel Breeze for authentication.

Features include:

• User registration
• Login
• Logout
• Route protection
• Dashboard access

Main routes:

/login
/register
/dashboard
🧩 System Modules

User Management (Managers, Receptionists, Instructors, Students)

Membership & Monthly Billing

Workout Management

Physical Evaluations

Attendance Monitoring

Internal Store Sales

Audit Logs & Security Monitoring

👥 Team
Diego Santos - Manager

Gustavo Marques - Back-end Developer

Lívia Karoliny - Front-end Developer

Gilles Gael - Tester

Guilherme Yuri - Tester

🎓 Academic Context

This project was developed as part of an academic assignment focusing on:

Database modeling (conceptual, logical, and physical)

RESTful API development

Software architecture

Version control collaboration

Team-based project management

Tasks were organized using Trello, and the source code is maintained using Git and
