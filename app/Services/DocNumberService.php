<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DocNumberService
{
    public static function next(string $prefix, string $table, string $column): string
    {
        $year = now()->format('Y');

        $latest = DB::table($table)
            ->where($column, 'like', "{$prefix}-{$year}-%")
            ->orderBy($column, 'desc')
            ->value($column);

        $nextSeq = 1;
        if ($latest) {
            $parts = explode('-', $latest);
            $nextSeq = ((int) end($parts)) + 1;
        }

        return sprintf('%s-%s-%06d', $prefix, $year, $nextSeq);
    }
}
