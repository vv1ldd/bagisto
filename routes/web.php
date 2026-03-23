<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NftMetadataController;

Route::get('/api/nft/metadata/{id}.json', [NftMetadataController::class, 'show']);
Route::get('/api/nft/image/{id}.svg', [NftMetadataController::class, 'image']);
