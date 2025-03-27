# Meeting Room Booking System

A Laravel + VueJS application for booking meeting rooms with subscription plans.

## Requirements

- PHP 8.0+
- Composer
- Node.js 14+
- MySQL 5.7+

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/engineermukeshrajbhar/meeting-room-booking.git
   cd meeting-room-booking
Install PHP dependencies:
```bash
composer install
```
```bash

composer install
```
Install JavaScript dependencies:
```bash
npm install
```
Create a copy of the environment file:

```bash

cp .env.example .env
```
Generate application key:

```bash

php artisan key:generate
```
Configure your database in .env file:

env

DB_DATABASE=meeting_room
DB_USERNAME=root
DB_PASSWORD=
Run migrations and seeders:

```bash
Copy
php artisan migrate --seed
```
Compile frontend assets:

```bash

npm run dev
```
Start the development server:

```bash

php artisan serve
```
Access the application at http://localhost:8000


Features
User authentication with login attempt limitation

Meeting room management

Booking system with availability checks

Subscription plans for increased booking limits

Booking history with filters




This solution provides a complete foundation for your meeting room booking system with all the requested features implemented. The system includes authentication with login attempt limitation, meeting room management, booking functionality with availability checks, subscription plans, and booking history.

Register

<img width="1287" alt="Screenshot 2025-03-27 at 10 11 34 AM" src="https://github.com/user-attachments/assets/d3f68a76-eb29-433c-b08a-068aeca5ac62" />

Login

<img width="1366" alt="Screenshot 2025-03-27 at 10 11 24 AM" src="https://github.com/user-attachments/assets/7adbf857-8620-4029-a995-5a349323fd93" />

Dashboard

<img width="1450" alt="Screenshot 2025-03-27 at 9 36 14 AM" src="https://github.com/user-attachments/assets/58f5b8b4-7ce3-451a-aa67-c3908cf14545" />

Meeting book form and availability

<img width="1461" alt="Screenshot 2025-03-27 at 9 36 36 AM" src="https://github.com/user-attachments/assets/a377d76e-5024-417e-a68e-c940be30db00" />


Meeting List

<img width="1434" alt="Screenshot 2025-03-27 at 9 36 52 AM" src="https://github.com/user-attachments/assets/3dfdc2db-d899-40b1-85c0-6f11c1702784" />


