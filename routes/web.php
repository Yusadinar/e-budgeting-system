<?php
// routes/web.php

use App\Http\Controllers\ApprovalController;

use App\Http\Controllers\BudgetUploadController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\SuperAdmin as SA;
use App\Http\Controllers\Director as Dir;
use Illuminate\Support\Facades\Route;

// ============================================================
// PUBLIC ROUTES
// ============================================================

// Redirect root ke dashboard (jika sudah login) atau login
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// ============================================================
// AUTH ROUTES — Bawaan Laravel Breeze
// ============================================================
require __DIR__.'/auth.php';

// ============================================================
// QR CODE APPROVAL — Public (tidak butuh login)
// Approver bisa scan QR dari email/print tanpa harus login dulu
// ============================================================
Route::get('/approval/qr/{token}', [ApprovalController::class, 'approveByQr'])
    ->name('approval.qr')
    ->middleware('signed'); // URL harus ditandatangani agar tidak bisa dimanipulasi



// ============================================================
// PROTECTED ROUTES — Wajib login & email verified
// ============================================================
Route::middleware(['auth', 'verified'])->group(function () {

    // ----------------------------------------------------------
    // DASHBOARD
    // ----------------------------------------------------------
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/budget-logs', [\App\Http\Controllers\BudgetLogController::class, 'index'])
        ->name('budget-logs.index');

    // Director monitoring dashboard
    Route::get('/director/dashboard', [Dir\DashboardController::class, 'index'])
        ->name('director.dashboard')
        ->middleware('role:man_dir,fin_dir,prod_dir,pres_dir');

    // Director department monitoring
    Route::prefix('director/departments')
        ->name('director.departments.')
        ->middleware('role:man_dir,fin_dir,prod_dir,pres_dir')
        ->group(function () {
            Route::get('/', [Dir\DepartmentController::class, 'index'])->name('index');
            Route::get('/{department}', [Dir\DepartmentController::class, 'show'])->name('show');
        });



    // ----------------------------------------------------------
    // BUDGET UPLOAD (Excel Template — Ka.Dept only)
    // ----------------------------------------------------------
    Route::prefix('budget-upload')->name('budget.upload.')->group(function () {
        Route::get('/',         [BudgetUploadController::class, 'index'])->name('index');
        Route::post('/parse',   [BudgetUploadController::class, 'parse'])->name('parse');
        Route::post('/store',   [BudgetUploadController::class, 'store'])->name('store');
        Route::get('/{budgetUpload}', [BudgetUploadController::class, 'show'])->name('show');
    });

    // ----------------------------------------------------------
    // PROFILE — Bawaan Breeze, tidak perlu diubah route-nya
    // ----------------------------------------------------------
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    // ----------------------------------------------------------
    // MENU PENGAJUAN
    // Alur: PPBJ → Proposal Harga → Internal Agreement
    // ----------------------------------------------------------
    Route::prefix('pengajuan')->name('pengajuan.')->group(function () {

        // List semua pengajuan milik user
        Route::get('/', [PengajuanController::class, 'index'])
            ->name('index');

        // Step 1 — PPBJ
        Route::get('/buat', [PengajuanController::class, 'createPpbj'])
            ->name('create-ppbj');

        Route::post('/buat', [PengajuanController::class, 'storePpbj'])
            ->name('store-ppbj');

        // Detail PPBJ (printable document view)
        Route::get('/{ppbj}/detail', [PengajuanController::class, 'showPpbj'])
            ->name('show-ppbj');

        // Batalkan / Hapus PPBJ
        Route::delete('/{ppbj}', [PengajuanController::class, 'destroyPpbj'])
            ->name('destroy-ppbj');

        // Step 2 — Proposal Harga
        // {ppbj} = ID PPBJ yang baru dibuat di step 1
        Route::get('/{ppbj}/proposal-harga', [\App\Http\Controllers\ProposalHargaController::class, 'create'])
            ->name('create-ph');

        Route::post('/{ppbj}/proposal-harga', [\App\Http\Controllers\ProposalHargaController::class, 'store'])
            ->name('store-ph');
            
        Route::get('/proposal-harga/{proposalHarga}/print', [\App\Http\Controllers\ProposalHargaController::class, 'print'])
            ->name('print-ph');

        // Step 3 — Internal Agreement
        // {proposalHarga} = ID PH yang baru dibuat di step 2
        Route::get('/{proposalHarga}/internal-agreement', [PengajuanController::class, 'createIa'])
            ->name('create-ia');

        Route::post('/{proposalHarga}/internal-agreement', [PengajuanController::class, 'storeIa'])
            ->name('store-ia');

        // Print IA (Printable Document View)
        Route::get('/internal-agreement/{ia}/print', [PengajuanController::class, 'printIa'])
            ->name('print-ia');
    });

    // ----------------------------------------------------------
    // MENU TRACKING
    // ----------------------------------------------------------
    Route::prefix('tracking')->name('tracking.')->group(function () {

        // List semua pengajuan (filter by role otomatis di controller)
        Route::get('/', [TrackingController::class, 'index'])
            ->name('index');

        // Detail pengajuan + timeline
        Route::get('/{ppbj}', [TrackingController::class, 'show'])
            ->name('show');
    });

    // ----------------------------------------------------------
    // APPROVAL
    // ----------------------------------------------------------
    Route::prefix('approval')->name('approval.')->group(function () {
        // PPBJ
        Route::post('/ppbj/{ppbj}/approve', [ApprovalController::class, 'approvePpbj'])->name('ppbj.approve');
        Route::post('/ppbj/{ppbj}/reject', [ApprovalController::class, 'rejectPpbj'])->name('ppbj.reject');

        // Proposal Harga
        Route::post('/ph/{ph}/approve', [ApprovalController::class, 'approvePh'])->name('ph.approve');
        Route::post('/ph/{ph}/reject', [ApprovalController::class, 'rejectPh'])->name('ph.reject');

        // Internal Agreement
        Route::post('/ia/{ia}/approve', [ApprovalController::class, 'approveIa'])->name('ia.approve');
        Route::post('/ia/{ia}/reject', [ApprovalController::class, 'rejectIa'])->name('ia.reject');
    });

    // ----------------------------------------------------------
    // API: Cost Center (Async Dropdown)
    // ----------------------------------------------------------
    Route::prefix('api/cost-centers')->name('api.cost-centers.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\CostCenterController::class, 'index'])->name('index');
        Route::get('/departments', [\App\Http\Controllers\Api\CostCenterController::class, 'departments'])->name('departments');
        Route::get('/budget-info', [\App\Http\Controllers\Api\CostCenterController::class, 'budgetInfo'])->name('budget-info');
    });

    // ----------------------------------------------------------
    // SUPERADMIN PANEL
    // ----------------------------------------------------------
    Route::prefix('superadmin')
        ->name('superadmin.')
        ->middleware('role:superadmin')
        ->group(function () {
            Route::get('/', [SA\DashboardController::class, 'index'])->name('dashboard');
            Route::resource('users', SA\UserController::class)->except(['show']);
            Route::resource('departments', SA\DepartmentController::class)->except(['show']);
            Route::get('budget', [SA\BudgetOverviewController::class, 'index'])->name('budget.index');
            Route::get('budget/{department}', [SA\BudgetOverviewController::class, 'show'])->name('budget.show');
            Route::get('audit', [SA\AuditLogController::class, 'index'])->name('audit.index');

            // Cost Center Management
            Route::resource('cost-centers', SA\CostCenterController::class)->except(['show']);
        });

});