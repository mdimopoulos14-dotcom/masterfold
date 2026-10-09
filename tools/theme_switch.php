$before = get_option('stylesheet');
if ( ! wp_get_theme('masterfold')->exists() ) return ['err'=>'missing theme'];
$mods = get_option('theme_mods_hello-elementor');
$css_post = get_post(73);
$css = $css_post ? $css_post->post_content : '';
$new_mods = ['nav_menu_locations'=>$mods['nav_menu_locations'] ?? [], 'sidebars_widgets'=>$mods['sidebars_widgets'] ?? null];
update_option('theme_mods_masterfold', $new_mods);
switch_theme('masterfold');
$r = wp_update_custom_css_post($css, ['stylesheet'=>'masterfold']);
if ( is_wp_error($r) ) { switch_theme($before); return ['err'=>$r->get_error_message()]; }
if (function_exists('opcache_reset')) @opcache_reset();
do_action('litespeed_purge_all');
$bad=[];
foreach (['/', '/product/ocean-fabric-wine-list-sani/', '/product-category/restaurant/restaurant-menu/'] as $p) {
  $res=wp_remote_get(home_url($p).'?LSCWP_CTRL=before_optm&nmsw='.time(), ['timeout'=>25,'sslverify'=>false]);
  $c=is_wp_error($res)?$res->get_error_message():wp_remote_retrieve_response_code($res); $b=wp_remote_retrieve_body($res);
  if ($c!==200 || stripos($b,'critical error')!==false || strpos($b,'masterfold-base-css')===false || strpos($b,'wp-custom-css')===false) $bad[$p]=[$c, strpos($b,'masterfold-base-css')!==false, strpos($b,'wp-custom-css')!==false];
}
if ($bad) { switch_theme($before); do_action('litespeed_purge_all'); return ['ROLLED_BACK'=>$bad]; }
return ['switched'=>get_option('stylesheet'), 'css_post'=>$r->ID, 'css_len'=>strlen($css), 'menus'=>get_nav_menu_locations()];
