<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ReferenceNumberGenerator
{
    /**
     * Generate a unique reference number for a given table and prefix.
     * Example: STZ-PROJ-000001
     */
    public static function generate(string $table, string $prefix, string $column = 'reference_number', int $digits = 6): string
    {
        return DB::transaction(function () use ($table, $prefix, $column, $digits) {
            $lastRecord = DB::table($table)
                ->where($column, 'like', "{$prefix}-%")
                ->orderBy('id', 'desc')
                ->first();

            $nextSequence = 1;

            if ($lastRecord && ! empty($lastRecord->{$column})) {
                $parts = explode('-', $lastRecord->{$column});
                $lastNumber = (int) end($parts);
                $nextSequence = $lastNumber + 1;
            }

            $numberStr = str_pad((string) $nextSequence, $digits, '0', STR_PAD_LEFT);
            $candidate = "{$prefix}-{$numberStr}";

            // Ensure absolute uniqueness
            while (DB::table($table)->where($column, $candidate)->exists()) {
                $nextSequence++;
                $numberStr = str_pad((string) $nextSequence, $digits, '0', STR_PAD_LEFT);
                $candidate = "{$prefix}-{$numberStr}";
            }

            return $candidate;
        });
    }
}
