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



