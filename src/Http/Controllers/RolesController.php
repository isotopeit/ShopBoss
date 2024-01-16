<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Isotope\Authorization\Models\Role;
use Illuminate\Contracts\Support\Renderable;
use Isotope\ShopBoss\Http\Services\DataTables\RolesDataTable;

class RolesController extends Controller
{
    public function index(RolesDataTable $dataTable) {
        abort_if(Gate::denies('access_user_management|role_access'), 403);

        return $dataTable->render('pos::roles.index');
    }


    public function create() {
        abort_if(Gate::denies('access_user_management|role_create'), 403);

        return view('pos::roles.create');
    }


    public function store(Request $request) {
        abort_if(Gate::denies('access_user_management|role_create'), 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'required|array'
        ]);

        DB::beginTransaction();

        $role = Role::create([
            'name' => $request->name
        ]);
        
        $role->givePermissionTo($request->permissions);

        DB::commit();

        toast('Role Created With Selected Permissions!', 'success');

        return redirect()->route('roles.index');
    }


    public function edit(Role $role) {
        abort_if(Gate::denies('access_user_management|role_update'), 403);

        return view('pos::roles.edit', compact('role'));
    }


    public function update(Request $request, Role $role) {
        abort_if(Gate::denies('access_user_management|role_update'), 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'required|array'
        ]);

        $role->update([
            'name' => $request->name
        ]);

        $role->syncPermissions($request->permissions);

        toast('Role Updated With Selected Permissions!', 'success');

        return redirect()->route('roles.index');
    }


    public function destroy(Role $role) {
        abort_if(Gate::denies('access_user_management|role_destroy'), 403);

        $role->delete();

        toast('Role Deleted!', 'success');

        return redirect()->route('roles.index');
    }
    
}
