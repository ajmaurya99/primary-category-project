<?php

/**
 * References
 * https://developer.wordpress.org/reference/functions/add_meta_box/
 * https://developer.wordpress.org/plugins/metadata/custom-meta-boxes/
 * https://10up.github.io/Engineering-Best-Practices/php/
 */

/**
 * Main Class to initialize the Plugin
 * Class Primary_Category_Project
 */
class Primary_Category_Project
{

  /**
   * Hook into the appropriate actions when the class is constructed.
   * Limit meta box to certain post types.
   */
  public function __construct()
  {
    add_action('load-post.php', array($this, 'pcp_create_meta_box'));
    add_action('load-post-new.php', array($this, 'pcp_create_meta_box'));
  }

  public function pcp_create_meta_box()
  {
    add_action('add_meta_boxes', array($this, 'pcp_add_primary_cat_box'));
    add_action('save_post', array($this, 'pcp_save_primary_cat_box'), 10, 2);
  }

  /**
   * Adds the meta box container.
   */
  public function pcp_add_primary_cat_box()
  {

    // https://wordpress.stackexchange.com/questions/73796/one-metabox-for-multiple-post-types
    // get all custom post types
    $post_types = get_post_types(
      array(
        'public' => true, // only get publically accessable post types
        '_builtin' => false // remove builtin post types
      )
    );

    // add buildin 'post' type to $post_types array
    $post_types['post'] = 'post';

    add_meta_box(
      'primary-category-project',
      esc_html__('Select Primary Category'),
      array($this, 'pcp_render_meta_box_content'),
      $post_types,
      'side',
      'high'
    );
  }

  /**
   * Render Meta Box content.
   *
   * @param WP_Post $post The post object.
   */
  public function pcp_render_meta_box_content($post)
  {

    // Add an nonce field so we can check for it later.
    wp_nonce_field('pcp_custom_meta_box', 'pcp_custom_meta_box_nonce');

    $primary_category = '';
    $primary_category_selected = get_post_meta($post->ID, 'pcp_primary_category', true);

    if ($primary_category_selected != '') {
      $primary_category = $primary_category_selected;
    }

    $args = array(
      'orderby' => 'term_id',
      'order' => 'ASC',
      'hide_empty' => FALSE,
    );

    // Retrieve list of category objects.
    $post_categories = get_the_category($post->ID);

    // Create the select box with category values to show in the meta box
    $html = '<select name="primary_category" id="primary_category">';

    // Set a default value for the option box
    $html .= '<option value="pcp_default_option">' . __('Select a primary category') . '</option>';

    if (!empty($post_categories)) {
      foreach ($post_categories as $post_category) {
        $html .= '<option value="' . $post_category->name . '" ' . selected($primary_category, $post_category->name, false) . '>' . __($post_category->name) . '</option>';
      }
    }

    $html .= '</select>';
    $html .= '<p>Select a Primary Category for this post.</p>';

    echo $html;
  }

  public function pcp_save_primary_cat_box($post_id, $post)
  {

    /**
     * We need to verify this came from the our screen and with proper authorization,
     * because save_post can be triggered at other times.
     */

    // Check if our nonce is set.
    if (!isset($_POST['pcp_custom_meta_box_nonce'])) {
      return $post_id;
    }

    $nonce = $_POST['pcp_custom_meta_box_nonce'];

    // Verify that the nonce is valid.
    if (!wp_verify_nonce($nonce, 'pcp_custom_meta_box')) {
      return $post_id;
    }

    /*
         * If this is an autosave, our form has not been submitted,
         * so we don't want to do anything.
         */
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return $post_id;
    }


    $post_type = get_post_type_object($post->post_type);

    // Check the user's permissions.
    if (!current_user_can($post_type->cap->edit_post, $post_id)) {
      return $post_id;
    }

    /* OK, it's safe for us to save the data now. */

    if ((isset($_POST['primary_category']) && ($_POST['primary_category'] != 'pcp_default_option'))) {
      // Sanitize the user input.
      $primary_category = sanitize_text_field($_POST['primary_category']);
      update_post_meta($post->ID, 'pcp_primary_category', $primary_category);
    }
  }
}
