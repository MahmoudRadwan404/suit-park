<?php
use App\Http\Controllers\NazelController;
use App\Models\Room;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\AmenityController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PolicyController;




Route::get('/policy', [PolicyController::class, 'index']);
Route::middleware('auth:api')->group(function () {
    Route::post('/policy', [PolicyController::class, 'store']);
    Route::delete('/policy', [PolicyController::class, 'destroy']);
});
// Rooms
Route::prefix('rooms')->group(function () {
    Route::get('/', [RoomController::class, 'index']);
    Route::get('/{id}', [RoomController::class, 'show']);
    Route::get('/filter/type', [RoomController::class, 'filterByType']);

    Route::middleware('auth:api')->group(function () {
        Route::post('/', [RoomController::class, 'store']);
        Route::post('/{id}', [RoomController::class, 'update']);
        Route::delete('/{id}', [RoomController::class, 'destroy']);
        Route::post('/{id}/amenities/attach', [RoomController::class, 'attachAmenities']);
        Route::delete('/{id}/amenities/detach', [RoomController::class, 'detachAmenities']);
    });
});


// Amenities
Route::prefix('amenities')->group(function () {
    Route::get('/', [AmenityController::class, 'index']);
    Route::get('/{id}', [AmenityController::class, 'show']);

    Route::middleware('auth:api')->group(function () {
        Route::post('/', [AmenityController::class, 'store']);
        Route::post('/{id}', [AmenityController::class, 'update']);
        Route::delete('/{id}', [AmenityController::class, 'destroy']);
    });
});

// Images
Route::prefix('images')->group(function () {
    Route::get('/', [ImageController::class, 'index']);
    Route::get('/type/{type}', [ImageController::class, 'getImageByType']);
    Route::get('/{id}', [ImageController::class, 'show']);

    Route::middleware('auth:api')->group(function () {
        Route::post('/', [ImageController::class, 'store']);
        Route::post('/{id}', [ImageController::class, 'update']);
        Route::delete('/{id}', [ImageController::class, 'destroy']);
    });
});

// Contacts
Route::prefix('contacts')->group(function () {
    Route::post('/', [ContactController::class, 'store']);

    Route::middleware('auth:api')->group(function () {
        Route::get('/', [ContactController::class, 'index']);
        Route::get('/{id}', [ContactController::class, 'show']);
        Route::delete('/{id}', [ContactController::class, 'destroy']);
    });
});

// Auth
Route::post('/login', [UserController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/register', [UserController::class, 'register']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::delete('/{id}', [UserController::class, 'deleteUser']);
    Route::post('/update/{id}', [UserController::class, 'updateUser']);
    Route::get('/me', [UserController::class, 'me']);
    Route::get('/users', [UserController::class, 'all']);
});


Route::get('/nazels', [NazelController::class, 'index']);
Route::get('/nazels/filter', [NazelController::class, 'filter']);  // filter route
Route::get('/nazels/{id}', [NazelController::class, 'show']);

Route::middleware('auth:api')->group(function () {
    Route::post('/nazels', [NazelController::class, 'store']);
    Route::post('/nazels/{id}', [NazelController::class, 'update']);
    Route::delete('/nazels/{id}', [NazelController::class, 'destroy']);
});

//---------------policies-----------------------------


Route::get('/api/health', function () {
    $room = Room::paginate(1);
    return response()->json([
        'status' => 'OK',
        'message' => 'Your API is running',
        'timestamp' => now()->toISOString(),
        "result" => $room
    ]);
});