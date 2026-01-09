# Financial Data Sync System 🚀

A robust, scalable backend service built with **Laravel** to synchronize financial market data (Crypto & Forex) from real external APIs.

> **Project Goal:** Demonstrate Senior Backend Engineer capabilities including Clean Architecture, Design Patterns, Dockerized environments, and advanced Testing strategies.

## 🏗 Architecture & Design Patterns

This project implements a highly decoupled architecture using the **Adapter Pattern** to ensure scalability and maintainability:

* **Adapters (Integration Layer):** Independent logic for each provider (`CoinGeckoAdapter`, `FrankfurterAdapter`) implementing a common `MarketDataInterface` contract.
* **DTOs (Data Transfer Objects):** All external data is normalized into `SecurityPriceData` objects, shielding the core system from changes in external API response formats.
* **Factory Pattern:** A `MarketDataProviderFactory` is used to dynamically resolve the correct adapter based on the asset type (Crypto vs. Forex).
* **Resilience:** Implements database transactions and robust exception handling to ensure data integrity during bulk synchronization processes.

## 🐳 Docker Setup (Zero Config)

The project is fully containerized to ensure a reproducible environment across different systems.

```bash
# 1. Start the containers
docker-compose up -d --build

# 2. Install PHP dependencies
docker-compose exec app composer install

# 3. Run Database Migrations
docker-compose exec app php artisan migrate

# 4. Trigger the Sync Job manually (Optional)
docker-compose exec app php artisan tinker
# Inside tinker, run:
# dispatch(new \App\Jobs\SyncAllSecuritiesJob());
```

## ✅ Testing

The project includes a comprehensive suite of Unit and Feature tests. It uses "Mocking" for external HTTP calls to ensure CI/CD reliability and fast execution.

```bash
# Run the test suite inside the container
docker-compose exec app php artisan test
```

* **Unit Tests:** Verify business logic within `SecurityPriceSyncService` using Mockery to isolate the service from its dependencies.
* **Integration Tests:** Ensure that API adapters (e.g., `CoinGeckoAdapter`) correctly normalize incoming data using Laravel's `Http::fake` feature.

## ⚙️ Tech Stack

* **PHP 8.2**
* **Laravel 11**
* **MySQL 8**
* **Docker**
* **PHPUnit**
* **Mockery**

### 👨‍💻 Author
**Leonardo Lama**  
Fullstack Developer — PHP / Laravel / AWS  
📧 leolama18@gmail.com  
🌐 https://www.linkedin.com/in/ing-leonardo-lama/