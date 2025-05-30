<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;
use Modules\Auth\Http\Controllers\UserController;
use Modules\Auth\Http\Controllers\RoleController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/admin-login', [AuthController::class, 'adminLogin']);

    Route::post('/forget-password', [AuthController::class, 'forgetPassword']);
    Route::post('/check-phone-otp', [AuthController::class, 'checkPhoneOTPForgetPassword']);
    Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });
});


Route::prefix('users')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [UserController::class, 'getAllUsers']);
    Route::get('/profile', [UserController::class, 'showProfile']);
    Route::post('/update-profile', [UserController::class, 'updateProfile']);
    Route::post('/change-password', [UserController::class, 'changePassword']);
    Route::delete('/{id}', [UserController::class, 'deleteUser'])->middleware('role:SuperAdmin');
});


Route::prefix('roles')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [RoleController::class, 'index']);
    Route::post('/', [RoleController::class, 'store'])->middleware('role:SuperAdmin');
    Route::get('/{id}', [RoleController::class, 'show']);
    Route::post('/{id}/update', [RoleController::class, 'update'])->middleware('role:SuperAdmin');
    Route::delete('/{id}', [RoleController::class, 'destroy'])->middleware('role:SuperAdmin');

    Route::post('/{id}/assign-permissions', [RoleController::class, 'assignPermissions'])->middleware('role:SuperAdmin');
    Route::post('/assign-role/{userId}', [RoleController::class, 'assignRoleToUser'])->middleware('role:SuperAdmin');
});

Route::get('/get-users-without-role', [RoleController::class, 'getUsersWithoutRole'])->middleware(['auth:sanctum','role:SuperAdmin']);

Route::prefix('permissions')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [RoleController::class, 'getPermissions']);
    Route::post('/', [RoleController::class, 'storePermission'])->middleware('role:SuperAdmin');
});