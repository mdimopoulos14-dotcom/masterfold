$DRY = __DRY__;
$ids = [41109,41107,41119,41117,41115,41113,41111,40499,35813,35367,34741,34457,34163,34124,32989,31858,1874];
$colmap = ['selected-color-column'=>'color','selected-binding-column'=>'binding','selected-imprint-column'=>'imprint','selected-size-column'=>'size','selected-binding-paper-column'=>'binding-paper','additional-filters'=>'additional-filters'];
$report = [];
$strip_scripts = function($html, &$removed) {
  return preg_replace_callback('#<script\b[^>]*>(.*?)</script>\s*#s', function($m) use (&$removed) {
    $js = $m[1];
    if (strpos($js,'.selected-binding-column tr')!==false || strpos($js,'targetAttributes = ["pa_binding", "pa_imprint"]')!==false
      || strpos($js,'.variable-item')!==false || strpos($js,'woo-selected-variation-item-name')!==false || strpos($js,'attribute_pa_dif-size')!==false) {
      $removed[] = substr(preg_replace('/\s+/',' ',$js),0,60); return '';
    }
    return $m[0];
  }, $html);
};
foreach ($ids as $id) {
  $raw = get_post_meta($id,'_mf_elementor_data_backup',true) ?: get_post_meta($id,'_elementor_data',true);
  $data = json_decode($raw,true); if(!$data){ $report[$id]='nodata'; continue; }
  $r = ['atc'=>[], 'sc_removed'=>0, 'js_removed'=>[]];
  $walk = function(&$els, $cls) use (&$walk, &$r, $colmap, $strip_scripts) {
    foreach ($els as $i => &$e) {
      $c = $cls . ' ' . ($e['settings']['css_classes'] ?? '');
      if (($e['elType'] ?? '') === 'widget') {
        $w = $e['widgetType'];
        if ($w === 'woocommerce-product-add-to-cart') {
          $group = 'all';
          foreach ($colmap as $k => $g) { if (preg_match('/(^|\s)'.preg_quote($k,'/').'(\s|$)/', $c)) $group = $g; }
          $e['widgetType'] = 'mf-attribute-section';
          $e['settings'] = array_merge($e['settings'] ?? [], ['mf_group'=>$group]);
          $r['atc'][] = $group;
        } elseif ($w === 'shortcode' && trim($e['settings']['shortcode'] ?? '') === '[wpcode id="2636"]') {
          // Its job is done on the server now; keep the (empty) widget so spacing stays identical.
          $e['settings']['shortcode'] = '[mf_section_spacer]'; $r['sc_removed']++;
        } elseif ($w === 'html') {
          $e['settings']['html'] = $strip_scripts($e['settings']['html'] ?? '', $r['js_removed']);
        }
      }
      if (!empty($e['elements'])) { $walk($e['elements'], $c); }
    }
    unset($e);
    $els = array_values($els);
  };
  $walk($data, '');
  $r['js_removed'] = array_count_values($r['js_removed']);
  $r['atc'] = array_count_values($r['atc']);
  if (!$DRY) {
    if (!get_post_meta($id,'_mf_elementor_data_backup',true)) add_post_meta($id,'_mf_elementor_data_backup', wp_slash($raw));
    update_post_meta($id,'_elementor_data', wp_slash(wp_json_encode($data)));
    delete_post_meta($id,'_elementor_css'); delete_post_meta($id,'_elementor_element_cache');
  }
  $report[$id] = $r;
}
if (!$DRY && class_exists('\Elementor\Plugin')) { \Elementor\Plugin::$instance->files_manager->clear_cache(); do_action('litespeed_purge_all'); }
return $report;
