<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\Auth\Models\User;
use Spatie\Activitylog\Models\Activity;

class UserController extends Controller
{
    public function getAllUsers()
    {
        return $this->respondOk(User::with('roles')->paginate());
    }

    public function showProfile()
    {
        $user = User::find(Auth::id());
        return $this->respondOk($user, 'User Profile');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email',
            'country_code' => 'nullable|string|max:10',
            'phone' => ['nullable', 'string', 'unique:users,phone'],
            'gender' => 'nullable|in:male,female',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
        ]);

        $user = User::find(Auth::id());
        if ($request->file('profile_image')) {
            $file = $request->file('profile_image');
            $fileName = 'Profile_Image' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('Profile_Images', $fileName, 'public');
            $fullPath = Storage::url($path);
            $user->profile_image = $fullPath;
        }

        $user->name = $request->name ?? $user->name;
        $user->email = $request->email ?? $user->email;
        $user->country_code = $request->country_code ?? $user->country_code;
        $user->phone = $request->phone ?? $user->phone;
        $user->gender = $request->gender ?? $user->gender;

        $user->save();
        return $this->respondOk($user, 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $userId = Auth::id();
        $user = User::findOrFail($userId);
        $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $user->password = bcrypt($request->new_password);
        $user->save();
        return $this->respondOk(null, "user password updated successfully.");
    }

    public function deleteUser($id)
    {
        $user = User::find($id);

        if(!$user) {
            return $this->respondNotFound(null, 'User not found');
        }

        $user->delete();
        return $this->respondOk(null, 'User deleted successfully');
    }

    public function getUserActivities($userId)
    {
        $activities = Activity::where('causer_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate();

        return $this->respondOk($activities);
    }
}
