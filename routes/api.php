<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ZipCodeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Ez az alapértelmezett user route (meghagyhatja, ha később kellene hitelesítés)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// --- EZEK A MI ÚJ VÉGPONTJAINK ---

// Megyék lekérdezése
Route::get('/counties', [ZipCodeController::class, 'counties']);

// Települések lekérdezése
Route::get('/settlements', [ZipCodeController::class, 'settlements']);

// Keresés (irányítószámok és települések)
Route::get('/zipcodes/search', [ZipCodeController::class, 'search']);

Route::get('/counties/{id}/letters', [ZipCodeController::class, 'letters']);
Route::post('/settlements', [ZipCodeController::class, 'store']);
Route::delete('/settlements/{id}', [ZipCodeController::class, 'destroy']);
