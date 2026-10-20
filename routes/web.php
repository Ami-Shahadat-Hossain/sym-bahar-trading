<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ChallanController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MoneyReceiptController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // ─────────────────────────────────────────────────────────────
    // Customers
    //   index, create, store, show, edit, update, destroy
    // ─────────────────────────────────────────────────────────────
    Route::resource('customers', CustomerController::class);

    // ─────────────────────────────────────────────────────────────
    // Challans
    //   Custom routes must come BEFORE the resource, otherwise
    //   `challans/bulk` would be matched by `challans/{challan}`.
    // ─────────────────────────────────────────────────────────────
    Route::get('challans/{challan}/print-a4', [ChallanController::class, 'printA4'])->name('challans.print-a4');
    Route::get('challans/{challan}/print-pad', [ChallanController::class, 'printPad'])->name('challans.print-pad');
    Route::delete('challans/bulk', [ChallanController::class, 'bulkDestroy'])->name('challans.bulk-destroy');
    Route::resource('challans', ChallanController::class);

    // ─────────────────────────────────────────────────────────────
    // Invoices
    //   Same rule: static paths before the resource.
    // ─────────────────────────────────────────────────────────────
    Route::post('invoices/select-challans', [InvoiceController::class, 'selectChallans'])->name('invoices.select-challans');
    Route::get('invoices/preview-no', [InvoiceController::class, 'previewNo'])->name('invoices.preview-no');
    Route::get('invoices/{invoice}/print-a4', [InvoiceController::class, 'printA4'])->name('invoices.print-a4');
    Route::get('invoices/{invoice}/print-pad', [InvoiceController::class, 'printPad'])->name('invoices.print-pad');
    Route::resource('invoices', InvoiceController::class);

    // ─────────────────────────────────────────────────────────────
    // Receipts
    //   Resource generates:
    //     GET    receipts                   → receipts.index
    //     GET    receipts/create            → receipts.create
    //     POST   receipts                   → receipts.store
    //     GET    receipts/{receipt}         → receipts.show
    //     GET    receipts/{receipt}/edit    → receipts.edit      ← auto
    //     PUT    receipts/{receipt}         → receipts.update    ← auto
    //     PATCH  receipts/{receipt}         → receipts.update
    //     DELETE receipts/{receipt}         → receipts.destroy
    // ─────────────────────────────────────────────────────────────
    Route::get('receipts/{receipt}/print-a4', [MoneyReceiptController::class, 'printA4'])->name('receipts.print-a4');
    Route::get('receipts/{receipt}/print-pad', [MoneyReceiptController::class, 'printPad'])->name('receipts.print-pad');
    Route::resource('receipts', MoneyReceiptController::class);
});

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
