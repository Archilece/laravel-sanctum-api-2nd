<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;

use App\Models\User;

use App\Http\Controllers\Api\CommentController;
use Illuminate\Support\Facades\Route;


Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

Route::post('/users', function () {
    $users = User::all();

    $usersWithTokens = $users->map(function ($user) {
        return [
            'userId' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'token' => $user->currentAccessToken()->plainTextToken,
        ];
    });

    return response()->json($usersWithTokens);
});



Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('posts', PostController::class);
    Route::post('comments', [CommentController::class, 'store']);
    Route::put('comments/{comment}', [CommentController::class, 'update']);
    Route::delete('comments/{comment}', [
        CommentController::class,
        'destroy'
    ]);
});