<?php

namespace Isotope\ShopBoss\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Isotope\HRM\Models\Branch;
use App\Http\Controllers\Controller;
use Isotope\HRM\Models\BranchUser;

class BranchUserController extends Controller
{
    public function index(Request $request)
    {
        $query = BranchUser::with(['user', 'branches']);

        $search = $request->input('search', []);

        if (!empty($search['user'])) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search['user'] . '%');
            });
        }
        if (!empty($search['branch'])) {
            $query->whereHas('branch', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search['branch'] . '%');
            });
        }

        $branchUsers = $query->paginate(10)->appends($request->query());

        return view('shopboss::branch_user.index', compact('branchUsers'));
    }

    public function create()
    {
        $users = User::all();
        $branches = Branch::all();
        return view('shopboss::branch_user.create', compact('users', 'branches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'branch_id' => [
                'required',
                'exists:branches,id',
                'unique:branch_user,branch_id,NULL,id,user_id,' . $request->user_id
            ],
        ]);
        BranchUser::create($data);
        return redirect()->route('shop-branch-user.index')->with('success', 'Assigned successfully.');
    }

    public function edit($id)
    {
        $branchUser = BranchUser::findOrFail($id);
        $users = User::all();
        $branches = Branch::all();
        return view('shopboss::branch_user.edit', compact('branchUser', 'users', 'branches'));
    }

    public function update(Request $request, BranchUser $branchUser)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'branch_id' => [
                'required',
                'exists:branches,id',
                'unique:branch_user,branch_id,' . $branchUser->id . ',id,user_id,' . $request->user_id
            ],
        ]);
        $branchUser->update($data);
        return redirect()->route('shop-branch-user.index')->with('success', 'Updated successfully.');
    }

    public function destroy($id)
    {
        $branchUser = BranchUser::findOrFail($id);
        $branchUser->delete();
        return redirect()->route('shop-branch-user.index')->with('success', 'Deleted successfully.');
    }
}
