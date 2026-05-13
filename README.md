# JeevanDaan Blood Management System

This is the cleaned full working project folder.

## Folder Structure

```text
jeevandaan_full_working_app/
├── app/
│   ├── config/          # PHP app configuration and database connection
│   ├── controllers/     # Backend controllers
│   ├── core/            # Router and base controller
│   ├── helpers/         # Helper files, if added later
│   ├── models/          # Model files, if added later
│   └── views/           # Existing frontend PHP views
├── database/
│   ├── jeevandaan.sql       # Full fresh database schema
│   └── backend_updates.sql  # Migration for an existing database
├── docs/
│   └── legacy_frontend_reference/ # Old static reference files from the original archive
└── public/
    ├── css/             # Existing CSS
    ├── images/          # Images
    ├── js/              # Existing JavaScript
    ├── uploads/         # Uploaded documents/images
    └── index.php        # Public entry point
```

## Setup

1. Put this folder in your local web server directory.
2. Update database settings in `app/config/config.php` if needed.
3. For a fresh database, import `database/jeevandaan.sql`.
4. If you already imported the old database, import `database/backend_updates.sql`.
5. Open the app from the configured `APP_URL`.

Default admin:

```text
Username: admin
Password: Admin@123
```
