<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Filters\QueryFilters;

class EmployeeStore extends Model
{
    //
    protected $fillable = [
        'user_id', 'store_id', 
    ];

	/* Metode tambahan untuk model Branch Sport. */

	/**
     * Relation Method(s).
     *
     */

	public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function store()
    {
        return $this->belongsTo('App\Store', 'store_id');
    }

	/**
     * Filtering Berdasarakan Request User
     * @param $query
     * @param QueryFilters $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, QueryFilters $filters)
    {
        return $filters->apply($query);
    }
}
