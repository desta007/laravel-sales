<?php

use App\Http\Controllers\TokoBarcodeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/toko/{toko}/print-barcode', [TokoBarcodeController::class, 'print'])->name('toko.print-barcode');
Route::get('/tokos/print-barcodes', [TokoBarcodeController::class, 'printBulk'])->name('toko.print-barcodes-bulk');
