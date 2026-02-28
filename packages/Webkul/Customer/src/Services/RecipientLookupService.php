<?php

namespace Webkul\Customer\Services;

use Webkul\Customer\Models\Customer;

class RecipientLookupService
{
    /**
     * Look up a customer by alias or Credits ID.
     */
    public function find(string $identifier): ?Customer
    {
        $identifier = trim($identifier);

        if (empty($identifier)) {
            return null;
        }

        // Check if it's an alias (starts with @)
        if (str_starts_with($identifier, '@')) {
            $alias = ltrim($identifier, '@');
            return Customer::where('username', $alias)->first();
        }

        // Check if it's a Credits ID (e.g., M-A1B2C3D4E5)
        if (str_contains($identifier, '-')) {
            return Customer::where('credits_id', $identifier)->first();
        }

        // Fallback: try direct alias lookup without @
        return Customer::where('username', $identifier)->first();
    }
}
