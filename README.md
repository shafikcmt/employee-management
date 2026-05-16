# Employee Management System (Redeveloped)

A professional, production-ready Laravel Employee Management System designed for HR and accounting teams. Handles attendance tracking, payroll processing, advance payments, extra duty approvals, and comprehensive reporting.

## ✨ Key Features

### 📊 Dashboard
- Real-time statistics and KPIs
- Today's attendance overview
- Monthly payroll summary
- Quick action buttons
- Recent reports history

### 👥 Employee Management
- Complete employee directory
- Search and filter capabilities
- Employment details tracking
- Project assignments
- Attendance and payslip history

### 📅 Attendance & Timesheet
- Simple table-based daily attendance
- Mark present/absent/Friday/public holiday
- Bulk actions (mark all, clear all)
- Monthly attendance sheet view
- Auto-calculation of extra duty
- Live summary statistics

### 💰 Payroll Processing
- Automated payslip generation
- Draft and final status management
- Advance payment deductions
- Extra duty amount inclusion
- Net salary calculation
- PDF download support

### ⏱️ Extra Duty Management
- Friday duty tracking
- Public holiday duty recording
- Pending/approved/rejected workflow
- Extra duty amount accumulation
- Seamless integration with payslips

### 📈 Advanced Reporting
- Employee list reports
- Daily/monthly attendance reports
- Absent employee reports
- Advance payment summaries
- Extra duty/Friday duty reports
- Salary/payslip reports
- Multi-format exports (PDF, Excel, ZIP)
- Report history and download management

### 📥 Excel Import
- Bulk employee import
- Timesheet import with auto-matching
- Advance payment import
- Duplicate prevention
- Validation and error reporting

### 🎯 One-Click Reporting
- Generate all reports at once
- Apply filters across reports
- Download as ZIP archive
- Export individual formats
- Report scheduling history

## 🚀 Quick Start

### Prerequisites
- PHP 8.2+
- MySQL 5.7+
- Node.js 16+ with npm
- Composer
- XAMPP (recommended for local development)

### Installation

1. **Extract Project**
```bash
cd c:\xampp\htdocs\employee-management
```

2. **Install Dependencies**
```bash
composer install
npm install
```

3. **Setup Environment**
```bash
copy .env.example .env
php artisan key:generate
```

4. **Configure Database** (Edit `.env`)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=employee_management
DB_USERNAME=root
DB_PASSWORD=
```

5. **Run Migrations**
```bash
php artisan migrate --seed
```

6. **Build Frontend**
```bash
npm run build
# Or for development with hot reload:
npm run dev
```

7. **Start Server**
```bash
# Terminal 1
php artisan serve

# Terminal 2 (if using npm run dev)
npm run dev
```

8. **Access Application**
- URL: http://localhost:8000
- Email: admin@example.com
- Password: password

## 📋 Modules Overview

### Dashboard
- Employee statistics (total, active, inactive)
- Today's attendance metrics
- Monthly hours summary
- Payroll totals and net amounts
- Advance payments tracking
- Extra duty pending count
- Recent reports list
- Quick action buttons

### Employees
- Add, edit, delete employees
- Search by name, Iqama, passport, code
- Filter by project, company, designation, status
- View employment history
- Track attendance, advances, extra duties, payslips

### Projects
- Create and manage projects
- View project-wise statistics
- Assign/remove employees
- Project-wise attendance summary
- Project performance metrics

### Attendance
- **Take Attendance**: Daily attendance recording with simple table interface
- **Monthly Sheet**: Excel-like month-view with all days
- **Absent Report**: Quick view of all absences

### Payroll
- **Advances**: Track employee advance payments
- **Extra Duties**: Manage Friday duty and overtime
- **Payslips**: Generate, finalize, and download payslips
- **Reports**: Comprehensive reporting system

### Reports
- Employee list
- Daily/monthly attendance
- Absent employees
- Advance payments
- Extra duties
- Payslip summaries
- Salary reports
- Export in PDF, Excel, or ZIP

### Imports
- Upload Excel files
- Auto-match employees
- Import timesheet data
- Import advance payments
- Duplicate prevention
- Success/failure reporting

## 🗂️ Project Structure

```
employee-management/
├── app/
│   ├── Http/Controllers/      # All controllers (refactored & readable)
│   ├── Models/                # Eloquent models with relationships
│   └── Services/              # Business logic services
├── database/
│   ├── migrations/            # Database schema
│   └── seeders/               # Initial data
├── resources/
│   ├── css/                   # Tailwind styles
│   ├── js/                    # Alpine.js scripts
│   └── views/                 # Blade templates
├── routes/                    # API & web routes
├── storage/
│   ├── app/reports/           # Generated reports
│   └── app/imports/           # Uploaded files
├── SETUP_GUIDE.md            # Detailed setup instructions
├── composer.json             # PHP dependencies
└── package.json              # Node dependencies
```

## 💻 Commands Reference

### Development
```bash
# Start development server
php artisan serve

# Watch frontend changes
npm run dev

# Build for production
npm run build
```

### Database
```bash
# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Reset database
php artisan migrate:refresh --seed

# Show migrations status
php artisan migrate:status
```

### Optimization
```bash
# Clear all caches
php artisan optimize:clear

# Cache routes, config, views
php artisan optimize

# Show all routes
php artisan route:list
```

## 🔑 Database Schema Highlights

### Core Tables
- `users` - System users with roles
- `employees` - Employee master data
- `projects` - Project records
- `companies` - Company/contractor info
- `designations` - Job titles/trades

### Operational Tables
- `attendance_records` - Daily attendance logs
- `attendance_months` - Monthly attendance tracking
- `advance_payments` - Employee advances
- `extra_duties` - Extra duty/overtime records
- `payslips` - Generated payslips
- `employee_project_assignments` - Project history
- `generated_reports` - Report history
- `imports` - Import logs

## 🎯 Workflow Examples

### Daily Attendance Entry
1. Go to "Take Attendance"
2. Select Project and Date
3. Click "Load Employees"
4. Mark status for each employee
5. Enter hours if present
6. Click "Save Attendance"

### Monthly Payslip Generation
1. Go to "Payslips"
2. Select Month, Year, optionally Project
3. Click "Generate Payslips"
4. Review calculations
5. Click "Finalize" on each payslip
6. Download PDF

### Generate All Reports
1. Go to "Reports Center"
2. Apply optional filters
3. Click "Generate All Reports ZIP"
4. Download the ZIP file
5. Extract and review reports

## 🔒 Security Features

- User authentication with Laravel Breeze
- Role-based access control (Admin, HR, Viewer)
- CSRF protection on all forms
- SQL injection prevention (Eloquent ORM)
- File upload validation
- Environment variable configuration
- Secure password hashing

## 🐛 Troubleshooting

**"SQLSTATE[HY000] General error"**
```bash
mkdir -p storage/app/reports
mkdir -p storage/app/imports
chmod -R 777 storage
```

**"manifest.json not found"**
```bash
npm install
npm run build
```

**"Database connection refused"**
- Ensure MySQL is running
- Check .env database credentials
- Create database: `CREATE DATABASE employee_management;`

**"File upload fails"**
- Check storage directory permissions
- Ensure disk space available
- Verify file size limits in php.ini

## 📞 Default Credentials

After fresh installation:
- **Admin**: admin@example.com / password
- **HR**: hr@example.com / password
- **Viewer**: viewer@example.com / password

⚠️ **Change passwords immediately in production!**

## 📈 Performance Tips

1. **Optimize Database Queries**
   - Use pagination for large datasets
   - Eager load relationships
   - Add indexes on frequently searched columns

2. **Cache Reports**
   - Generated reports are stored for retrieval
   - Use filters to reduce report size

3. **Bulk Operations**
   - Import multiple records at once
   - Batch payslip generation

4. **Regular Maintenance**
   ```bash
   # Clear old files periodically
   rm -rf storage/app/reports/old_*.xlsx
   
   # Optimize database
   php artisan optimize
   ```

## 📚 Documentation Files

- **SETUP_GUIDE.md** - Detailed setup and configuration
- **README.md** - This file, system overview
- **routes/web.php** - All application routes
- **app/Http/Controllers/** - Controller documentation

## 🔄 Recent Improvements (v2.0)

- ✅ Refactored all compressed controllers to be readable
- ✅ Improved Dashboard with comprehensive statistics
- ✅ Enhanced Attendance UI with simple table view
- ✅ Created professional Reports Center
- ✅ Redesigned application layout and navigation
- ✅ Better error handling and validation
- ✅ Expanded service classes for business logic
- ✅ Added comprehensive documentation

## 🎓 For New Users

1. Read **SETUP_GUIDE.md** for installation
2. Use default credentials to login
3. Explore Dashboard to understand features
4. Add test employees
5. Record attendance
6. Generate reports
7. Refer to documentation for specific tasks

## 📝 Changelog

### v2.0 (Current)
- Complete UI/UX redesign
- Refactored codebase for maintainability
- Enhanced Reports Center
- Improved Import workflow
- Better error handling
- Comprehensive documentation

### v1.0
- Initial release
- Basic CRUD operations
- Simple reporting
- Excel import

## 🤝 Support

For issues or questions:
1. Check SETUP_GUIDE.md troubleshooting section
2. Verify database and file permissions
3. Check Laravel logs: `storage/logs/laravel.log`
4. Ensure all dependencies are installed

## 📄 License

This project is provided as-is for internal use.

---

**Version**: 2.0  
**Last Updated**: May 2026  
**Technology Stack**: Laravel 12 • PHP 8.2 • MySQL • Blade • Tailwind CSS • Vite
