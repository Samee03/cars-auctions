<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (any logged-in user)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::get('/user', [AuthController::class, 'getAuthUser'])->name('user');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change.password');

    // Email verification
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware('signed')
        ->name('verification.verify');
    Route::post('/email/resend', [AuthController::class, 'resendVerificationEmail'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    /*
    |----------------------------------------------------------------------
    | Verified + Approved Users Only
    |----------------------------------------------------------------------
    */
    Route::middleware(['verified', 'approved'])->group(function () {
        Route::get('/profile', [ProfileController::class, 'getProfile'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'updateUser'])->name('update.profile');

        Route::apiResource('/addresses', AddressController::class);

        Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
            Route::get('/users', function () {
                $users = Cache::remember('users.all', 3600, fn () => User::all());
                return response()->json($users);
            });

            Route::get('/search-user', function () {
                $key = request()->keywords;

                $cacheKey = 'users.search.' . md5($key);

                $results = Cache::remember($cacheKey, 3600, fn () => User::search($key)->get());

                return response()->json($results);
            })->name('search');
        });

    });
});
