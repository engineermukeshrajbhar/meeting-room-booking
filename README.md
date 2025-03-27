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
   git clone https://github.com/your-repo/meeting-room-booking.git
   cd meeting-room-booking
Install PHP dependencies:

bash
Copy
composer install
Install JavaScript dependencies:

bash
Copy
npm install
Create a copy of the environment file:

bash
Copy
cp .env.example .env
Generate application key:

bash
Copy
php artisan key:generate
Configure your database in .env file:

env
Copy
DB_DATABASE=meeting_room
DB_USERNAME=root
DB_PASSWORD=
Run migrations and seeders:

bash
Copy
php artisan migrate --seed
Compile frontend assets:

bash
Copy
npm run dev
Start the development server:

bash
Copy
php artisan serve
Access the application at http://localhost:8000

Testing
Run PHPUnit tests:

bash
Copy
php artisan test
Features
User authentication with login attempt limitation

Meeting room management

Booking system with availability checks

Subscription plans for increased booking limits

Booking history with filters

Copy

## 7. Git Guidelines

1. Create a new repository on your preferred Git platform
2. Add `dev@webol.co.uk` as a collaborator
3. Follow a feature branch workflow:
   - `main` branch for production-ready code
   - Feature branches for development (`feature/login`, `feature/booking`, etc.)
4. Commit messages should be clear and descriptive
5. Push all code including migrations and seeders

This solution provides a complete foundation for your meeting room booking system with all the requested features implemented. The system includes authentication with login attempt limitation, meeting room management, booking functionality with availability checks, subscription plans, and booking history.