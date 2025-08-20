<?php

use Illuminate\Support\Facades\Auth;

if (! function_exists('hasRole')) {
    function hasRole($roles): bool
    {
        return in_array(Auth::user()->role->name, (array) $roles);
    }
}
