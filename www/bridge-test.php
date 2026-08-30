<?php
header('Content-Type: text/plain; charset=utf-8');
echo "🚀 Unified Cross-Platform Cache Bridge Verification...\n\n";

function query_redis($cmd) {
    $fp = fsockopen('127.0.0.1', 6379, $errno, $errstr, 2);
    if (!$fp) return "❌ Connection Error: $errstr";

    fwrite($fp, $cmd . "\r\n");
    fgets($fp); // Skip RESP line tracking markers
    $payload = trim(fgets($fp));
    fclose($fp);
    return $payload;
}

// Fetch the live timestamp token injected by your Node.js engine background layer
$js_visit = query_redis("GET last_js_visit");

if (empty($js_visit) || strpos($js_visit, 'Error') !== false) {
    echo "🟨 Redis is hot, but no JavaScript visitor logs exist in memory yet.\n";
    echo "👉 Action: Hit http://127.0.0.1:3000 first, then refresh this page!\n";
} else {
    echo "✅ Success! PHP read the tracking token out of Redis memory cells natively!\n";
    echo "⏱️ Last backend Node.js execution timestamp: " . $js_visit . "\n";
}
