<?php

namespace Modules\Auth\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Modules\Auth\Models\SuperAdmin;
use Modules\Auth\Models\User;
use Spatie\Permission\Models\Role;

class AuthService
{
    public function register(array $data)
    {
        if (isset($data['profile_image'])) {
            $image = $data['profile_image'];
            $imageName = 'User_' . time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('Users', $imageName, 'public');
            $data['profile_image'] = Storage::url($path);
        }

        $user = User::create([
            'name'         => $data['name'],
            'email'        => $data['email'] ?? null,
            'country_code' => $data['country_code'] ?? '+2',
            'phone'        => $data['phone'] ?? null,
            'password'     => Hash::make($data['password']),
            'gender'       => $data['gender'] ?? null,
            'profile_image' => $data['profile_image'] ?? null,
        ]);

        if (isset($data['role']) ) {
            $role = Role::where('name', $data['role'])->first();
            $user->assignRole($role);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $user->load('roles');

        return [
            'token' => $token,
            'user'  => $user,
        ];

    }

    public function login(array $data)
    {
        $user = User::where(function ($query) use ($data) {
            if (!empty($data['email'])) {
                $query->where('email', $data['email']);
            }
            if (!empty($data['phone'])) {
                $query->orWhere('phone', $data['phone']);
            }
        })->first();

        if ($user && Hash::check($data['password'], $user->password)) {

            // if (!$user->otp_verified_at) {
            //     throw new Exception('Your phone number is not verified. Please verify your OTP first.');
            // }

            $token = $user->createToken('auth_token')->plainTextToken;
            $user->load('roles');

            return [
                'token' => $token,
                'user'  => $user,
            ];
        }

        return null;
    }

    public function adminLogin(array $data)
    {
        $user = SuperAdmin::where(function ($query) use ($data) {
            if (!empty($data['email'])) {
                $query->where('email', $data['email']);
            }
            if (!empty($data['phone'])) {
                $query->orWhere('phone', $data['phone']);
            }
        })->first();

        if ($user && Hash::check($data['password'], $user->password) && $user->hasRole('SuperAdmin')) {
            $token = $user->createToken('auth_token')->plainTextToken;
            $user->load('roles');

            return [
                'token' => $token,
                'user'  => $user,
            ];
        }

        return null;
    }

    public function resetPassword($user, $password)
    {
        $user->update([
            'password' => Hash::make($password),
            'otp' => null,
        ]);

        $user->tokens()->delete();
    }

}
