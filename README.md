🏋️ FitPulse – Gym Management System

FitPulse is a gym management platform developed using Laravel to manage students, instructors, memberships, and gym operations in a centralized system.

The project was created in an academic context, focusing on database modeling, backend development, system architecture, and version control collaboration.

📖 Project Overview

FitPulse is designed to centralize the administrative, financial, and operational processes of a fitness center into a single platform.

The system allows gym managers to manage students, monitor attendance, control memberships, and organize workout and evaluation data efficiently.

🎯 Project Objectives

The system aims to:

Manage gym members and staff roles

Control memberships and monthly payments

Monitor student attendance

Manage personalized workout plans

Register physical evaluations

Control internal store sales

Maintain audit logs for system security

🛠 Technologies & Tools
💻 Back-end

PHP 8+

Laravel 12

MySQL

Eloquent ORM

🎨 Front-end

Blade

CSS

JavaScript

Vite

🗄 Database Modeling

DBDiagram.io

Draw.io / Diagrams.net

🔄 Version Control

Git

GitHub

📋 Project Management

Trello

📂 Project Structure
fitpulse-api
│
├── docs/          # Project documentation (DER, diagrams, PDFs)
│
└── src/           # Laravel application source code
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

Git

Node.js & NPM

Recommended development environments:

XAMPP

Laragon

🚀 How to Run the Project
1️⃣ Clone the repository
git clone https://github.com/gustavomrq/fitpulse-api.git
2️⃣ Enter the project folder
cd fitpulse-api/src
3️⃣ Install PHP dependencies
composer install
4️⃣ Install JavaScript dependencies
npm install
5️⃣ Create the environment file

Copy the example file:

cp .env.example .env
6️⃣ Configure the database

Open the .env file and configure:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fitpulse
DB_USERNAME=root
DB_PASSWORD=
7️⃣ Generate the application key
php artisan key:generate
8️⃣ Run database migrations
php artisan migrate
9️⃣ Run the development servers

Start the Laravel server:

php artisan serve

Start the Vite development server:

npm run dev
🌐 Access the system

After running the servers, open:

http://127.0.0.1:8000
🔐 Authentication

The system uses Laravel Breeze for authentication.

Features include:

User registration

Login

Logout

Route protection

Dashboard access

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

👥 Team Members
Diego Santos - Manager

Gustavo Marques – Back-end Developer 

Lívia Karoliny – Front-end Developer

Gilles Gael - Tester

Guilherme Yuri - Tester

📌 Academic Context

This project was developed as part of an academic assignment focused on:

Database modeling (conceptual, logical, and physical)

RESTful API development

Software architecture

Version control collaboration

Team-based project management

Tasks were organized using Trello, and the source code is maintained using Git and GitHub.

📊 Languages

PHP

Blade

CSS

JavaScript
