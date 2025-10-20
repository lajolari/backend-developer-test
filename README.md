# 📈 Backend Developer Test – Securities Price Sync API

This repository contains a Laravel-based solution for the **Backend Developer Test**.  
The goal is to simulate syncing stock/security prices from an external API into a local database.

---

## 🧠 Problem Summary

The investment platform needs to keep up-to-date prices for securities.  
Data is stored in three tables:

| Table | Purpose |
|--------|----------|
| `security_types` | Defines the type of security (e.g., mutual funds, ETFs, stocks) |
| `securities` | Contains individual securities and their symbols |
| `security_prices` | Stores the last known price for each security |

An external provider (mocked in this test) offers an API like:
```
GET /securities/prices?type=mutual_funds
```

Example response:
```json
{
  "results": [
    { "symbol": "APPL", "price": 188.97, "last_price_datetime": "2023-10-30T17:31:18-04:00" },
    { "symbol": "TSLA", "price": 244.42, "last_price_datetime": "2023-10-30T17:32:11-04:00" }
  ]
}
```

---

## 🧩 Features Implemented

✅ Database schema with migrations for:
- `security_types`
- `securities`
- `security_prices`

✅ Models for all entities  
✅ Service to mock API response and sync local prices  
✅ REST endpoint to trigger synchronization for a specific `security_type`  
✅ Optional async Job to sync all types  
✅ Feature tests (happy path + validation)

---

## ⚙️ Tech Stack

- **PHP 8.2+**
- **Laravel 10+**
- **SQLite/MySQL**
- **Pest/PHPUnit** for testing
- **Queue worker** (optional for job execution)

---

## 🚀 Setup Instructions

### 1️⃣ Clone and install dependencies
```bash
git clone https://github.com/lajolari/backend-developer-test.git
cd backend-developer-test
composer install
cp .env.example .env
php artisan key:generate
```

### 2️⃣ Run database migrations
```bash
php artisan migrate
```

### 3️⃣ (Optional) Seed some data manually or via Tinker
```bash
php artisan tinker

>>> \App\Models\SecurityType::create(['slug' => 'mutual_funds', 'name' => 'Mutual Funds']);
>>> \App\Models\Security::create(['security_type_id' => 1, 'symbol' => 'APPL']);
```

---

## 🧠 Endpoint Overview

### 🔹 `POST /api/securities/sync`
**Description:**  
Triggers the sync process for a single security type.

**Body example:**
```json
{
  "security_type": "mutual_funds"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Security prices for 'mutual_funds' synced successfully."
}
```

**Error (invalid type):**
```json
{
  "status": "error",
  "message": "The given data was invalid."
}
```

---

## ⚙️ Asynchronous Sync (Bonus)

A background job can sync **all security types** using the queue system.

### Dispatch the job manually:
```bash
php artisan tinker

>>> dispatch(new \App\Jobs\SyncAllSecuritiesJob());
```

### Run the queue worker:
```bash
php artisan queue:work
```

This will process the `SyncAllSecuritiesJob` and update all registered securities in the background.

---

## 🧪 Testing

To run all feature tests:
```bash
php artisan test
```

Expected output:
```
PASS  Tests\Feature\SecurityPriceSyncTest
✓ it syncs prices for a given security type
✓ it returns 422 if security type is invalid
```

---

## 🧱 Suggested Commit Workflow

| Step | Commit message |
|------|----------------|
| 1️⃣ | `Add migrations and Eloquent models for securities schema` |
| 2️⃣ | `Implement SecurityPriceSyncService with mock API response` |
| 3️⃣ | `Add SecurityPriceController with POST /api/securities/sync endpoint` |
| 4️⃣ | `Add SyncAllSecuritiesJob for background syncing` |
| 5️⃣ | `Add feature tests for security price syncing endpoint` |
| 6️⃣ | `Refactor & document code (comments and validation improvements)` |

---

## 💬 Notes

> 🧩 This implementation focuses on demonstrating understanding of Laravel architecture, best practices, service layer separation, validation, and clean REST design.  
> The external API is **mocked**, so no external requests are actually performed.  
> The endpoint is **POST** because it modifies data (syncs prices), aligning with REST conventions.

---

### 👨‍💻 Author
**Leonardo Lama**  
Backend Developer — PHP / Laravel  
📧 leolama18@gmail.com
🌐 https://www.linkedin.com/in/ing-leonardo-lama/
