<?php

namespace App\Filament\Support;

use App\Models\Admin;

/**
 * Centralises access to the authenticated Filament panel user and
 * any role-based checks that are needed across resources, pages,
 * tables, schemas and widgets.
 */
class AuthContext
{
    public static function admin(): ?Admin
    {
        /** @var Admin|null */
        return filament()->auth()->user();
    }

    public static function isAgent(): bool
    {
        return (bool) static::admin()?->hasRole('agent');
    }

    public static function isAccountant(): bool
    {
        return (bool) static::admin()?->hasRole('accountant');
    }
}