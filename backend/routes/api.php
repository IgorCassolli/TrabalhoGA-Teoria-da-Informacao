<?php

use App\Http\Controllers\DecoderController;
use App\Http\Controllers\EncoderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/encode-golomb', [EncoderController::class, 'encodeByGolomb']);
Route::post('/encode-elias-gamma', [EncoderController::class, 'encodeByEliasGamma']);
Route::post('/encode-zeckendorf', [EncoderController::class, 'encodeByFibonacciZeckendorf']);
Route::post('/encode-huffman', [EncoderController::class, 'encodeByHuffman']);

Route::post('/decode-golomb', [DecoderController::class, 'decodeByGolomb']);
Route::post('/decode-elias-gamma', [DecoderController::class, 'decodeByEliasGamma']);
Route::post('/decode-zeckendorf', [DecoderController::class, 'decodeByFibonacciZeckendorf']);
Route::post('/decode-huffman', [DecoderController::class, 'decodeByHuffman']);
