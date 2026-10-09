<?php
// wp eval-file regen_sizes.php : create missing registered sizes for product images (originals untouched)
global $wpdb;
$ids = $wpdb->get_col("SELECT DISTINCT meta_value FROM $wpdb->postmeta WHERE meta_key='_thumbnail_id' AND post_id IN (SELECT ID FROM $wpdb->posts WHERE post_type='product' AND post_status='publish')");
foreach ($wpdb->get_col("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key='_product_image_gallery' AND meta_value<>''") as $g) { foreach (explode(',', $g) as $x) { $ids[] = $x; } }
$ids = array_unique(array_filter(array_map('intval', $ids)));
require_once ABSPATH . 'wp-admin/includes/image.php';
$todo = array();
foreach ($ids as $a) { $m = wp_get_attachment_metadata($a); if ($m && !empty($m['width']) && $m['width'] > 1024 && empty($m['sizes']['large']) && empty($m['sizes']['medium_large'])) { $todo[] = $a; } }
echo count($todo) . " to process\n";
$t0 = microtime(true); $done = 0; $err = 0;
foreach ($todo as $a) {
  $r = wp_update_image_subsizes($a);
  if (is_wp_error($r)) { $err++; echo "ERR $a " . $r->get_error_message() . "\n"; }
  if (++$done % 100 === 0) { printf("%d done, %d errors, %.0fs\n", $done, $err, microtime(true) - $t0); }
}
printf("FINISHED %d done, %d errors, %.0fs\n", $done, $err, microtime(true) - $t0);
