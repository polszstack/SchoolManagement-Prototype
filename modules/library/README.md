# BCP School Library Management System
**Bestlink College of the Philippines — Professional Edition**

---

## 📁 Project Structure

```
library_system/
├── index.php              ← Main entry point (requires login)
├── login.php              ← BCP-branded login page
├── api.php                ← REST API router
├── database.sql           ← Full database schema + sample data
│
├── auth/
│   ├── session.php        ← Session guard
│   └── logout.php         ← Logout handler
│
├── includes/              ← PHP models & config
│   ├── config.php
│   ├── Database.php
│   ├── Response.php
│   ├── Settings.php
│   ├── Book.php
│   ├── Borrower.php
│   ├── Transaction.php
│   ├── ActivityLog.php
│   ├── AIBookRecommender.php
│   └── AILateReturnDetector.php
│
├── api/                   ← API controllers
│   ├── BooksApiController.php
│   ├── BorrowersApiController.php
│   ├── TransactionsApiController.php
│   ├── DashboardApiController.php
│   ├── SettingsApiController.php
│   └── AIApiController.php
│
├── views/
│   ├── layout.php         ← Master layout (BCP sidebar + header)
│   └── partials/          ← Tab content partials
│       ├── dashboard.php
│       ├── books.php
│       ├── gallery.php
│       ├── members.php
│       ├── borrow.php
│       ├── transactions.php
│       ├── reports.php
│       ├── settings.php
│       └── ai_features.php
│
├── assets/
│   ├── css/
│   │   ├── main.css        ← Layout, sidebar, header (BCP themed)
│   │   ├── components.css  ← Buttons, forms, tables, cards
│   │   ├── ai_features.css ← AI panel styles
│   │   └── dropdown.css    ← Bell & user dropdown styles
│   ├── js/
│   │   ├── ApiService.js
│   │   ├── UIService.js
│   │   ├── app.js
│   │   ├── DashboardController.js
│   │   ├── BooksController.js
│   │   ├── GalleryController.js
│   │   ├── MembersController.js
│   │   ├── BorrowController.js
│   │   ├── controllers.js
│   │   └── AIController.js
│   └── images/
│       ├── bcp-logo.png
│       └── bg.jpg
│
└── uploads/
    └── covers/            ← Book cover uploads (auto-created)
```

---

## ⚙️ Setup Instructions

### 1. Database
1. Open **phpMyAdmin** or MySQL CLI
2. Import `database.sql`
3. This creates the `school_library` database with all tables + sample data

### 2. Configuration
Edit `includes/config.php`:
```php
define('DB_HOST', 'localhost');    // your DB host
define('DB_USER', 'root');         // your DB username
define('DB_PASS', '');             // your DB password
define('DB_NAME', 'school_library');
```

### 3. Web Server
- Place the folder inside `htdocs/` (XAMPP) or `www/` (WAMP)
- Access: `http://localhost/library_system/`

### 4. Login
Default credentials:
- **Username:** `admin`
- **Password:** `admin123`

> To change credentials, edit the login check in `login.php` or connect it to your database users table.

---

## ✨ Features

| Feature | Description |
|---------|-------------|
| 🔐 Login Page | BCP-branded with bg.jpg, page loader, logo |
| 📚 Book Catalog | Add, edit, delete, search books with cover images |
| 🖼️ Book Gallery | Visual grid view with ISBN cover auto-fetch |
| 👥 Members | Student/Teacher/Staff management |
| 🤝 Borrow/Return | Issue and return with fine calculation |
| 📊 Transactions | Full history with status & fine tracking |
| 📈 Reports | Genre breakdown, overdue list, top borrowed |
| ⚙️ Settings | Library name, fine rate, borrow limits |
| 🤖 AI Insights | Late return risk scoring + book recommendations |
| 🍔 Hamburger Menu | Collapsible sidebar (BCP style) |
| 🔔 Notifications | Bell dropdown in sidebar |
| 👤 User Dropdown | Profile & logout from sidebar |
| ⏰ Realtime Clock | Live clock in header |
| 📱 Responsive | Mobile-friendly layout |
| ⏳ Page Loader | BCP logo spinner on page load |

---

## 🎨 Design

- **Colors:** BCP Purple (`#200082`) + blue accent
- **Sidebar:** Dark purple BCP-branded with user avatar
- **Header:** White with hamburger, realtime clock, quick actions
- **Font:** Inter (Google Fonts)
- **School:** Bestlink College of the Philippines (Est. 2002)
