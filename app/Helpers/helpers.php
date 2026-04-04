<?php

use Illuminate\Support\Facades\Auth;

/**
 * Get the authenticated user instance
 */
function user(): ?\App\Models\User {
    return Auth::user();
}
