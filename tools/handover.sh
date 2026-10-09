#!/bin/bash
# handover.sh id,id,... : deactivate WPCode snippets (plugin copy takes over), health-check, revert on failure
IDS="$1"
cat > /tmp/nm_ho.php <<PHP
\$ids=array_map('intval',explode(',','$IDS'));
\$recache=function(){ if (function_exists('wpcode') && isset(wpcode()->cache)) wpcode()->cache->cache_all_loaded_snippets(); };
foreach (\$ids as \$id) { wp_update_post(['ID'=>\$id,'post_status'=>'draft']); update_post_meta(\$id,'_mf_disabled_reason','Moved into Masterfold Core plugin (includes/snippets).'); }
\$recache(); wp_cache_delete('mf_wpcode_active','masterfold'); do_action('litespeed_purge_all');
\$bad=[];
foreach (['/','/product/ocean-fabric-wine-list-sani/','/product-category/restaurant/restaurant-menu/','/materials/standard-materials/leather/','/materials/sustainable-materials/natural-surfaces/'] as \$p) {
  \$r=wp_remote_get(home_url(\$p).'?nmho='.time(), ['timeout'=>60,'sslverify'=>false]);
  \$c=is_wp_error(\$r)?\$r->get_error_message():wp_remote_retrieve_response_code(\$r);
  \$b=is_wp_error(\$r)?'':wp_remote_retrieve_body(\$r);
  if (!in_array(\$c,[200,301,302],true) || stripos(\$b,'critical error')!==false) \$bad[\$p]=\$c;
}
if (\$bad) { foreach (\$ids as \$id) { wp_update_post(['ID'=>\$id,'post_status'=>'publish']); delete_post_meta(\$id,'_mf_disabled_reason'); } \$recache(); wp_cache_delete('mf_wpcode_active','masterfold'); do_action('litespeed_purge_all'); return ['REVERTED'=>\$bad]; }
return ['handed_over'=>count(\$ids)];
PHP
./php.sh /tmp/nm_ho.php | python3 -I -c "import json,sys;d=json.load(sys.stdin);print(d.get('data',{}).get('return_value',d))"
