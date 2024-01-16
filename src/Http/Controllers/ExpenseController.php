<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Illuminate\Http\Request;
use Isotope\ShopBoss\Models\Expense;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Contracts\Support\Renderable;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Exp;
use Isotope\ShopBoss\Http\Services\DataTables\ExpensesDataTable;
use Isotope\ShopBoss\Models\ExpenseCategory;

class ExpenseController extends Controller
{
    public static $permissions = [
        'index'   => ['access_expenses', 'Expense List'],
        'create'  => ['create_expenses', 'Expense Create'],
        'store'   => ['store_expenses', 'Expense Store'],
        'show'    => ['show_expenses', 'Expense Show'],
        'edit'    => ['edit_expenses', 'Expense Edit'],
        'update'  => ['update_expenses', 'Expense Update'],
        'destroy' => ['delete_expenses', 'Expense Delete'],
    ];

    public function index(ExpensesDataTable $dataTable) {
        abort_if(Gate::denies('access_expenses'), 403);

        return $dataTable->render('pos::expense.expenses.index');
    }


    public function create() {
        abort_if(Gate::denies('create_expenses'), 403);
        $categories = ExpenseCategory::all();
        return view('pos::expense.expenses.create',compact('categories'));
    }


    public function store(Request $request) {
        abort_if(Gate::denies('store_expenses'), 403);

        $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|max:255',
            'category_id' => 'required',
            'amount' => 'required|numeric|max:2147483647',
            'details' => 'nullable|string|max:1000'
        ]);

        Expense::create([
            'date' => $request->date,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'details' => $request->details
        ]);

        toast('Expense Created!', 'success');

        return redirect()->route('expenses.index');
    }


    public function edit(Expense $expense) {
        abort_if(Gate::denies('edit_expenses'), 403);
        $categories = ExpenseCategory::all();
        return view('pos::expense.expenses.edit', compact('expense','categories'));
    }


    public function update(Request $request, Expense $expense) {
        abort_if(Gate::denies('update_expenses'), 403);

        $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|max:255',
            'category_id' => 'required',
            'amount' => 'required|numeric|max:2147483647',
            'details' => 'nullable|string|max:1000'
        ]);

        $expense->update([
            'date' => $request->date,
            'reference' => $request->reference,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'details' => $request->details
        ]);

        toast('Expense Updated!', 'info');

        return redirect()->route('expenses.index');
    }


    public function destroy(Expense $expense) {
        abort_if(Gate::denies('delete_expenses'), 403);

        $expense->delete();

        toast('Expense Deleted!', 'warning');

        return redirect()->route('expenses.index');
    }
}
