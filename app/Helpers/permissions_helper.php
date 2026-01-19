<?php

/**
 * Helper de Permisos (RBAC)
 */

if (!function_exists('can')) {

    function can(string $permission): bool
    {
        static $cachedPermissions = null;

        if ($cachedPermissions === null) {

            if (!session()->get('logged')) {
                $cachedPermissions = [];
                return false;
            }

            $permissions = session()->get('permissions');
            $cachedPermissions = is_array($permissions) ? $permissions : [];
        }

        // Super admin
        if (in_array('*', $cachedPermissions, true)) {
            return true;
        }

        return in_array($permission, $cachedPermissions, true);
    }
}

if (!function_exists('canAny')) {

    function canAny(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (can($permission)) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('canAll')) {

    function canAll(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!can($permission)) {
                return false;
            }
        }
        return true;
    }
}

if (!function_exists('deny')) {

    function deny(string $permission): void
    {
        if (!can($permission)) {
            throw new \CodeIgniter\Exceptions\PageForbiddenException();
        }
    }
}

if (!function_exists('denyAny')) {

    function denyAny(array $permissions): void
    {
        if (!canAny($permissions)) {
            throw new \CodeIgniter\Exceptions\PageForbiddenException();
        }
    }
}
