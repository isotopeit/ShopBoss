<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Isotope\ShopBoss\Models\Branch;
use App\Http\Controllers\Controller;

class BranchController extends Controller
{
    public static $permissions = [
        'index'   => ['access_branch', 'Branch List'],
        'create'  => ['create_branch', 'Branch Create'],
        'store'   => ['store_branch', 'Branch Store'],
        'edit'    => ['edit_branch', 'Branch Edit'],
        'update'  => ['update_branch', 'Branch Update'],
        'destroy' => ['delete_branch', 'Branch Delete'],
    ];

    public function index() {
        return view('shopboss::branch.index', ['branches' => Branch::orderByDesc('id')->get()]);
    }

    public function store(Request $request) {
        $request->validate([
            'branch_no'   => 'required|unique:branches,branch_no',
            'branch_name' => 'required'
        ]);

        Branch::create([
            'uuid'               => Str::uuid(),
            'branch_name'        => $request->branch_name,
            'branch_description' => $request->branch_description,
            'branch_no'          => $request->branch_no,
            'branch_location'    => $request->branch_location,
        ]);

        return redirect()->route('branches.index')->withSuccess("Branch Created");
    }

    public function create() {
        return view('shopboss::branch.create');
    }

    public function edit($id) {
        $branch = Branch::findOrFail($id);
        return view('shopboss::branch.edit', compact('branch'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'branch_no'   => 'required|unique:branches,branch_no,'. $id,
            'branch_name' => 'required'
        ]);

        Branch::findOrFail($id)->update([
            'branch_name'        => $request->branch_name,
            'branch_description' => $request->branch_description,
            'branch_no'          => $request->branch_no,
            'branch_location'    => $request->branch_location,
        ]);

        return redirect()->route('branches.index')->withSuccess("Branch Updated");
    }

    public function destroy($id) {
        Branch::findOrFail($id)->delete();
        toast('Branch Deleted!', 'warning');
        return redirect()->route('branches.index')->withSuccess("Branch deleted");
    }
}
