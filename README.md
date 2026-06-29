# Weather API Integration Service

A lightweight, clean Laravel backend application that integrates with the OpenWeatherMap API to retrieve real-time and cached city weather metrics. Built to showcase clean separation of concerns, modern PHP 8 features, and structured error boundaries.

---

## 🛠️ Architecture & Approach

This project strictly adheres to a **Service Pattern** to ensure the controller has zero knowledge of the network operations or data mapping formatting rules:

1. **`WeatherController`**: Manages pure incoming HTTP layers, route endpoint targets, and formats semantic JSON exception boundaries cleanly.
2. **`WeatherService`**: Consumes configuration details, communicates directly with OpenWeatherMap using Laravel's fluent `Http` client, transforms payload values safely using PHP 8 safe operators, and coordinates the 10-minute caching layer.
3. **Data Caching Strategy**: Utilizes automated `Cache::has` evaluation constraints. If a city request hits the cache bucket, the data tracking property dynamically updates its identifier to `"source": "cache"` before passing data down the stack.

---

## 🚀 Installation & System Configuration

### Prerequisites
- PHP >= 8.5 (Optimized via Laravel Herd)
- Composer

### Setup Steps
1. **Clone the Repository:**
   ```bash
   git clone https://github.com/mcalvinjavier/weather-api
   cd weather-api
Install Composer Dependencies:

    bash
    composer install
    Establish Environment Configs:

    bash
    cp .env.example .env
    
Open .env and configure your OpenWeatherMap credentials without surrounding space properties:

    Code snippet
        OPENWEATHER_API_KEY=01225945f1ddd4ed1094a11a6d1e343a
        OPENWEATHER_BASE_URL=https://api.openweathermap.org/data/2.5
Generate App Security Key:

    Bash
    php artisan key:generate


🔗 Available Endpoints
1. Fresh Live Weather Data
URL: GET /api/weather/{city}

Behavior: Fetches real-time API details from the live server.

Example: http://weather-exam.test/api/weather/manila

2. Cached Weather Data
URL: GET /api/weather/{city}/cached

Behavior: Checks cache blocks first; serves from storage mapping "source": "cache" if found. Stays valid for exactly 10 minutes.

Example: http://weather-api.test/api/weather/manila/cached

🧪 Automated Testing
The feature tests leverage Laravel's standard Http::fake validation layers to fully execute assertions without exhausting open external API tokens or running database migrations.

To run the full test suite using your current runtime configurations, execute:

Bash
php artisan test
(If your local system terminal relies on isolated environment paths, use: C:\Users\NAME\.config\herd\bin\php artisan test)
