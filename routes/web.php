<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HouseholdController;
use App\Http\Controllers\ImmunizationController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- PUBLIC ROUTES (No login required) ---
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'processLogin'])
    ->middleware('throttle:5,1')  // 5 attempts per minute
    ->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/password/forgot', [AuthController::class, 'showForgotPassword'])->name('password.forgot');
Route::post('/password/forgot', [AuthController::class, 'submitForgotPassword'])
    ->middleware('throttle:3,1')  // 3 attempts per minute
    ->name('password.forgot.submit');

// --- PROTECTED ROUTES (Only for logged-in users) ---
Route::middleware('auth')->group(function () {

    // 1. DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. SEARCH API (For AJAX/autocomplete)
    Route::get('/search/patients', [SearchController::class, 'patients'])->name('search.patients');
    Route::get('/search/diagnoses', [SearchController::class, 'diagnoses'])->name('search.diagnoses');
    Route::get('/search/medicines', [SearchController::class, 'medicines'])->name('search.medicines');
    Route::get('/search/households', [SearchController::class, 'households'])->name('search.households');

    // 3. PATIENT MANAGEMENT
    // Households (Census)
    Route::get('/households', [HouseholdController::class, 'index'])
        ->name('households.index')
        ->middleware('role:Admin,BHW,Nurse');
    Route::get('/households/create', [HouseholdController::class, 'create'])
        ->name('households.create')
        ->middleware('role:Admin,BHW,Nurse');
    Route::post('/households', [HouseholdController::class, 'store'])
        ->name('households.store')
        ->middleware('role:Admin,BHW,Nurse');

    // Patients
    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');

    // Create Patient (Order matters: This must be BEFORE {id})
    Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');
    Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');

    // Show Patient Profile (Wildcard catches IDs like 1, 2, 100)
    Route::get('/patients/{id}', [PatientController::class, 'show'])->name('patients.show');

    // 4. CONSULTATION MODULE
    // Consultation History (list) – must be before /consultations/{id}
    Route::get('/consultations', [ConsultationController::class, 'index'])->name('consultations.index');

    // Triage / New Admission
    Route::get('/patients/{id}/consultations/create', [ConsultationController::class, 'create'])->name('consultations.create');
    Route::post('/patients/{id}/consultations', [ConsultationController::class, 'store'])->name('consultations.store');

    // Doctor's Workspace (View specific consultation)
    Route::get('/consultations/{id}', [ConsultationController::class, 'show'])
        ->name('consultations.show')
        ->middleware('role:Admin,Nurse');

    // Doctor Actions (Diagnosis & Rx)
    Route::post('/consultations/{id}/diagnosis', [ConsultationController::class, 'addDiagnosis'])
        ->name('consultations.diagnosis')
        ->middleware('role:Admin,Nurse');
    Route::post('/consultations/{id}/prescription', [ConsultationController::class, 'addPrescription'])
        ->name('consultations.prescription')
        ->middleware('role:Admin,Nurse');

    // 5. IMMUNIZATION
    Route::get('/immunizations', [ImmunizationController::class, 'index'])->name('immunizations.index');
    Route::get('/patients/{id}/immunizations', [ImmunizationController::class, 'forPatient'])->name('immunizations.patient');
    Route::post('/immunizations', [ImmunizationController::class, 'store'])->name('immunizations.store');

    // 6. REPORTS (FHSIS)
    Route::get('/reports', [ReportController::class, 'index'])
        ->name('reports.index')
        ->middleware('role:Admin,BHW,Nurse');
    Route::get('/reports/morbidity', [ReportController::class, 'morbidity'])
        ->name('reports.morbidity')
        ->middleware('role:Admin,BHW,Nurse');
    Route::get('/reports/consultation-summary', [ReportController::class, 'consultationSummary'])
        ->name('reports.consultation-summary')
        ->middleware('role:Admin,BHW,Nurse');

    // 7. USER MANAGEMENT
    Route::get('/users', [UserManagementController::class, 'index'])
        ->name('users.index')
        ->middleware('role:Admin');
    Route::get('/users/create', [UserManagementController::class, 'create'])
        ->name('users.create')
        ->middleware('role:Admin');
    Route::post('/users', [UserManagementController::class, 'store'])
        ->name('users.store')
        ->middleware('role:Admin');
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])
        ->name('users.edit')
        ->middleware('role:Admin');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])
        ->name('users.update')
        ->middleware('role:Admin');
    Route::post('/users/{user}/disable', [UserManagementController::class, 'disable'])
        ->name('users.disable')
        ->middleware('role:Admin');
    Route::post('/users/{user}/enable', [UserManagementController::class, 'enable'])
        ->name('users.enable')
        ->middleware('role:Admin');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])
        ->name('users.destroy')
        ->middleware('role:Admin');

    // 7a. Password reset request admin queue
    Route::get('/users/password-reset-requests', [UserManagementController::class, 'passwordResetRequests'])
        ->name('users.password-reset-requests')
        ->middleware('role:Admin');
    Route::post('/users/password-reset-requests/{passwordResetRequest}/complete', [UserManagementController::class, 'completePasswordResetRequest'])
        ->name('users.password-reset-requests.complete')
        ->middleware('role:Admin');

    // 8. MEDICINE MANAGEMENT
    Route::get('/medicines', [MedicineController::class, 'index'])
        ->name('medicines.index')
        ->middleware('role:Admin,Nurse');
    Route::get('/medicines/create', [MedicineController::class, 'create'])
        ->name('medicines.create')
        ->middleware('role:Admin,Nurse');
    Route::post('/medicines', [MedicineController::class, 'store'])
        ->name('medicines.store')
        ->middleware('role:Admin,Nurse');
    Route::post('/medicines/import', [MedicineController::class, 'import'])
        ->name('medicines.import')
        ->middleware('role:Admin,Nurse');
    Route::get('/medicines/{id}', [MedicineController::class, 'show'])
        ->name('medicines.show')
        ->middleware('role:Admin,Nurse');
    Route::get('/medicines/{id}/edit', [MedicineController::class, 'edit'])
        ->name('medicines.edit')
        ->middleware('role:Admin,Nurse');
    Route::put('/medicines/{id}', [MedicineController::class, 'update'])
        ->name('medicines.update')
        ->middleware('role:Admin,Nurse');
    Route::delete('/medicines/{id}', [MedicineController::class, 'destroy'])
        ->name('medicines.destroy')
        ->middleware('role:Admin');

    // 9. SETTINGS
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('/settings/account', [SettingsController::class, 'account'])->name('settings.account');
    Route::post('/settings/account', [SettingsController::class, 'updateAccount'])->name('settings.account.update');
    Route::get('/settings/backups', [SettingsController::class, 'backups'])
        ->name('settings.backups')
        ->middleware('role:Admin');
    Route::post('/settings/backups/export', [SettingsController::class, 'exportBackup'])
        ->name('settings.backups.export')
        ->middleware('role:Admin');
    Route::post('/settings/backups/import', [SettingsController::class, 'importBackup'])
        ->name('settings.backups.import')
        ->middleware('role:Admin');
    Route::post('/settings/backups/export', [SettingsController::class, 'exportBackup'])
        ->name('settings.backups.export')
        ->middleware('role:Admin');

}); // <--- End of Auth Group
