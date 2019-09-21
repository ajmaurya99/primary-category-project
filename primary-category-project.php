<?php
/*
Plugin Name: Primary Category Plugin
Plugin URI: https://github.com/ajmaurya99
Description: https://github.com/ajmaurya99
Author: Ajay Maurya
Version: 1.0
Author URI: https://github.com/ajmaurya99
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
  exit;
}


if (!defined('PLUGIN_DIR')) {
  define('PLUGIN_DIR', plugin_dir_path(__FILE__));
}


include PLUGIN_DIR . 'includes/class-primary-category-project.php';

// initializing the class
$pcp = new Primary_Category_Project();