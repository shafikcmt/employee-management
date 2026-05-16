<?php

namespace App\Http\Controllers;

use App\Models\{AdvancePayment, Employee, Project};
use Illuminate\Http\Request;

class AdvancePaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = AdvancePayment::with('employee', 'project');

        // Filter by employee
        if ($request->employee_id) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by project
        if ($request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by month/year
        if ($request->month) {
            $query->whereMonth('payment_date', $request->month);
        }

        if ($request->year) {
            $query->whereYear('payment_date', $request->year);
        }

        $advancePayments = $query->latest('payment_date')->paginate(30)->appends($request->query());

        return view('payments.index', [
            'advancePayments' => $advancePayments,
            'employees' => Employee::orderBy('name')->get(),
            'projects' => Project::orderBy('name')->get(),
            'filters' => [
                'employee_id' => $request->employee_id,
                'project_id' => $request->project_id,
                'month' => $request->month,
                'year' => $request->year,
            ],
        ]);
    }

    public function create()
    {
        return view('payments.form', [
            'advancePayment' => new AdvancePayment,
            'employees' => Employee::where('status', 'active')->orderBy('name')->get(),
            'projects' => Project::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'project_id' => 'nullable|exists:projects,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_type' => 'required|in:cash,cheque,transfer,other',
            'remarks' => 'nullable|string',
        ]);

        AdvancePayment::create($validated);

        return redirect()->route('advance-payments.index')->with('success', 'Advance payment created successfully.');
    }

    public function show(AdvancePayment $advancePayment)
    {
        $advancePayment->load('employee', 'project');

        return view('payments.show', compact('advancePayment'));
    }

    public function edit(AdvancePayment $advancePayment)
    {
        return view('payments.form', [
            'advancePayment' => $advancePayment,
            'employees' => Employee::where('status', 'active')->orderBy('name')->get(),
            'projects' => Project::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, AdvancePayment $advancePayment)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'project_id' => 'nullable|exists:projects,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_type' => 'required|in:cash,cheque,transfer,other',
            'remarks' => 'nullable|string',
        ]);

        $advancePayment->update($validated);

        return redirect()->route('advance-payments.index')->with('success', 'Advance payment updated successfully.');
    }

    public function destroy(AdvancePayment $advancePayment)
    {
        $advancePayment->delete();

        return back()->with('success', 'Advance payment deleted successfully.');
    }
}
