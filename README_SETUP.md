# Medicine Inventory Management System - Setup Guide

## Database Configuration ✅
- **Connection**: MariaDB
- **Host**: 127.0.0.1
- **Port**: 3369
- **Database**: mims
- **Username**: root
- **Password**: Configured

## Default Admin Credentials
- **Username**: admin
- **Password**: admin123

## Running the Application

### Option 1: Using Laravel's Built-in Scripts (Recommended)
```bash
composer run dev
```
This will start:
- Laravel server (http://localhost:8000)
- Queue worker
- Log viewer
- Vite dev server

### Option 2: Manual Start
```bash
# Terminal 1 - Laravel Server
php artisan serve

# Terminal 2 - Vite Dev Server
npm run dev
```

## Access the Application
1. Open your browser: http://localhost:8000
2. Login with admin credentials:
   - Username: `admin`
   - Password: `admin123`

## Features Available

### Admin Role
- Medicine Master (CRUD)
- Receiving/Stock-In
- Inventory Monitoring
- Request Management (RIS & PTR)
- Approvals & Releasing
- Reports
- Audit Logs

### Staff Role
- Receiving/Stock-In
- Inventory Monitoring
- Request Management
- Approvals & Releasing
- Reports

### User Role
- Request Medicine
- My Requests
- Inventory View

## Next Steps
1. Create additional users via database or tinker
2. Add medicine items through the Medicine Master
3. Start receiving stock
4. Create requests and process approvals

## Troubleshooting

### If you see "Migration table not found"
Run: `php artisan migrate`

### If assets don't load
Run: `npm run build`

### If routes don't work
Run: `php artisan route:clear && php artisan config:clear`

