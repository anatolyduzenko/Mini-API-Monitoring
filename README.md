# API Monitoring Laravel App

## Overview
This API Monitoring Laravel App helps track API performance, availability, and response times. It provides insights into API health, error rates, and latency issues, ensuring better system reliability.

## Features
- **API Health Checks**: Monitors API uptime and response times.
- **Error Logging**: Captures and logs API failures.
- **Notifications**: Sends alerts when API downtime is detected.
- **Performance Metrics**: Tracks response times and status codes.
- **Authentication**: Secure access to monitoring reports.

## Requirements
- PHP 8.2+
- Laravel 12
- MySQL/PostgreSQL
- Redis (for queue and caching)
- Supervisor (for background job management)

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/anatolyduzenko/api-monitoring-laravel.git
   cd api-monitoring-laravel
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Copy the environment file**
   ```bash
   cp .env.example .env
   ```

4. **Set up the database** in `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=api_monitoring
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run migrations**
   ```bash
   php artisan migrate --seed
   ```

6. **Generate application key**
   ```bash
   php artisan key:generate
   ```

7. **Set up scheduler and queue workers**
   ```bash
   php artisan queue:work
   ```
   Add the following line to your server’s crontab to run scheduled jobs:
   ```bash
   * * * * * php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1
   ```

8. **Run the application**
   ```bash
   php artisan serve
   ```

## Routes

### Authentication
- `POST /login` - Authenticate user
- `POST /logout` - Logout user

### API Endpoints
- `GET /endpoints` - Get monitored APIs endpoints 
- `POST /endpoints` - Add a new endpoint to monitor
- `DELETE /endpoints/{id}` - Remove an endpoint from monitoring

### Dashboard
- `GET /dashboard` - Dashboard to get some statistics of endpoints

## Notifications
- Email notifications for API failures.
- Telegram integration for real-time alerts. (future)
- Webhook support to send notifications to external services.

## Testing
Run tests using PHPUnit:
```bash
php artisan test
```

## Deployment
- Use **Supervisor** to manage queue workers.
- Set up **Nginx/Apache** for production.
- Configure **Redis** for better caching and performance.

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## License
This project is licensed under the MIT License.
