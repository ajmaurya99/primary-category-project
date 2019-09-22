<?php

/**
 * References
 * https://codex.wordpress.org/Function_Reference/add_shortcode
 * https://wpshout.com/how-to-create-wordpress-shortcodes/
 */

/**
 * Main Class to initialize the Plugin Shortcode
 * Class Primary_Category_Project_Shortcode
 */
 class Primary_Category_Project_Shortcode
{

  public function __construct()
  {
    add_shortcode('primary-category-project', array($this, 'primaryCategoryShortcode'));
  }

  public function primaryCategoryShortcode($attributes = [])
  {
    $attributes = shortcode_atts(
      array(
        'primary_category' => 'no category'
      ),
      $attributes,
      'primary-category-project'
    );

    $query = new WP_Query(array(
      'post_type'     => 'any',
      'meta_key'      => 'pcp_primary_category',
      'meta_value'    => $attributes['primary_category']
    ));

    $html = "";
    if ($query->have_posts()) {
      $html .= '<ul>';
      while ($query->have_posts()) {
        $query->the_post();
        $html .= '<li><a title="' . get_the_title() . '" href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
      }
      $html .= '</ul>';
    } else {
      $html = "No Posts Found";
    }

    echo $html;
  }
}
