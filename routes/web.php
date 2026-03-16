<?php

use App\Http\Controllers\CompetitionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\CompetitionAdminController;
use App\Http\Controllers\Admin\NewsAdminController;
use App\Http\Controllers\Admin\TeamAdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/sacensibas', [CompetitionController::class, 'index'])->name('competitions.index');
Route::get('/jaunumi', [NewsController::class, 'index'])->name('news.index');

Route::middleware('guest')->group(function () {
	Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
	Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function () {
	Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

	Route::get('/admin/profils', [AuthController::class, 'adminProfile'])->name('admin.profile');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
	Route::get('/sacensibas', [CompetitionAdminController::class, 'index'])->name('competitions.index');
	Route::get('/sacensibas/izveidot', [CompetitionAdminController::class, 'create'])->name('competitions.create');
	Route::post('/sacensibas', [CompetitionAdminController::class, 'store'])->name('competitions.store');
	Route::get('/sacensibas/{competition}/rediget', [CompetitionAdminController::class, 'edit'])->name('competitions.edit');
	Route::put('/sacensibas/{competition}', [CompetitionAdminController::class, 'update'])->name('competitions.update');
	Route::delete('/sacensibas/{competition}', [CompetitionAdminController::class, 'destroy'])->name('competitions.destroy');
	Route::post('/sacensibas/{competition}/komandas', [CompetitionAdminController::class, 'storeTeam'])->name('competitions.teams.store');
	Route::post('/sacensibas/{competition}/speles', [CompetitionAdminController::class, 'storeMatch'])->name('competitions.matches.store');
	Route::put('/sacensibas/{competition}/vietas', [CompetitionAdminController::class, 'updatePlacements'])->name('competitions.placements.update');

	Route::get('/jaunumi', [NewsAdminController::class, 'index'])->name('news.index');
	Route::get('/jaunumi/izveidot', [NewsAdminController::class, 'create'])->name('news.create');
	Route::post('/jaunumi', [NewsAdminController::class, 'store'])->name('news.store');
	Route::get('/jaunumi/{newsPost}/rediget', [NewsAdminController::class, 'edit'])->name('news.edit');
	Route::put('/jaunumi/{newsPost}', [NewsAdminController::class, 'update'])->name('news.update');
	Route::delete('/jaunumi/{newsPost}', [NewsAdminController::class, 'destroy'])->name('news.destroy');

	Route::get('/komandas', [TeamAdminController::class, 'index'])->name('teams.index');
	Route::get('/komandas/izveidot', [TeamAdminController::class, 'create'])->name('teams.create');
	Route::post('/komandas', [TeamAdminController::class, 'store'])->name('teams.store');
	Route::get('/komandas/{team}/rediget', [TeamAdminController::class, 'edit'])->name('teams.edit');
	Route::put('/komandas/{team}', [TeamAdminController::class, 'update'])->name('teams.update');
	Route::delete('/komandas/{team}', [TeamAdminController::class, 'destroy'])->name('teams.destroy');
});
