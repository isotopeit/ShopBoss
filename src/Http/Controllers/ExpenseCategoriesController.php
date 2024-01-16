<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Isotope\ShopBoss\Models\ExpenseCategory;
use Isotope\ShopBoss\Http\Services\DataTables\ExpenseCategoriesDataTable;

class ExpenseCategoriesController extends Controller
{
    public static $permissions = [
        'index'   => ['access_expense_categories', 'Expense Categories List'],
        'create'  => ['create_expense_categories', 'Expense Categories Create'],
        'store'   => ['store_expense_categories', 'Expense Categories Store'],
        'show'    => ['show_expense_categories', 'Expense Categories Show'],
        'edit'    => ['edit_expense_categories', 'Expense Categories Edit'],
        'update'  => ['update_expense_categories', 'Expense Categories Update'],
        'destroy' => ['delete_expense_categories', 'Expense Categories Delete'],
    ];

    public function index(ExpenseCategoriesDataTable $dataTable) {
        abort_if(Gate::denies('access_expense_categories'), 403);

        return $dataTable->render('pos::expense.categories.index');
    }

    public function store(Request $request) {
        abort_if(Gate::denies('store_expense_categories'), 403);

        $request->validate([
            'category_name' => 'required|string|max:255|unique:expense_categories,category_name',
            'category_description' => 'nullable|string|max:1000'
        ]);

        ExpenseCategory::create([
            'category_name' => $request->category_name,
            'category_description' => $request->category_description
        ]);

        toast('Expense Category Created!', 'success');

        return redirect()->route('expense-categories.index');
    }


    public function edit(ExpenseCategory $expenseCategory) {
        abort_if(Gate::denies('edit_expense_categories'), 403);

        return view('pos::expense.categories.edit', compact('expenseCategory'));
    }


    public function update(Request $request, ExpenseCategory $expenseCategory) {
        abort_if(Gate::denies('update_expense_categories'), 403);

        $request->validate([
            'category_name' => 'required|string|max:255|unique:expense_categories,category_name,' . $expenseCategory->id,
            'category_description' => 'nullable|string|max:1000'
        ]);

        $expenseCategory->update([
            'category_name'        => $request->category_name,
            'category_description' => $request->category_description
        ]);

        toast('Expense Category Updated!', 'info');

        return redirect()->route('expense-categories.index');
    }


    public function destroy(ExpenseCategory $expenseCategory) {
        abort_if(Gate::denies('delete_expense_categories'), 403);

        if ($expenseCategory->expenses->isNotEmpty()) {
            return back()->withErrors('Can\'t delete beacuse there are expenses associated with this category.');
        }

        $expenseCategory->delete();

        toast('Expense Category Deleted!', 'warning');

        return redirect()->route('expense-categories.index');
    }
}
