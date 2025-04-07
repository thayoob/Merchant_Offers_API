# 🛍️ Merchant Offers Management System

A powerful **Laravel-based RESTful API** built for managing merchants, their offers, and vouchers. This system is designed to simplify the creation, distribution, and management of promotional offers across multiple merchants, featuring secure authentication using **Laravel Passport**.

---

##  Features

-  **Secure Authentication** using Laravel Passport (OAuth2)
-  **Merchant Management**: Create, update, view, and delete merchant profiles
-  **Offers Management**: Associate offers with merchants and manage them
-  **Voucher Code System**: Generate, retrieve, and manage vouchers for offers
-  **RESTful API Endpoints**: Easy to integrate with web or mobile frontends

---

##  Project Structure Overview

```
MerchantOffersAPI/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── API/
│   │   ├── Resources/
│   │   └── Requests/
│   ├── Models/
├── database/
│   └── migrations/
├── routes/
│   └── api.php
├── .env
├── composer.json
└── README.md
```

---

##  Requirements

Before running this project, make sure you have the following installed:

- PHP >= 8.0
- Composer
- Laravel >= 9.x
- MySQL

---

##  Installation & Setup

Follow these steps to run the project locally:

### 1. Clone the Repository

```bash
git clone https://github.com/thayoob/merchant-offers-api.git
cd merchant-offers-api
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Configure Environment

```bash
cp .env.example .env
```

Update your `.env` file with database and application settings.

### 4. Generate App Key

```bash
php artisan key:generate
```

### 5. Run Database Migrations

```bash
php artisan migrate
```

### 6. Install Laravel Passport

```bash
php artisan passport:install
```

This command generates encryption keys for API token management.

### 7. Serve the Application

```bash
php artisan serve
```

API will be available at `http://localhost:8000`.

---

##  API Endpoints Overview

Here’s a quick summary of the available endpoints:

###  Authentication

| Method | Endpoint         | Description       |
|--------|------------------|-------------------|
| POST   | /api/register    | User Registration |
| POST   | /api/login       | User Login        |
| POST   | /api/logout      | User Logout       |
| GET    | /api/verify-token| Verify Token      |

---

###  Merchants

| Method | Endpoint                  | Description             |
|--------|---------------------------|-------------------------|
| GET    | /api/merchants            | List all merchants      |
| POST   | /api/merchants            | Create a new merchant   |
| GET    | /api/merchants/{id}       | Show merchant details   |
| PUT    | /api/merchants/{id}       | Update merchant         |
| DELETE | /api/merchants/{id}       | Delete merchant         |

---

###  Offers

| Method | Endpoint              | Description           |
|--------|-----------------------|-----------------------|
| GET    | /api/offers           | List all offers       |
| POST   | /api/offers           | Create a new offer    |
| GET    | /api/offers/{id}      | Show offer details    |
| PUT    | /api/offers/{id}      | Update offer          |
| DELETE | /api/offers/{id}      | Delete offer          |

---

###  Voucher Codes

| Method | Endpoint                              | Description                    |
|--------|---------------------------------------|--------------------------------|
| GET    | /api/voucher-codes                    | List all vouchers              |
| POST   | /api/voucher-codes                    | Create a new voucher           |
| GET    | /api/voucher-codes/{id}               | Show voucher details           |
| PUT    | /api/voucher-codes/{id}               | Update voucher                 |
| DELETE | /api/voucher-codes/{id}               | Delete voucher                 |
| GET    | /api/voucher-codes/offer/{offerId}    | Get vouchers for a specific offer |

>  All endpoints (except register and login) are protected and require **Bearer Token** (Passport) authentication.



## Thank You

---
