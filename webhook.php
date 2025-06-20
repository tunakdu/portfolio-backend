<?php
/**
 * GitHub Webhook Handler for Portfolio Deployment
 * Place this file in your web root and configure GitHub webhook
 */

// Security configuration
define('WEBHOOK_SECRET', 'portfolio_deploy_2024_secure_key_tunakdu');
define('ALLOWED_IPS', [
    '140.82.112.0/20',   // GitHub webhook IPs
    '185.199.108.0/22',
    '192.30.252.0/22',
    '143.55.64.0/20',
]);

// Logging function
function logMessage($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[{$timestamp}] {$message}" . PHP_EOL;
    file_put_contents(__DIR__ . '/webhook.log', $logEntry, FILE_APPEND | LOCK_EX);
    echo $logEntry;
}

// Verify request origin
function verifyGitHubIP() {
    $clientIP = $_SERVER['REMOTE_ADDR'] ?? '';
    
    foreach (ALLOWED_IPS as $range) {
        if (strpos($range, '/') !== false) {
            // CIDR notation
            list($subnet, $mask) = explode('/', $range);
            if ((ip2long($clientIP) & ~((1 << (32 - $mask)) - 1)) == ip2long($subnet)) {
                return true;
            }
        } else {
            // Single IP
            if ($clientIP === $range) {
                return true;
            }
        }
    }
    
    return false;
}

// Verify webhook signature
function verifySignature($payload, $signature) {
    $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, WEBHOOK_SECRET);
    return hash_equals($expectedSignature, $signature);
}

// Start processing
logMessage("🔗 Webhook request received from: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    logMessage("❌ Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    exit('Method not allowed');
}

// Get raw POST data
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';

// Verify signature
if (!verifySignature($payload, $signature)) {
    http_response_code(401);
    logMessage("❌ Invalid webhook signature");
    exit('Unauthorized');
}

// Verify IP (optional, comment out if behind proxy)
// if (!verifyGitHubIP()) {
//     http_response_code(403);
//     logMessage("❌ Request from unauthorized IP: " . $_SERVER['REMOTE_ADDR']);
//     exit('Forbidden');
// }

// Parse payload
$data = json_decode($payload, true);

if (!$data) {
    http_response_code(400);
    logMessage("❌ Invalid JSON payload");
    exit('Bad request');
}

// Check if this is a push to master branch
if (!isset($data['ref']) || $data['ref'] !== 'refs/heads/master') {
    logMessage("ℹ️ Ignoring push to branch: " . ($data['ref'] ?? 'unknown'));
    exit('Not master branch, ignoring');
}

// Get commit information
$commits = $data['commits'] ?? [];
$latestCommit = end($commits);
$commitMessage = $latestCommit['message'] ?? 'Unknown commit';
$commitAuthor = $latestCommit['author']['name'] ?? 'Unknown author';

logMessage("📦 Push to master detected");
logMessage("👤 Author: {$commitAuthor}");
logMessage("💬 Commit: " . substr($commitMessage, 0, 100));

// Check if deployment script exists
$deployScript = __DIR__ . '/deploy.sh';
if (!file_exists($deployScript)) {
    http_response_code(500);
    logMessage("❌ Deploy script not found: {$deployScript}");
    exit('Deploy script not found');
}

// Make script executable
chmod($deployScript, 0755);

// Execute deployment in background
$command = "cd " . __DIR__ . " && ./deploy.sh > /tmp/webhook-deploy-output.log 2>&1 &";
$result = shell_exec($command);

logMessage("🚀 Deployment started in background");
logMessage("📝 Command: {$command}");

// Return success response
http_response_code(200);
echo json_encode([
    'status' => 'success',
    'message' => 'Deployment started',
    'commit' => $commitMessage,
    'author' => $commitAuthor,
    'timestamp' => date('Y-m-d H:i:s')
]);

logMessage("✅ Webhook processed successfully");
?>