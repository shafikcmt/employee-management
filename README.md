# Employee Management Laravel System

Generated Laravel source package for: employee details, attendance/absent reports, advance payments, extra duty/Friday duty, payslips, Excel import, and one-click PDF/Excel/ZIP reports.

## Setup
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run dev
php artisan serve
```

Default login:
- admin@example.com / password
- hr@example.com / password
- viewer@example.com / password

## Import reference Excel files
The uploaded Excel files are included in `storage/app/imports/reference`.

```bash
php artisan import:employee-excel \
  --advance="storage/app/imports/reference/Advance Sheet 2025 for Weekly 2.xlsx" \
  --timesheet="storage/app/imports/reference/02,FEBRUARY 2026- TIMESHEET  WADI SAFAR PROJECT - .xlsx" \
  --project="WADI SAFAR" --month=2 --year=2026
```

## Main modules
Dashboard, Employees, Projects, Companies, Designations, Attendance, Absent Report, Advance Payments, Extra Duty, Payslips, Reports, Imports.

## Notes
This is source code without `vendor/` and `node_modules/`. Install dependencies before running.
