<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\VisitorAnalytics;
use Jenssegers\Agent\Agent;

class TrackVisitors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip tracking for admin routes and API routes (except public ones)
        if ($request->is('admin/*') || 
            ($request->is('api/*') && !$request->is('api/articles') && !$request->is('api/projects') && !$request->is('api/skills'))) {
            return $next($request);
        }

        // Skip tracking for admin IP addresses
        $clientIp = $this->getClientIp($request);
        if ($this->isAdminIp($clientIp)) {
            return $next($request);
        }

        try {
            $this->trackVisitor($request);
        } catch (\Exception $e) {
            // Don't let tracking errors break the application
            \Log::error('Visitor tracking error: ' . $e->getMessage());
        }

        return $next($request);
    }

    private function trackVisitor(Request $request)
    {
        $agent = new Agent();
        $ip = $this->getClientIp($request);
        $userAgent = $request->userAgent();
        
        // Check if it's a bot
        $isBot = $this->isBot($userAgent);
        
        // Get or create session ID
        $sessionId = $request->session()->getId() ?: 'anonymous_' . md5($ip . $userAgent);
        
        // Parse UTM parameters
        $utmParams = $this->getUtmParameters($request);
        
        // Get location info (you might want to use a service like GeoIP)
        $locationData = $this->getLocationData($ip);
        
        // Get device information
        $deviceType = $this->getDeviceType($agent);
        
        VisitorAnalytics::create([
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'page_url' => $request->fullUrl(),
            'referrer' => $request->header('referer'),
            'country' => $locationData['country'] ?? null,
            'city' => $locationData['city'] ?? null,
            'device_type' => $deviceType,
            'browser' => $agent->browser() . ' ' . $agent->version($agent->browser()),
            'os' => $agent->platform() . ' ' . $agent->version($agent->platform()),
            'utm_parameters' => $utmParams,
            'is_bot' => $isBot,
            'session_id' => $sessionId,
            'visited_at' => now(),
        ]);
    }

    private function getClientIp(Request $request)
    {
        // Try to get real IP from various headers
        $ipKeys = [
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ipKeys as $key) {
            if ($ip = $request->server($key)) {
                // Handle comma separated IPs
                $ips = explode(',', $ip);
                $ip = trim($ips[0]);
                
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return $request->ip();
    }

    private function isAdminIp($ip)
    {
        // Admin IP addresses - these visitors won't be tracked
        $adminIps = [
            '127.0.0.1',        // localhost
            '::1',              // localhost IPv6
            // Add your specific admin IP addresses here
            // You can get your current IP from: https://whatismyipaddress.com/
            // Example: '192.168.1.100', '203.0.113.1'
        ];
        
        // You can also add environment variable support
        if ($envAdminIps = env('ADMIN_IPS')) {
            $envIps = explode(',', $envAdminIps);
            $adminIps = array_merge($adminIps, array_map('trim', $envIps));
        }
        
        return in_array($ip, $adminIps);
    }

    private function isBot($userAgent)
    {
        $bots = [
            'googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider',
            'yandexbot', 'facebookexternalhit', 'twitterbot', 'linkedinbot',
            'whatsapp', 'telegrambot', 'applebot', 'spider', 'crawler'
        ];

        $userAgent = strtolower($userAgent);
        
        foreach ($bots as $bot) {
            if (strpos($userAgent, $bot) !== false) {
                return true;
            }
        }

        return false;
    }

    private function getUtmParameters(Request $request)
    {
        $utmParams = [];
        $utmKeys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];
        
        foreach ($utmKeys as $key) {
            if ($request->has($key)) {
                $utmParams[$key] = $request->get($key);
            }
        }
        
        return empty($utmParams) ? null : $utmParams;
    }

    private function getLocationData($ip)
    {
        // For now, return empty array
        // You can integrate with services like:
        // - MaxMind GeoIP2
        // - ipapi.com
        // - ipstack.com
        // - freegeoip.app
        
        try {
            // Simple free service (rate limited)
            if ($ip !== '127.0.0.1' && $ip !== '::1') {
                $response = @file_get_contents("http://ip-api.com/json/{$ip}");
                if ($response) {
                    $data = json_decode($response, true);
                    if ($data['status'] === 'success') {
                        return [
                            'country' => $data['country'] ?? null,
                            'city' => $data['city'] ?? null,
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            // Ignore errors
        }
        
        return ['country' => null, 'city' => null];
    }

    private function getDeviceType(Agent $agent)
    {
        if ($agent->isMobile()) {
            return 'mobile';
        } elseif ($agent->isTablet()) {
            return 'tablet';
        } elseif ($agent->isDesktop()) {
            return 'desktop';
        }
        
        return 'unknown';
    }
}
