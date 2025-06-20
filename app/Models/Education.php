<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Education extends Model
{
    protected $fillable = [
        'institution_name',
        'degree',
        'field_of_study',
        'start_year',
        'end_year',
        'is_current',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'start_year' => 'integer',
        'end_year' => 'integer',
        'is_current' => 'boolean',
        'is_active' => 'boolean'
    ];

    // Duration calculator
    public function getDurationAttribute()
    {
        $startYear = $this->start_year;
        $endYear = $this->is_current ? date('Y') : $this->end_year;
        
        if (!$endYear || !$startYear) return null;
        
        $years = $endYear - $startYear;
        
        if ($years > 0) {
            return $years . ' yıl';
        } else {
            return '1 yıl';
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
        return $query->orderBy('sort_order', 'asc')->orderBy('start_year', 'desc');
    }
}
