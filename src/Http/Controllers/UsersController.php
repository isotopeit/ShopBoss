<?php

namespace Isotope\ShopBoss\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Isotope\ShopBoss\Models\Upload;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Support\Renderable;
use Isotope\ShopBoss\Http\Services\DataTables\UsersDataTable;

class UsersController extends Controller
{
    public function index(UsersDataTable $dataTable) {
        abort_if(Gate::denies('access_user_management|user_access'), 403);

        return $dataTable->render('pos::users.index');
    }


    public function create() {
        abort_if(Gate::denies('access_user_management|user_create'), 403);

        return view('pos::users.create');
    }


    public function store(Request $request) 
    {
        try {
            abort_if(Gate::denies('access_user_management|user_create'), 403);

            $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|max:255|confirmed'
            ]);
            
            DB::beginTransaction();

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'is_active' => $request->is_active
            ]);
    
            $user->assignRole($request->role);
    
            if ($request->has('image')) {
                $tempFile = Upload::where('folder', $request->image)->first();
                if ($tempFile) {
                    $user->addMedia(Storage::path('public/temp/' . $request->image . '/' . $tempFile->filename))->toMediaCollection('avatars');
    
                    Storage::deleteDirectory('public/temp/' . $request->image);
                    $tempFile->delete();
                }
            }
            
            DB::commit();
            
            toast("User Created & Assigned '$request->role' Role!", 'success');
    
            return redirect()->route('users.index');
            
        } catch (Exception $th) {
            DB::rollBack();
        }
    }


    public function edit(User $user) {
        abort_if(Gate::denies('access_user_management|user_update'), 403);

        return view('pos::users.edit', compact('user'));
    }


    public function update(Request $request, User $user) {
        abort_if(Gate::denies('access_user_management|user_update'), 403);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,'.$user->id,
        ]);

        $user->update([
            'name'     => $request->name,
            'email'    => $request->email,
            'is_active' => $request->is_active
        ]);

        $user->syncRoles($request->role);

        if ($request->has('image')) {
            $tempFile = Upload::where('folder', $request->image)->first();

            if ($user->getFirstMedia('avatars')) {
                $user->getFirstMedia('avatars')->delete();
            }

            if ($tempFile) {
                $user->addMedia(Storage::path('public/temp/' . $request->image . '/' . $tempFile->filename))->toMediaCollection('avatars');

                Storage::deleteDirectory('public/temp/' . $request->image);
                $tempFile->delete();
            }
        }

        toast("User Updated & Assigned '$request->role' Role!", 'info');

        return redirect()->route('users.index');
    }


    public function destroy(User $user) {
        abort_if(Gate::denies('access_user_management|user_delete'), 403);

        $user->delete();

        toast('User Deleted!', 'warning');

        return redirect()->route('users.index');
    }
}