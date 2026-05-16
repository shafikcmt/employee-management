# Employee Management System - Setup Guide

A comprehensive Laravel-based Employee Management System for attendance tracking, payroll processing, and reporting.

## 📋 System Requirements

- PHP 8.2 or higher
- MySQL 5.7 or higher (MariaDB compatible)
- XAMPP or similar local development environment
- Node.js 16+ and npm (for frontend build)
- Composer (PHP dependency manager)

## 🚀 Quick Start (XAMPP Setup)

### Step 1: Clone/Extract Project

```bash
# Navigate to XAMPP htdocs
cd c:\xampp\htdocs

# The project folder is: employee-management
cd employee-management
```

### Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### Step 3: Environment Configuration

```bash
# Copy example environment file
copy .env.example .env

# Generate application key
php artisan key:generate
```

Edit `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=employee_management
DB_USERNAME=root
DB_PASSWORD=
```

### Step 4: Database Setup

```bash
# Create database in MySQL
# Using PhpMyAdmin: Create new database called "employee_management"
# Or using command line:
mysql -u root -p
# Then type: CREATE DATABASE employee_management;
# Exit with: exit

# Run migrations
php artisan migrate

# Seed initial data (optional but recommended)
php artisan db:seed
```

### Step 5: Build Frontend Assets

```bash
# Build Tailwind CSS and JavaScript
npm run build

# For development with hot reload:
npm run dev
```

### Step 6: Start Development Server

```bash
# Terminal 1: Start Laravel server
php artisan serve

# Terminal 2: Start Vite dev server (if using npm run dev)
npm run dev
```

Access the application at: `http://localhost:8000`

## 📝 Default Credentials

After seeding, use:
- **Email**: admin@example.com
- **Password**: password

⚠️ **Change these credentials immediately in production!**

## 🔧 Configuration

### Database

Edit `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=employee_management
DB_USERNAME=root
DB_PASSWORD=your_password
```

### File Storage

Ensure `/storage/app/reports` directory exists and is writable:

```bash
# Ensure directories exist
mkdir -p storage/app/reports
mkdir -p storage/app/imports

# Set permissions (Linux/Mac)
chmod -R 755 storage
```

### Mail (Optional)

For email notifications, update `.env`:

```env
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@example.com
```

## 📦 Features & Usage

### 1. Dashboard

**Path**: http://localhost:8000/dashboard

- View employee statistics
- Check today's attendance
- Monitor payroll metrics
- Quick access buttons
- Recent reports

### 2. Employees

**Path**: http://localhost:8000/employees

- Add, edit, delete employees
- Search by name, Iqama, passport, code
- Filter by project, company, designation, status
- View employee details and history

### 3. Attendance

**Path**: http://localhost:8000/attendance/take

- Take daily attendance
- Mark present/absent/Friday/public holiday
- Add hours and remarks
- Bulk actions (mark all present/absent)
- Live summary statistics
- Sticky save bar

**Monthly Sheet**: http://localhost:8000/attendance

- View month-wise attendance
- Excel-like interface
- Horizontal scroll for daily view
- Attendance totals

### 4. Projects

**Path**: http://localhost:8000/projects

- Create and manage projects
- View project-wise employee summary
- Assign employees to projects
- Track project attendance and hours

### 5. Advance Payments

**Path**: http://localhost:8000/advance-payments

- Record employee advance payments
- Filter by employee, project, month
- Track advance history
- Used for payslip deductions

### 6. Extra Duty / Friday Duty

**Path**: http://localhost:8000/extra-duties

- View all extra duty records
- Filter by status (pending/approved/rejected)
- Approve or reject duties
- Track extra duty amount
- Automatically created from attendance if hours marked on Friday/PH

### 7. Payslips

**Path**: http://localhost:8000/payslips

- Generate payslips for employees
- Select month, year, project
- View draft or final status
- Finalize payslips (prevents duplicates)
- Download individual PDF
- Calculate gross, net, deductions

**Calculation**:
- Normal Salary = Normal Hours × Hourly Rate (or Monthly Salary if set)
- Gross = Normal Salary + Approved Extra Duty Amount
- Net = Gross - Advance Deductions

### 8. Reports Center

**Path**: http://localhost:8000/reports

- **Employee List** - Complete directory
- **Daily Attendance** - Day-wise records
- **Monthly Attendance** - Month summary
- **Absent Report** - All absences
- **Advance Payments** - Payment records
- **Extra Duty** - Friday/overtime duties
- **Payslip Report** - Salary details
- **Salary Summary** - Total payroll

**Export Formats**:
- PDF (individual reports)
- Excel (spreadsheets)
- ZIP (all reports bundled)

**Apply Filters**:
- Month/Year
- Project
- Employee
- Company
- Designation

### 9. Excel Import

**Path**: http://localhost:8000/imports

- Upload advance payment sheets
- Upload timesheet files
- Automatic employee matching
- Preview before import
- Bulk create attendance records
- Duplicate prevention

**Import Format**:
- Number = Hours worked
- A = Absent
- Fri = Friday
- PH = Public Holiday
- NW = Not Working
- Empty = No entry

## 🔑 Common Tasks

### Add a New Employee

1. Go to **Employees** → Click **Add Employee**
2. Fill in:
   - Name, Iqama/Passport, Designation
   - Company, Project
   - DOJ, Shift, Accommodation
   - Hourly Rate or Monthly Salary
3. Click **Save**

### Take Attendance

1. Go to **Take Attendance**
2. Select Project and Date
3. Click **Load Employees**
4. In table:
   - Click status buttons: Present, Absent, Fri, PH, NW
   - Enter hours if present
   - Mark extra duty checkbox if applicable
   - Add remarks if needed
5. Click **Save Attendance**

### Generate Payslips

1. Go to **Payslips**
2. Select Month, Year, optionally Project/Employee
3. Click **Generate Payslips**
4. Review draft payslips
5. Click **Finalize** when ready
6. Download PDF or print

### Generate Reports

1. Go to **Reports Center**
2. Apply filters (optional)
3. Click report type button: PDF, Excel, or ZIP
4. Download generated file

## 🐛 Troubleshooting

### Error: `SQLSTATE[HY000]: General error: 1030`

```bash
# Ensure storage folders exist and are writable
mkdir -p storage/app/reports
mkdir -p storage/app/imports
chmod -R 777 storage
```

### Error: `npm run build` fails

```bash
# Clear cache and reinstall
rm -rf node_modules package-lock.json
npm install
npm run build
```

### Error: `public/build/manifest.json` not found

```bash
# Build frontend assets
npm run build

# Or for development:
npm run dev
```

### Database connection error

```bash
# Check MySQL is running
# Verify .env database credentials
# Run migrations again
php artisan migrate:refresh
```

### Cannot upload files

```bash
# Ensure storage directory is writable
chmod -R 777 storage
```

## 📚 Artisan Commands

```bash
# Clear all caches
php artisan optimize:clear

# Show all routes
php artisan route:list

# Reset database
php artisan migrate:refresh --seed

# Create admin user
php artisan tinker
# Then: App\Models\User::factory()->create(['email' => 'admin@test.com', 'password' => 'password'])

# Serve application
php artisan serve
```

## 🔐 Security Notes

1. **Change Default Password**: Update admin password immediately
2. **Environment File**: Never commit `.env` to version control
3. **File Permissions**: Set correct permissions on storage directory
4. **Database**: Use strong passwords in production
5. **HTTPS**: Use SSL certificates in production
6. **Backups**: Regularly backup database and files

## 📞 Support & Documentation

### Key URLs

- Dashboard: http://localhost:8000/dashboard
- Employees: http://localhost:8000/employees
- Attendance: http://localhost:8000/attendance/take
- Payslips: http://localhost:8000/payslips
- Reports: http://localhost:8000/reports

### Database Tables

- `users` - System users
- `employees` - Employee master data
- `projects` - Project records
- `companies` - Company/contractor records
- `designations` - Employee trades/designations
- `attendance_records` - Daily attendance logs
- `advance_payments` - Employee advances
- `extra_duties` - Extra duty/overtime records
- `payslips` - Generated payslips
- `generated_reports` - Report history

## 🎯 Workflow

### Daily Workflow

1. **Morning**: Open "Take Attendance" page
2. **Select**: Project and today's date
3. **Mark**: Status for each employee
4. **Save**: Single click to save all

### Weekly/Monthly Workflow

1. **Approvals**: Review and approve extra duties
2. **Advances**: Add any new advance payments
3. **Generate**: Create payslips for the month
4. **Export**: Download salary reports

### Month-End Workflow

1. **Finalize**: Finalize all payslips
2. **Reports**: Generate all reports ZIP
3. **Distribution**: Download and share payslips
4. **Archive**: Keep backup of reports

## 📊 Data Import Tips

### Best Practices

1. Use Excel with proper date formatting (YYYY-MM-DD)
2. Include header row in sheets
3. Employee names should match Iqama/Passport
4. Use consistent project names
5. Validate data before import
6. Test with small batches first

### Example Sheet Structure

| Name | Iqama | Designation | Day1 | Day2 | Day3 | ...
| --- | --- | --- | --- | --- | --- | --- |
| Ali Ahmed | 12345678 | Carpenter | 8 | 8 | Fri | ... |
| Mohammed | 98765432 | Welder | 8 | A | 8 | ... |

## 🎓 Tips for New Users

1. **Start Simple**: Add few employees first, test attendance
2. **Use Filters**: Combine filters for better reporting
3. **Check Calculations**: Verify payslip calculations before finalization
4. **Monthly Review**: Review absent report monthly
5. **Backup**: Keep backup of reports and data
6. **Mobile**: Attendance page is mobile-friendly

## 📝 Notes

- Application uses UTC timezone - configure in `config/app.php` if needed
- Report generation can take time for large datasets
- Attendance records use `updateOrCreate` - duplicates are prevented
- Advance payments are deducted monthly if payment_date falls in the month
- Extra duties must be approved before inclusion in payslips

---

**Version**: 1.0.0  
**Last Updated**: May 2026  
**Created with**: Laravel 12, PHP 8.2, Blade, Tailwind CSS
