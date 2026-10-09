$out=[];
foreach ([2368,39327] as $pid) {
  $raw=get_post_meta($pid,'_elementor_data',true); $d=json_decode($raw,true); if(!$d){$out[$pid]='nodata';continue;}
  $n=0;
  foreach ($d as &$e) {
    $s=&$e['settings'];
    if (($e['elType']??'')==='container' && ($s['sticky']??'')==='top' && !empty($s['hide_mobile']) && empty($s['hide_desktop'])) {
      $s['sticky']=''; $cls=trim($s['css_classes']??''); if(strpos($cls,'mf-sticky-top')===false) $s['css_classes']=trim($cls.' mf-sticky-top'); $n++;
    }
    unset($s);
  }
  unset($e);
  if ($n) {
    if (!get_post_meta($pid,'_mf_elementor_data_backup_sticky',true)) update_post_meta($pid,'_mf_elementor_data_backup_sticky',wp_slash($raw));
    update_post_meta($pid,'_elementor_data',wp_slash(wp_json_encode($d)));
  }
  $out[$pid]=$n;
}
\Elementor\Plugin::$instance->files_manager->clear_cache();
do_action('litespeed_purge_all');
return $out;
