<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
  die;
}

$query = new WP_Query(array('post_type' => 'post'));
$posts = $query->posts;

foreach ($posts as $post) {
  delete_post_meta($post->ID, 'pcp_primary_category');
}

?>
