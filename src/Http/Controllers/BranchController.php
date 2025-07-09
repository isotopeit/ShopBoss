<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Isotope\ShopBoss\Models\Branch;
use App\Http\Controllers\Controller;
use Isotope\Metronic\Models\Setting;
use Illuminate\Support\Facades\Artisan;

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
        $branches = Branch::search()->orderByDesc('id')->paginate(15);
        return view('shopboss::branch.index', compact('branches'));
    }

    public function store(Request $request) {
        $request->validate([
            'branch_no'   => 'required|unique:shopboss_branches,branch_no',
            'branch_name' => 'required'
        ]);

        Branch::create([
            'uuid'               => Str::uuid(),
            'branch_name'        => $request->branch_name,
            'branch_description' => $request->branch_description,
            'branch_no'          => $request->branch_no,
            'branch_location'    => $request->branch_location,
        ]);

        return redirect()->route('shopboss-branches.index')->withSuccess("Branch Created");
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
            'branch_no'   => 'required|unique:shopboss_branches,branch_no,'. $id,
            'branch_name' => 'required'
        ]);

        Branch::findOrFail($id)->update([
            'branch_name'        => $request->branch_name,
            'branch_description' => $request->branch_description,
            'branch_no'          => $request->branch_no,
            'branch_location'    => $request->branch_location,
        ]);

        return redirect()->route('shopboss-branches.index')->withSuccess("Branch Updated");
    }

    public function destroy($id) {
           try {
            $branch = Branch::firstWhere('id', $id);
            if (is_null($branch)) throw new Exception("Branch Not Found", 400);
            $branch->delete();
            return redirect()->route('shopboss-branches.index')->withSuccess("Branch deleted");
        } catch (Exception $e) {
            return redirect()->route('shopboss-branches')->withErrors($e->getMessage() . ' / ' . $e->getLine());
        }
    }

    public function branchEnable(Request $request)
    {
        try {
            $isEnabled = $request->input('enabled') ? 1 : 0;

            Setting::updateOrCreate(
                ['option' => 'enable_branch'],
                ['text' => $isEnabled, 'group' => 'therapy-branch']
            );

            Artisan::call('cache:clear');

            $message = $isEnabled ? 'Branch Enabled successfully.' : 'Branch Disabled successfully.';

            return redirect()->route('shopboss-branches.index')->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('shopboss-branches.index')->withErrors($e->getMessage());
        }
    }
}
