$out=[];
foreach ([2368, 39327] as $id) {
  $raw = get_post_meta($id,'_mf_elementor_data_backup',true) ?: get_post_meta($id,'_elementor_data',true);
  $data = json_decode($raw,true); $n=0;
  $walk=function(&$els) use (&$walk,&$n){ foreach($els as &$e){
    if(($e['elType']??'')==='widget' && $e['widgetType']==='shortcode' && trim($e['settings']['shortcode']??'')==='[wpcode id="37396"]'){
      $keep=array_filter($e['settings'],fn($k)=>strpos($k,'_')===0,ARRAY_FILTER_USE_KEY);
      $e['widgetType']='mf-mobile-menu'; $e['settings']=array_merge($keep,['menu'=>'mobile-menu','back_label'=>'ALL']); $n++; }
    if(!empty($e['elements'])) $walk($e['elements']); } unset($e); };
  $walk($data);
  if (!get_post_meta($id,'_mf_elementor_data_backup',true)) add_post_meta($id,'_mf_elementor_data_backup', wp_slash($raw));
  update_post_meta($id,'_elementor_data', wp_slash(wp_json_encode($data)));
  delete_post_meta($id,'_elementor_css'); delete_post_meta($id,'_elementor_element_cache');
  $out[$id]=$n;
}
\Elementor\Plugin::$instance->files_manager->clear_cache(); do_action('litespeed_purge_all');
return $out;
