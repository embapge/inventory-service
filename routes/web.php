<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $phrase  = "[{'url': 'https://via.placeholder.com/640x480.png/0088cc?text=animals+est','name': 'Ikhsan Natsir S.IP'},{'url': 'https://via.placeholder.com/640x480.png/00ff44?text=animals+et','name': 'Ajeng Sudiati'}]";
    dd($phrase, json_decode($phrase, true));
    $updatedText = str_replace("'", "TEMP_PLACEHOLDER", $phrase);
    $updatedText = str_replace('"', "'", $updatedText);
    $updatedText = str_replace("TEMP_PLACEHOLDER", '"', $updatedText);
    $value = json_decode($updatedText, true);
    // dd(json_decode(["barata" => "test"]));
    dd(json_decode('[{"url": "https://via.placeholder.com/640x480.png/0088cc?text=animals+est","name": "Ikhsan Natsir S.IP"},{"url": "https://via.placeholder.com/640x480.png/00ff44?text=animals+et","name": "Ajeng Sudiati"}]'));
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
