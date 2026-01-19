<?php

namespace App\Http\Controllers;

use App\Models\Salary;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        $salaries = Salary::with('user')
            ->orderBy('period', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('salaries.index', compact('salaries'));
    }

    public function create()
    {
        $users = User::lazy();
        return view('salaries.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'daily_salary' => 'required|numeric|min:0',
            'working_days' => 'required|numeric|min:0',
            'bonus' => 'required|numeric|min:0',
            'deductions' => 'required|numeric|min:0',
            'status' => 'required|in:pending,paid',
            'notes' => 'nullable|string',
        ]);

        $working_days = $request->working_days;

        $base_salary = $request->daily_salary * $working_days;
        $net_salary = $base_salary + $request->bonus - $request->deductions;

        Salary::create([
            'user_id' => $request->user_id,
            'period' => $request->end_date, // Keep for backward compatibility
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'daily_salary' => $request->daily_salary,
            'working_days' => $working_days,
            'base_salary' => $base_salary,
            'bonus' => $request->bonus,
            'deductions' => $request->deductions,
            'net_salary' => $net_salary,
            'status' => $request->status,
            'paid_at' => $request->status === 'paid' ? now() : null,
            'notes' => $request->notes,
        ]);

        return redirect()->route('salaries.index')->with('success', 'Gaji berhasil dicatat.');
    }

    public function edit(Salary $salary)
    {
        $users = User::where('role', '!=', 'super_admin')->get();
        return view('salaries.edit', compact('salary', 'users'));
    }

    public function update(Request $request, Salary $salary)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'daily_salary' => 'required|numeric|min:0',
            'working_days' => 'required|numeric|min:0',
            'bonus' => 'required|numeric|min:0',
            'deductions' => 'required|numeric|min:0',
            'status' => 'required|in:pending,paid',
            'notes' => 'nullable|string',
        ]);

        $working_days = $request->working_days;

        $base_salary = $request->daily_salary * $working_days;
        $net_salary = $base_salary + $request->bonus - $request->deductions;

        $salary->update([
            'user_id' => $request->user_id,
            'period' => $request->end_date,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'daily_salary' => $request->daily_salary,
            'working_days' => $working_days,
            'base_salary' => $base_salary,
            'bonus' => $request->bonus,
            'deductions' => $request->deductions,
            'net_salary' => $net_salary,
            'status' => $request->status,
            'paid_at' => ($request->status === 'paid' && $salary->status !== 'paid') ? now() : ($request->status === 'pending' ? null : $salary->paid_at),
            'notes' => $request->notes,
        ]);

        return redirect()->route('salaries.index')->with('success', 'Data gaji berhasil diperbarui.');
    }

    public function destroy(Salary $salary)
    {
        $salary->delete();
        return redirect()->route('salaries.index')->with('success', 'Data gaji berhasil dihapus.');
    }

    public function markAsPaid(Salary $salary)
    {
        $salary->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Gaji telah ditandai sebagai LUNAS.');
    }
}
