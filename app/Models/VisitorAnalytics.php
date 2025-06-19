<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class VisitorAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip_address',
        'user_agent',
        'page_url',
        'referrer',
        'country',
        'city',
        'device_type',
        'browser',
        'os',
        'session_duration',
        'utm_parameters',
        'is_bot',
        'session_id',
        'visited_at',
    ];

    protected $casts = [
        'utm_parameters' => 'array',
        'is_bot' => 'boolean',
        'visited_at' => 'datetime',
    ];

    // Analytics helper methods
    public static function getTodayVisitors()
    {
        return static::whereDate('visited_at', today())
            ->where('is_bot', false)
            ->distinct('ip_address')
            ->count('ip_address');
    }

    public static function getTotalVisitors()
    {
        return static::where('is_bot', false)
            ->distinct('ip_address')
            ->count('ip_address');
    }

    public static function getTodayPageViews()
    {
        return static::whereDate('visited_at', today())
            ->where('is_bot', false)
            ->count();
    }

    public static function getTotalPageViews()
    {
        return static::where('is_bot', false)->count();
    }

    public static function getTopPages($limit = 5)
    {
        return static::where('is_bot', false)
            ->selectRaw('page_url, COUNT(*) as views')
            ->groupBy('page_url')
            ->orderBy('views', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getTopCountries($limit = 5)
    {
        return static::where('is_bot', false)
            ->whereNotNull('country')
            ->selectRaw('country, COUNT(DISTINCT ip_address) as visitors')
            ->groupBy('country')
            ->orderBy('visitors', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getVisitorsByDate($days = 7)
    {
        return static::where('is_bot', false)
            ->where('visited_at', '>=', Carbon::now()->subDays($days))
            ->selectRaw('DATE(visited_at) as date, COUNT(DISTINCT ip_address) as visitors, COUNT(*) as page_views')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public static function getDeviceStats()
    {
        return static::where('is_bot', false)
            ->whereNotNull('device_type')
            ->selectRaw('device_type, COUNT(DISTINCT ip_address) as visitors')
            ->groupBy('device_type')
            ->get();
    }

    public static function getBrowserStats()
    {
        return static::where('is_bot', false)
            ->whereNotNull('browser')
            ->selectRaw('browser, COUNT(DISTINCT ip_address) as visitors')
            ->groupBy('browser')
            ->orderBy('visitors', 'desc')
            ->limit(5)
            ->get();
    }

    public static function getReferrerStats()
    {
        return static::where('is_bot', false)
            ->whereNotNull('referrer')
            ->where('referrer', '!=', '')
            ->selectRaw('referrer, COUNT(*) as visits')
            ->groupBy('referrer')
            ->orderBy('visits', 'desc')
            ->limit(5)
            ->get();
    }
}
