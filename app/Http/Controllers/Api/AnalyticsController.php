<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VisitorAnalytics;
use App\Models\Article;
use App\Models\Project;
use App\Models\Message;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Get dashboard overview statistics
     */
    public function overview()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $lastWeek = Carbon::now()->subDays(7);
        $lastMonth = Carbon::now()->subDays(30);

        // Today's stats
        $todayVisitors = VisitorAnalytics::getTodayVisitors();
        $todayPageViews = VisitorAnalytics::getTodayPageViews();
        
        // Yesterday's stats for comparison
        $yesterdayVisitors = VisitorAnalytics::whereDate('visited_at', $yesterday)
            ->where('is_bot', false)
            ->distinct('ip_address')
            ->count('ip_address');
            
        $yesterdayPageViews = VisitorAnalytics::whereDate('visited_at', $yesterday)
            ->where('is_bot', false)
            ->count();

        // Total stats
        $totalVisitors = VisitorAnalytics::getTotalVisitors();
        $totalPageViews = VisitorAnalytics::getTotalPageViews();
        
        // Content stats
        $totalArticles = Article::count();
        $publishedArticles = Article::where('is_published', true)->count();
        $totalProjects = Project::count();
        $totalMessages = Message::count();
        
        // Recent messages
        $recentMessages = Message::latest()->limit(5)->get(['name', 'email', 'subject', 'created_at']);

        return response()->json([
            'visitors' => [
                'today' => $todayVisitors,
                'yesterday' => $yesterdayVisitors,
                'total' => $totalVisitors,
                'change_percent' => $yesterdayVisitors > 0 ? 
                    round((($todayVisitors - $yesterdayVisitors) / $yesterdayVisitors) * 100, 1) : 0
            ],
            'page_views' => [
                'today' => $todayPageViews,
                'yesterday' => $yesterdayPageViews,
                'total' => $totalPageViews,
                'change_percent' => $yesterdayPageViews > 0 ? 
                    round((($todayPageViews - $yesterdayPageViews) / $yesterdayPageViews) * 100, 1) : 0
            ],
            'content' => [
                'articles' => [
                    'total' => $totalArticles,
                    'published' => $publishedArticles,
                    'draft' => $totalArticles - $publishedArticles
                ],
                'projects' => $totalProjects,
                'messages' => $totalMessages
            ],
            'recent_messages' => $recentMessages
        ]);
    }

    /**
     * Get visitor analytics chart data
     */
    public function visitorsChart(Request $request)
    {
        $days = $request->get('days', 7); // Default 7 days
        $data = VisitorAnalytics::getVisitorsByDate($days);
        
        // Fill missing dates with zero
        $chartData = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $found = $data->firstWhere('date', $date);
            
            $chartData[] = [
                'date' => $date,
                'visitors' => $found ? $found->visitors : 0,
                'page_views' => $found ? $found->page_views : 0,
                'formatted_date' => Carbon::parse($date)->format('M d')
            ];
        }

        return response()->json($chartData);
    }

    /**
     * Get top pages statistics
     */
    public function topPages()
    {
        $pages = VisitorAnalytics::getTopPages(10);
        
        // Clean page URLs for better display
        $cleanedPages = $pages->map(function ($page) {
            $url = parse_url($page->page_url);
            $path = $url['path'] ?? '/';
            
            // Convert paths to readable names
            $name = $this->getPageName($path);
            
            return [
                'page' => $name,
                'path' => $path,
                'views' => $page->views
            ];
        });

        return response()->json($cleanedPages);
    }

    /**
     * Get visitor location statistics
     */
    public function locations()
    {
        $countries = VisitorAnalytics::getTopCountries(10);
        return response()->json($countries);
    }

    /**
     * Get device and browser statistics
     */
    public function devices()
    {
        $devices = VisitorAnalytics::getDeviceStats();
        $browsers = VisitorAnalytics::getBrowserStats();
        
        return response()->json([
            'devices' => $devices,
            'browsers' => $browsers
        ]);
    }

    /**
     * Get referrer statistics
     */
    public function referrers()
    {
        $referrers = VisitorAnalytics::getReferrerStats();
        
        // Clean referrer URLs
        $cleanedReferrers = $referrers->map(function ($referrer) {
            $parsed = parse_url($referrer->referrer);
            $domain = $parsed['host'] ?? $referrer->referrer;
            
            return [
                'referrer' => $domain,
                'visits' => $referrer->visits
            ];
        });

        return response()->json($cleanedReferrers);
    }

    /**
     * Get recent visitors activity
     */
    public function recentActivity()
    {
        $recentVisits = VisitorAnalytics::where('is_bot', false)
            ->orderBy('visited_at', 'desc')
            ->limit(20)
            ->get(['ip_address', 'country', 'city', 'page_url', 'device_type', 'browser', 'visited_at']);

        $activity = $recentVisits->map(function ($visit) {
            $url = parse_url($visit->page_url);
            $path = $url['path'] ?? '/';
            
            return [
                'ip' => $this->maskIp($visit->ip_address),
                'location' => $visit->city && $visit->country ? 
                    "{$visit->city}, {$visit->country}" : 
                    ($visit->country ?: 'Unknown'),
                'page' => $this->getPageName($path),
                'device' => $visit->device_type,
                'browser' => $visit->browser,
                'time' => $visit->visited_at->diffForHumans()
            ];
        });

        return response()->json($activity);
    }

    /**
     * Get page name from path
     */
    private function getPageName($path)
    {
        $pageNames = [
            '/' => 'Ana Sayfa',
            '/about' => 'Hakkımda',
            '/contact' => 'İletişim',
            '/projects' => 'Projeler',
            '/blog' => 'Blog',
        ];

        // Check for blog post pattern
        if (preg_match('/^\/blog\/(.+)$/', $path)) {
            return 'Blog Detay';
        }
        
        // Check for project pattern
        if (preg_match('/^\/projects\/(.+)$/', $path)) {
            return 'Proje Detay';
        }

        return $pageNames[$path] ?? $path;
    }

    /**
     * Mask IP address for privacy
     */
    private function maskIp($ip)
    {
        if (strpos($ip, ':') !== false) {
            // IPv6
            $parts = explode(':', $ip);
            return implode(':', array_slice($parts, 0, 4)) . ':****';
        } else {
            // IPv4
            $parts = explode('.', $ip);
            return $parts[0] . '.' . $parts[1] . '.***.' . $parts[3];
        }
    }
}
