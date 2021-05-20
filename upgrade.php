<?php    
include_once("wp-config.php");
include_once("wp-includes/wp-db.php");

$sqls = [
	"UPDATE wp_options SET option_value = replace(option_value, 'https://eshop.bonculina.se', 'https://bonculina.se') WHERE option_name = 'home' OR option_name = 'siteurl';",
	"UPDATE wp_posts SET post_content = replace(post_content, 'https://eshop.bonculina.se', 'https://bonculina.se');",
	"UPDATE wp_postmeta SET meta_value = replace(meta_value,'https://eshop.bonculina.se','https://bonculina.se');",
	"UPDATE wp_usermeta SET meta_value = replace(meta_value, 'https://eshop.bonculina.se','https://bonculina.se');",
	"UPDATE wp_links SET link_url = replace(link_url, 'https://eshop.bonculina.se','https://bonculina.se');",
	"UPDATE wp_comments SET comment_content = replace(comment_content , 'https://eshop.bonculina.se','https://bonculina.se');",
];
foreach($sqls as $sql) {
	var_export($wpdb->get_results($sql));
}