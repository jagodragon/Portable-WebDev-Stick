<?php
header('Content-Type: text/plain');
echo "🧠 Testing Portable Redis Mega-Stack Connection...\n\n";

// Raw Redis Protocol Helper Function
function send_redis_cmd($fp, $cmd) {
    fwrite($fp, $cmd . "\r\n");
    return fgets($fp, 512);
}

try {
    // 1. Open a direct network stream socket over your portable loopback port
    $fp = fsockopen('127.0.0.1', 6379, $errno, $errstr, 2);

    if (!$fp) {
        throw new Exception("Could not connect to Redis socket: $errstr ($errno)");
    }

    // 2. Send the raw SET command to write the payload into memory
    send_redis_cmd($fp, "SET sandbox_status \"🚀 PORTABLE STACK RUNNING PERFECTLY!\"");

    // 3. Send the raw GET command to fetch it right back out
    send_redis_cmd($fp, "GET sandbox_status");
    $result = fgets($fp, 512); // Capture the bulk string response payload

    // Clean up the raw line breaks from the data output stream
    $clean_result = trim($result);

    echo "✅ Success! Redis retrieved value: " . $clean_result . "\n";

    fclose($fp);
} catch (Exception $e) {
    echo "❌ Error connecting to Redis: " . $e->getMessage() . "\n";
}
