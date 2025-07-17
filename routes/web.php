<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Маршруты для гостей (только регистрация/авторизация)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Регистрация
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    // Авторизация
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Основные маршруты (требуют аутентификации)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Выход
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Материалы (CRUD)
    Route::resource('materials', MaterialController::class)->except(['index', 'show']);
    
    // Просмотр материалов (только для авторизованных)
    Route::get('/materials', [MaterialController::class, 'index'])->name('materials.index');
    Route::get('/materials/{material}', [MaterialController::class, 'show'])->name('materials.show');
    
    // Личный кабинет
    Route::get('/profile', [UserController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile']);
});

/*
|--------------------------------------------------------------------------
| Административные маршруты
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Панель управления
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Управление пользователями
    Route::resource('users', UserController::class)->except(['show']);
    
    // Управление материалами
    Route::get('/materials', [AdminController::class, 'materials'])->name('materials.index');
    Route::delete('/materials/{material}', [AdminController::class, 'destroyMaterial'])->name('materials.destroy');
    Route::put('/materials/{material}', [AdminController::class, 'updateMaterial'])->name('materials.update');
});

/*
|--------------------------------------------------------------------------
| Перенаправление с корня
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'materials.index' : 'login');
});