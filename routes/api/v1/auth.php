<?php

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::name("api.v1.auth.")->group(function () {
        Route::post("/", "register")->name("register");
        Route::post("/login", "login")->name("login");
        Route::get("/login", "loginTest")->name("login.test")->middleware("auth");
    });
});
