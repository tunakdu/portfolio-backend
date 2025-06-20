<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Experience extends Model
{
    protected $fillable = [
        'company_name',
        'position',
        'description',
        'start_date',
        'end_date',
        'is_current',
        'location',
        'company_website',
        'technologies',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'is_active' => 'boolean',
        'technologies' => 'array'
    ];

    // Duration calculator
    public function getDurationAttribute()
    {
        $start = $this->start_date;
        $end = $this->is_current ? Carbon::now() : $this->end_date;
        
        if (!$end) return null;
        
        $months = $start->diffInMonths($end);
        $years = floor($months / 12);
        $remainingMonths = $months % 12;
        
        if ($years > 0 && $remainingMonths > 0) {
            return $years . ' yıl ' . $remainingMonths . ' ay';
        } elseif ($years > 0) {
            return $years . ' yıl';
        } else {
            return $remainingMonths . ' ay';
        }
    }

    // Scope for active records
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for ordered records
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('start_date', 'desc');
    }
}
