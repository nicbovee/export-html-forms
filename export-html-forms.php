<?php

/**
 * Plugin Name: Export HTML Forms
 * Plugin URI: https://github.com/nicbovee/export-html-forms
 * Description: Export HTML Forms submissions to CSV.
 * Version:           0.1.0
 * Requires PHP: 7.4
 * Author: nicbovee
 * Author URI: https://github.com/nicbovee
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: export-html-forms
 */



add_action('admin_enqueue_scripts', function () {

  if ($_GET['page'] === 'html-forms' &&  $_GET['view'] === 'edit') {

    wp_enqueue_script('export-html-forms', plugin_dir_url(__FILE__) . '/export-html-forms.js', array(), null, TRUE);
    wp_add_inline_script('export-html-forms', 'const wpApiSettings = ' . json_encode([
      'root' => esc_url_raw(rest_url()),
      'nonce' => wp_create_nonce('wp_rest'),
      'htmlFormsId' => $_GET['form_id']

    ]), 'before');
  }
});

add_action('hf_admin_output_form_tab_submissions', function () {
  require_once __DIR__ . '/export-button.php';
});


// Make url available /wp-json/export-html-forms/v1/download
add_action('rest_api_init', function () {
  register_rest_route('export-html-forms/v1', '/download', array(
    'methods' => 'GET',
    'callback' => function () {

      $submissions = hf_get_form_submissions($_GET['form_id']);
      return $submissions;
    },
    'permission_callback' => function () {
      return current_user_can('administrator');
    }
  ));
});
