<?php

use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(UserController::class)->group(function () {
    Route::post("/auth", "index");
    Route::get("/", "redirect")->name("login");
});

Route::middleware("auth:sanctum", "role:admin")->prefix("admin")->group(function () {
    Route::controller(ReportController::class)->group(function () {
        Route::get("/", "index");
        Route::get("/statistic", "getStatistic");
        Route::put("/status-update/{id}", "updateStatus");
    });
});
Route::middleware("auth:sanctum", "role:user")->prefix("user")->group(function () {
    Route::controller(ReportController::class)->group(function () {
        Route::get("/", "index");
        Route::post("/store", "store");
        Route::put("/update/{id}", "update");
        Route::delete("/delete/{id}", "destroy");
    });
});
