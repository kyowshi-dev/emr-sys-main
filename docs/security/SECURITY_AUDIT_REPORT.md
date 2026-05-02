# Security Audit Report - BHCIS Laravel Application

**Date:** March 31, 2026  
**Status:** ⚠️ **CRITICAL & HIGH PRIORITY VULNERABILITIES IDENTIFIED**

---

## Executive Summary

The application has several critical security vulnerabilities that need immediate attention:

- **Passwords not being hashed** (Critical)
- **Insecure Direct Object References (IDOR)** (High)
- **Missing authorization checks on resources** (High)
- **Insufficient input validation on file uploads** (Medium)
- **Missing rate limiting** (Medium)
- **Session continuation/timeout issues** (Medium)

---

## Detailed Vulnerability Analysis

### 1. ❌ CRITICAL: Passwords Not Being Hashed

**Severity:** CRITICAL  
**Location:**

- `app/Http/Controllers/UserManagementController.php` (Lines 45, 120)
- Users' passwords are stored in plain text in the database

**Code Issue:**

```php
// ❌ WRONG - Line 45 in store()
'password' => $validated['password'],  // Should be HASHED!

// ❌ WRONG - Line 120 in update()
if (! empty($validated['password'])) {
    $user->password = $validated['password'];  // Should be HASHED!
}
```

**Impact:**

- If database is compromised, all user passwords are exposed
- Users can reset credentials directly in DB without knowing original password
- Violates HIPAA/healthcare compliance requirements

**Fix Required:**

```php
use Illuminate\Support\Facades\Hash;

// ✅ CORRECT
'password' => Hash::make($validated['password']),
if (! empty($validated['password'])) {
    $user->password = Hash::make($validated['password']);
}
```

---

### 2. ⚠️ HIGH: Insecure Direct Object References (IDOR)

**Severity:** HIGH  
**Affected Endpoints:**

| Endpoint                      | Issue                                       | Risk                                |
| ----------------------------- | ------------------------------------------- | ----------------------------------- |
| `/patients/{id}`              | No authorization check                      | View any patient's data             |
| `/consultations/{id}`         | Auth middleware present, but no owner check | Access unauthorized consultations   |
| `/medicines/{id}`             | Generic access                              | Expose all medicines                |
| `/medicines/{id}/edit`        | Generic access                              | Modify any medicine                 |
| `/users/{user}/edit`          | Only Admin role checked                     | Edit any user as admin              |
| `/users/{user}` (PUT/DELETE)  | Only Admin role checked                     | Delete any user as admin            |
| `/immunizations/patient/{id}` | No authorization check                      | View patient's immunization records |

**Code Example (PatientController.php):**

```php
// ❌ VULNERABILITY - No check if current user can access this patient
public function show($id)
{
    $patient = DB::table('patients')
        ->join('households', 'patients.household_id', '=', 'households.id')
        ->where('patients.id', $id)
        ->first();

    if (! $patient) {
        abort(404, 'Patient not found');
    }
    // NO CHECK: Is current user authorized to view this patient?
    return view('patients.show', compact('patient'));
}
```

**Recommended Fix:**

```php
// ✅ SOLUTION: Add authorization check
public function show($id)
{
    $patient = DB::table('patients')
        ->join('households', 'patients.household_id', '=', 'households.id')
        ->where('patients.id', $id)
        ->first();

    if (! $patient) {
        abort(404, 'Patient not found');
    }

    // Check if user is authorized to view this patient
    if (! auth()->user()->hasRole('Admin') && ! auth()->user()->hasRole('Nurse')) {
        abort(403, 'You are not authorized to view this patient.');
    }

    return view('patients.show', compact('patient'));
}
```

---

### 3. ⚠️ HIGH: Missing Authorization in Related Resource Operations

**Severity:** HIGH  
**Issue:** Many operations only check the user's role, not if they can access that specific resource.

**Examples:**

#### UserManagementController

```php
// ❌ VULNERABLE - Admin can modify ANY user, including other admins
public function update(Request $request, User $user)
{
    // Only checks role:Admin, does NOT check if user can edit this specific user
    // Missing: Check if editing yourself is restricted, etc.
}

// ❌ VULNERABLE - Can disable another admin
public function disable(User $user)
{
    if (! $user->is_active) { ... }
    $user->is_active = false;
    $user->save();
    // Missing: Prevent disabling the only admin
}
```

**Recommended Checks:**

- Add unique constraint checks to prevent orphaning critical roles
- Prevent users from accidentally disabling all admins
- Log who disables/enables users
- Add confirmation requirements for destructive operations

---

### 4. ⚠️ MEDIUM: Insufficient File Upload Validation

**Severity:** MEDIUM  
**Location:** `MedicineController.php` - `import()` method

**Issues:**

```php
// ⚠️ Only validates file type and size, not content
$request->validate([
    'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
]);

// ⚠️ Trusts CSV headers blindly
if (($handle = fopen($path, 'r')) !== false) {
    $header = fgetcsv($handle, 1000, ',');
    // No validation of header format
    // No max rows check
    // No duplicate detection across multiple uploads
}
```

**Risks:**

- Malformed CSV could cause database errors
- No rate limiting on import operations
- No audit trail of imports
- Duplicate medicines could be inserted with slightly different names

**Recommendations:**

- Validate header format explicitly
- Limit number of rows per import
- Add import audit logging
- Case-insensitive duplicate check
- Add import history tracking

---

### 5. ⚠️ MEDIUM: No Rate Limiting on Authentication

**Severity:** MEDIUM  
**Issue:** No rate limiting on login endpoints

**Vulnerable Endpoints:**

- `POST /login` - Brute force attack possible
- `POST /password/forgot` - Enumeration/DOS attack possible

**Current Code (AuthController.php):**

```php
// ❌ No rate limiting
public function processLogin(Request $request)
{
    $credentials = $request->validate([...]);
    if (Auth::attempt([...], $remember)) {
        // Allow unlimited login attempts
    }
}
```

**Recommendation:**
Add rate limiting in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    ]);
})
```

Then in routes:

```php
Route::post('/login', [AuthController::class, 'processLogin'])
    ->middleware('throttle:5,1')  // 5 attempts per minute
    ->name('login.process');

Route::post('/password/forgot', [AuthController::class, 'submitForgotPassword'])
    ->middleware('throttle:3,1')  // 3 attempts per minute
    ->name('password.forgot.submit');
```

---

### 6. ⚠️ MEDIUM: Password Reset Token Security

**Severity:** MEDIUM  
**Location:** `AuthController.php` - `submitForgotPassword()`

**Issues:**

```php
// ⚠️ Creates request but doesn't generate secure token
$requestRecord = \App\Models\PasswordResetRequest::create([
    'user_id' => $user?->id,
    'username_requested' => $validated['username'],
    'status' => 'pending',
]);

// ⚠️ Returns user_id directly to DB - not using secure tokens
// If user tries reset for non-existent user, it still succeeds (info disclosure)
```

**Risks:**

- No unique token generated for secure verification
- Admin can see which users requested reset without verification
- Timing attack possible (different response for existing vs non-existing users)

**Recommendation:**

```php
public function submitForgotPassword(Request $request)
{
    $validated = $request->validate([...]);

    $user = \App\Models\User::where('username', $validated['username'])->first();

    // ✅ Same response for existing and non-existing users
    // Create reset record with secure token
    if ($user) {
        $token = Str::random(64);

        \App\Models\PasswordResetRequest::create([
            'user_id' => $user->id,
            'token' => $token,
            'status' => 'pending',
            'expires_at' => now()->addHours(24),
        ]);
    }

    return redirect()->route('login')
        ->with('success', 'If account exists, password reset instructions have been sent.');
}
```

---

### 7. ⚠️ MEDIUM: Missing Audit Logging

**Severity:** MEDIUM  
**Issue:** No audit trail for sensitive operations

**Missing Logs:**

- User login/logout
- User creation/modification/deletion
- Password changes
- Database backups/exports
- Medicine imports
- Patient data access
- Consultation modifications

**Recommendation:**
Create an audit log system:

```php
DB::table('audit_logs')->insert([
    'user_id' => auth()->id(),
    'action' => 'patient_viewed',
    'resource_id' => $patient->id,
    'resource_type' => 'Patient',
    'ip_address' => request()->ip(),
    'user_agent' => request()->userAgent(),
    'timestamp' => now(),
]);
```

---

### 8. ⚠️ MEDIUM: Session Configuration Review Needed

**Severity:** MEDIUM  
**Location:** `config/session.php`

**Recommendations:**

- Set `'lifetime' => 2` (20 minutes for healthcare app)
- Ensure `'encrypt' => true` is set
- Verify `'secure' => true` for production (HTTPS only)
- Verify `'http_only' => true` (prevents JS access to cookies)

---

### 9. ⚠️ MEDIUM: Database Backup Access Control

**Severity:** MEDIUM  
**Location:** `SettingsController.php` - `exportBackup()`

**Issue:**

```php
// ⚠️ Only checks role:Admin, but doesn't log who exports
public function exportBackup(Request $request)
{
    // Exports entire database including user passwords (if not hashed)
    // No export logging
    // No rate limiting
}
```

**Recommendations:**

- Log all backup exports with timestamp and user
- Require password confirmation before export
- Limit backup exports to once per hour
- Encrypt exported backups

---

### 10. ⚠️ LOW-MEDIUM: Search Endpoints - SQL Injection Risk

**Severity:** LOW-MEDIUM  
**Location:** Various search endpoints in `SearchController.php`

**Issue:**

```php
// ⚠️ Using LIKE with user input (escaped by Laravel, but verify)
$patients = DB::table('patients')
    ->where(function ($qb) use ($query) {
        $qb->where('last_name', 'LIKE', "{$query}%")  // Safe due to parameterization
            ->orWhere('first_name', 'LIKE', "{$query}%");
    })
```

**Status:** Laravel query builder parameterizes these by default, so it's safe. No immediate fix needed, but good to audit.

---

### 11. ⚠️ LOW: XSS - Output Escaping Verification Needed

**Severity:** LOW  
**Location:** All blade templates

**Recommendations:**

- Ensure all `{{ }}` are used for escaped output (not `{!! !!}`)
- Verify patient data, consultation notes, etc. are properly escaped
- Use `@xss` directives where raw HTML is needed

**Example:**

```blade
{{-- ✅ CORRECT - Escaped --}}
<h1>{{ $patient->first_name }}</h1>

{{-- ❌ DANGEROUS - Raw HTML (only use if necessary) --}}
<p>{!! $consultation->notes !!}</p>
```

---

### 12. ⚠️ LOW: Exception Handling May Expose Data

**Severity:** LOW  
**Location:** Controllers returning 404/403 errors

**Issue:**

```php
// ⚠️ Error message might reveal resource exists
abort(404, 'Patient not found');  // Confirms that patient/ID doesn't exist
```

**Recommendation:**
Use generic error messages in production:

```php
if (! $patient) {
    abort(404, 'Resource not found');
}
```

---

## Summary of Required Fixes

### CRITICAL (Fix Immediately)

- [ ] Hash all user passwords using `Hash::make()`
- [ ] Fix `UserManagementController` store/update methods

### HIGH (Fix This Week)

- [ ] Add authorization checks to all endpoints accessing patient/consultation/user data
- [ ] Implement resource-level authorization (not just role-based)
- [ ] Add IDOR tests to prevent regression

### MEDIUM (Fix Within 2 Weeks)

- [ ] Add rate limiting to login and password reset endpoints
- [ ] Implement audit logging for sensitive operations
- [ ] Improve file upload validation
- [ ] Implement secure password reset token system
- [ ] Add backup export logging and restrictions

### LOW (Fix Within 1 Month)

- [ ] Audit blade templates for XSS
- [ ] Review session configuration
- [ ] Implement generic error messages
- [ ] Add input sanitization where needed

---

## Testing Recommendations

Create security tests for:

```php
// Test that user cannot view other patients
public function testCannotViewOtherPatient()
{
    $user = User::factory()->create(['role_id' => 2]); // BHW
    $patient1 = Patient::factory()->create();
    $patient2 = Patient::factory()->create();

    $this->actingAs($user)
        ->get("/patients/{$patient2->id}")
        ->assertForbidden(); // Should fail if no authorization
}

// Test that passwords are hashed
public function testPasswordIsHashed()
{
    $this->post('/users', [
        'username' => 'testuser',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        // ... other fields
    ]);

    $user = User::where('username', 'testuser')->first();
    $this->assertNotEquals('password123', $user->password); // Should be hashed
}

// Test rate limiting on login
public function testLoginRateLimiting()
{
    for ($i = 0; $i < 6; $i++) {
        $response = $this->post('/login', [
            'username' => 'user@test.com',
            'password' => 'wrong',
        ]);

        if ($i >= 5) {
            $this->assertEquals(429, $response->status()); // Too Many Requests
        }
    }
}
```

---

## Additional Recommendations

1. **Implement Laravel Policies** for resource authorization
2. **Use Form Requests** for centralized validation
3. **Add API rate limiting** on search endpoints
4. **Implement soft deletes** instead of hard deletes for data retention
5. **Regular security audits** and penetration testing
6. **Keep dependencies updated** (`composer update`, `npm update`)
7. **Configure CORS** properly if used
8. **Implement CSRF protection** explicitly (Laravel does by default)
9. **Add security headers** (X-Frame-Options, X-Content-Type-Options, etc.)

---

## References

- [Laravel Security Best Practices](https://laravel.com/docs/11/security)
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [CWE-639: Authorization Bypass Through User-Controlled Key](https://cwe.mitre.org/data/definitions/639.html)
- [Healthcare Data Security (HIPAA)](https://www.hhs.gov/hipaa/index.html)

---

**Report Generated:** March 31, 2026  
**Next Review:** Immediately after fixes are applied
