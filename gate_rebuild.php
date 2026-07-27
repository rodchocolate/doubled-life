<?php
// Rebuild-request drain for the Studio code poller. Returns + removes queued
// "rebuild this page" requests (see rebuild.php). Token-gated (shared secret,
// same as the other drains); one-time drain.
$config = @include __DIR__ . '/config.php';
if (!$config || !hash_equals($config['poll_token'] ?? '', $_GET['token'] ?? '')) {
    http_response_code(403); exit('no');
}
$dir = __DIR__ . '/../doubled_state/rebuild_pending';
$out = [];
if (is_dir($dir)) {
    foreach (glob($dir . '/*.json') as $f) {
        $d = json_decode(file_get_contents($f), true);
        if ($d) { $out[] = $d; }
        @unlink($f);
    }
}
header('Content-Type: application/json');
echo json_encode($out);
