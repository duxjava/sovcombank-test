<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Sortable
{
    /**
     * Scope a query to sort results.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSort(Builder $query, Request $request)
    {
        // Get sortable column
        $sortables = data_get($this, 'sortables', []);

        // Get the column to sort
        $sort = $request->get('sort', '-created_at');
        $column = preg_replace('/-/', '', $sort);

        // Ensure column to sort is part of model's sortables property
        // and that the direction is a valid value
        if ($sort && in_array($column, $sortables)) {
            // Return ordered query
            return $query->orderBy($column, str_contains($sort, '-') ? 'DESC' : 'ASC');
        }

        // No sorting, return query
        return $query;
    }
}