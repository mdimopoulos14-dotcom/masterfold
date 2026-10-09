<?php
// Usage: php webp_conv.php <uploads_dir> <limit> [quality]
// Creates image.jpg.webp / image.png.webp next to originals (EWWW naming). Originals untouched.
$dir = rtrim($argv[1], '/'); $limit = (int)($argv[2] ?? 0); $q = (int)($argv[3] ?? 82);
$done = 0; $skipped = 0; $saved = 0; $orig = 0; $t0 = microtime(true);
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
foreach ($it as $f) {
  $p = $f->getPathname();
  if (!preg_match('/\.(jpe?g|png)$/i', $p)) continue;
  if (strpos($p, '/ewww/') !== false || strpos($p, '/litespeed/') !== false) continue;
  $out = $p . '.webp';
  if (file_exists($out)) { $skipped++; continue; }
  if ($f->getSize() < 20480) { $skipped++; continue; }
  try {
    $im = new Imagick($p);
    $im->setImageFormat('webp');
    $im->setImageCompressionQuality($q);
    $im->setOption('webp:method', '2');
    if (preg_match('/\.png$/i', $p)) { $im->setOption('webp:lossless', 'false'); $im->setOption('webp:alpha-quality', '100'); }
    $blob = $im->getImagesBlob(); $im->clear();
    $o = filesize($p);
    // Only keep the WebP when it is actually smaller.
    if (strlen($blob) < $o) { file_put_contents($out, $blob); touch($out, filemtime($p)); $saved += $o - strlen($blob); $orig += $o; }
    else { $skipped++; }
  } catch (Throwable $e) { fwrite(STDERR, "ERR $p: " . $e->getMessage() . "\n"); }
  $done++;
  if ($done % 500 === 0) { printf("%d done, %.1f MB saved, %.0fs\n", $done, $saved/1048576, microtime(true)-$t0); }
  if ($limit && $done >= $limit) break;
}
printf("FINISHED %d converted/checked, %d skipped, original %.1f MB -> saved %.1f MB (%.0f%%), %.0fs\n", $done, $skipped, $orig/1048576, $saved/1048576, $orig? $saved*100/$orig:0, microtime(true)-$t0);
