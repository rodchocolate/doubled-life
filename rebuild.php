<?php
// "rebuild this page" intake — session-gated (same gate as the rest of the
// authed site; see index.php). Queues ONE request file under
// ../doubled_state/rebuild_pending/ for the Studio poller to drain via
// gate_rebuild.php and dispatch through ingest.reframe_subjects. Mirrors how
// gate_pending.php / the notes intake in index.php queue their state.
$config = @include __DIR__ . '/config.php';
if (!$config) { http_response_code(503); exit('not configured'); }

session_set_cookie_params(['lifetime' => 0, 'httponly' => true, 'samesite' => 'Lax']);
session_start();
if (($_SESSION['auth'] ?? false) !== true) { http_response_code(403); exit('no'); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('no'); }

$subject = trim($_POST['subject'] ?? '');
$slug = trim($_POST['slug'] ?? '');
$lens = trim($_POST['lens'] ?? 'auto');
$instructions = trim($_POST['instructions'] ?? '');

$valid_lenses = ['auto', 'collection', 'craft', 'encyclopedia'];
if (!in_array($lens, $valid_lenses, true)) { $lens = 'auto'; }

if ($subject === '' || mb_strlen($subject) > 200) { http_response_code(400); exit('bad subject'); }
if ($slug === '' || !preg_match('/^[a-z0-9-]{1,80}$/', $slug)) { http_response_code(400); exit('bad slug'); }
if (mb_strlen($instructions) > 2000) { $instructions = mb_substr($instructions, 0, 2000); }

$dir = __DIR__ . '/../doubled_state/rebuild_pending';
if (!is_dir($dir)) { @mkdir($dir, 0700, true); }
$id = time() . '-' . bin2hex(random_bytes(3));
file_put_contents($dir . '/' . $id . '.json', json_encode([
    'subject' => $subject,
    'slug' => $slug,
    'lens' => $lens,
    'instructions' => $instructions,
    'ts' => time(),
]), LOCK_EX);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex,nofollow">
<title>doubled.life</title>
<style>
  :root {
    --bg: #26547c; --text: #06d6a0; --porcelain: #fffcf9;
    --line: rgba(255,252,249,.24); --link: #ef476f;
  }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Space Grotesk', 'Avenir Next', 'Segoe UI', system-ui, sans-serif;
    background: var(--bg); color: var(--text); min-height: 100vh;
    display: flex; align-items: center; justify-content: center; text-align: center; padding: 2rem; }
  .box { max-width: 28rem; }
  h1 { font-size: 1.2rem; font-weight: 600; color: var(--porcelain); margin-bottom: .6rem; }
  p { font-size: .9rem; opacity: .8; line-height: 1.5; margin-bottom: 1.4rem; }
  a { color: var(--link); }
</style>
</head>
<body>
<div class="box">
  <h1>queued — Hermes will rebuild this page</h1>
  <p><?= htmlspecialchars($subject) ?></p>
  <a href="javascript:history.back()">back</a>
</div>
</body>
</html>
