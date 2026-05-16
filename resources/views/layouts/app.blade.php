<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900">
<div class="min-h-screen md:flex">
    <aside class="bg-white md:w-72 p-4 border-r">
        <div class="rounded-2xl bg-teal-700 px-4 py-3 text-white">
            <b class="text-lg">Employee MS</b>
            <div class="text-xs text-teal-100">Attendance, salary and reports</div>
        </div>

        <a class="mt-4 block rounded-xl bg-amber-500 px-4 py-3 text-center text-sm font-bold text-white hover:bg-amber-600" href="{{ route('attendance.take') }}">
            Take Attendance
        </a>

        <nav class="mt-5 grid gap-1">
            @foreach([
                ['Dashboard','dashboard'],
                ['Employees','employees.index'],
                ['Attendance Sheet','attendance.index'],
                ['Take Attendance','attendance.take'],
                ['Absent Report','absent.index'],
                ['Advances','advance-payments.index'],
                ['Extra Duty','extra-duties.index'],
                ['Payslips','payslips.index'],
                ['Reports','reports.index'],
                ['Imports','imports.index'],
                ['Projects','projects.index'],
                ['Companies','companies.index'],
                ['Designations','designations.index'],
            ] as [$label,$route])
                <a class="rounded-xl px-3 py-2 text-sm hover:bg-slate-50 {{ request()->routeIs($route) ? 'bg-slate-100 font-semibold text-teal-700' : '' }}" href="{{ route($route) }}">
                    {{ $label }}
                </a>
            @endforeach
        </nav>
    </aside>

    <main class="flex-1 p-4 md:p-6">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-2xl font-bold">@yield('title')</h1>
            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button class="btn-light">Logout</button>
            </form>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-xl bg-emerald-50 p-3 text-emerald-700">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="mb-4 rounded-xl bg-red-50 p-3 text-red-700">{{ $errors->first() }}</div>
        @endif

        @yield('content')
    </main>
</div>
</body>
</html>
