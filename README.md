# AutoParts

![status](https://img.shields.io/badge/status-production-brightgreen)
![version](https://img.shields.io/badge/version-1.0.0-informational)
![node](https://img.shields.io/badge/node-%3E%3D18-339933)
![postgres](https://img.shields.io/badge/postgres-%3E%3D13-4169E1)
![license](https://img.shields.io/badge/license-not%20specified-lightgrey)

**AutoParts** — повноцінний проєкт інтернет‑магазину автозапчастин. Складається з бекенду (**/api**, Node.js + PostgreSQL). Надає повний набір можливостей: каталог товарів із фільтрами та сумісністю за авто (марка/модель/покоління/модифікація), кошик, замовлення, улюблені, адреси, довідники методів доставки/оплати, тікет‑саппорт, а також службові маршрути для адміністративних задач. Оптимізовано для роботи за reverse‑proxy і масштабування.

![Preview](docs/img.png)

---

## Зміст
- [Встановлення](#встановлення)
- [Використання](#використання)
- [Структура репозиторію](#структура-репозиторію)
- [Технології](#технології)
- [Налаштування середовища](#налаштування-середовища)
- [Деплой](#деплой)
- [Внесок у проєкт](#внесок-у-проєкт)
- [Ліцензія](#ліцензія)

---

## Встановлення

> Вимоги: Node.js **18+**, PostgreSQL **13+**, доступ до інстансу БД.

```bash
git clone <URL вашого репозиторію>
cd AutoParts

# 1) API: змінні середовища
cd api
cp .env .env.local    # або створіть .env і заповніть значення

# 2) Встановлення залежностей
npm ci   # або npm i

# 3) Запуск (production-mode локально)
NODE_ENV=production npm start
# => http://localhost:3000
```

---

## Використання

### Авторизація
Токен сесії (UUID) передається в заголовку:
- `Authorization: Bearer <token>` або
- `X-Auth-Token: <token>`

### Базовий флоу
```bash
# Реєстрація
curl -X POST http://localhost:3000/api/auth/register   -H "Content-Type: application/json"   -d '{"name":"User","email":"user@example.com","password":"secret"}'

# Логін → отримати токен
TOKEN=$(curl -s -X POST http://localhost:3000/api/auth/login   -H "Content-Type: application/json"   -d '{"email":"user@example.com","password":"secret"}' | jq -r .token)

# Перегляд каталогу
curl "http://localhost:3000/api/products?page=1&perPage=12&sort=popular"

# Додати у вибране
curl -X POST http://localhost:3000/api/wishlist/items   -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json"   -d '{"product_id":123}'

# Створити замовлення
curl -X POST http://localhost:3000/api/orders   -H "Authorization: Bearer $TOKEN" -H "Content-Type: application/json"   -d '{"cart_id":1,"delivery_method_id":1,"payment_method_id":1,"address":"вул. Приклад, 1"}'
```

> Health‑check: `GET /` → `{ "success": true, "message": "AutoParts API is running" }`.

---

## Структура репозиторію

```
AutoParts/
├─ api/                # бекенд (Node.js + Express + PostgreSQL)
│  ├─ server.js
│  ├─ db/
│  ├─ middlewares/
│  ├─ models/
│  └─ routes/
└─ docs/
   └─ img.png          # прев’ю/скріншоти
```

**api/** (урізане дерево):
```
./
  package-lock.json
  package.json
  server.js
  db/
    index.js
  middlewares/
    auth.js
  models/
    addressModel.js
    adminModel.js
    brandModel.js
    carsModel.js
    cartModel.js
    categoryModel.js
    couponsModel.js
    methodModel.js
    orderModel.js
    productModel.js
    supportModel.js
    userModel.js
    wishlistModel.js
  routes/
    addresses.js
    admin.js
    auth.js
    brands.js
    cars.js
    carts.js
    categories.js
    methods.js
    orders.js
    products.js
    support.js
    users.js
    wishlist.js
```

---

## Технології

- **Runtime:** Node.js (ESM), Express
- **Database:** PostgreSQL (`pg` connection pool)
- **Security:** bcrypt (хешування паролів), сесійні токени (UUID)
- **Infra:** dotenv, CORS
- **Архітектура:** `routes → models → db` (SQL‑запити зосереджено у моделях)
- **Залежності:** bcrypt, cors, dotenv, express, pg, uuid

---

## Налаштування середовища

**api/.env (приклад):**
```env
PORT=3000
PGHOST=localhost
PGUSER=your_user
PGPASSWORD=your_password
PGDATABASE=autoparts-db
PGPORT=5432
```

> Міграцій у репозиторії немає — створіть схему БД самостійно за прикладами запитів у `api/models/*`.

### npm‑скрипти
- `start` — `node server.js`

---

## Деплой

### PM2
```bash
npm i -g pm2
cd api
pm2 start server.js --name autoparts-api --update-env --time
pm2 save && pm2 startup
```

### Docker (приклад)
```yaml
version: "3.8"
services:
  db:
    image: postgres:16
    restart: unless-stopped
    environment:
      POSTGRES_DB: autoparts-db
      POSTGRES_USER: user
      POSTGRES_PASSWORD: password
    ports: ["5432:5432"]
    volumes: [db_data:/var/lib/postgresql/data]

  api:
    image: node:20-alpine
    working_dir: /app
    volumes: ["./api:/app"]
    environment:
      NODE_ENV: production
      PORT: "3000"
      PGHOST: db
      PGUSER: user
      PGPASSWORD: password
      PGDATABASE: autoparts-db
      PGPORT: "5432"
    command: ["node","server.js"]
    ports: ["3000:3000"]
    depends_on: [db]
volumes:
  db_data:
```

> Рекомендовано розміщувати за reverse‑proxy (Nginx) з TLS та health‑check.

---

## Внесок у проєкт

Вітаються pull‑request’и:
1. Форк → гілка `feature/<short-name>`.
2. Дотримуйтеся контрактів API `{ success, data|error }` та стилю коду.
3. Додайте базові тести (за наявності).
4. Оформіть короткий опис у PR.

> Якщо внесок закритий — замініть цей блок повідомленням про заборону.

---

## Ліцензія

Ліцензію не вказано. Додайте файл `LICENSE` або визначте умови використання.
