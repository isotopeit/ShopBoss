<?php

namespace Isotope\ShopBoss\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Isotope\ShopBoss\Models\Upload;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Modules\User\Rules\MatchCurrentPassword;



class ProfileController extends Controller
{

    public static $permissions = [
        
        'edit'           => ['edit_profile', 'Profile Edit'],
        'update'         => ['update_profile', 'Profile Update'],
        'updatePassword' => ['update_password', 'Update Password'],
    ];


    public function edit() {
        return view('pos::users.profile');
    }

    public function update(Request $request) {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id()
        ]);
        $user = User::find(auth()->user()->id);
        
        $user->update([
            'name'  => $request->name,
            'email' => $request->email
        ]);

        if ($request->has('image')) {
            if ($request->has('image')) {
                $tempFile = Upload::where('folder', $request->image)->first();

                if ($user->getFirstMedia('avatars')) {
                    $user->getFirstMedia('avatars')->delete();
                }
                if ($tempFile) {
                    // dd(Storage::path('temp/' . $request->image . '/' . $tempFile->filename));
                    $user->addMedia(Storage::path('temp/' . $request->image . '/' . $tempFile->filename))->toMediaCollection('avatars');

                    Storage::deleteDirectory('temp/' . $request->image);
                    $tempFile->delete();
                }
            }
        }

        toast('Profile Updated!', 'success');

        return back();
    }

    public function updatePassword(Request $request) {
        $request->validate([
            'current_password'  => 'required',
            'password' => 'required|min:8|max:255|confirmed'
        ]);
        
        $user = User::find(auth()->user()->id);

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'The current password does not match our records.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        toast('Password Updated!', 'success');

        return back();
    }
}
