<?php

/**
 * Plugin Name: Export HTML Forms
 * Plugin URI: https://github.com/nicbovee/export-html-forms
 */

// die;
add_action('plugins_loaded', function () {
  if (class_exists('HTML_Forms\Form')) {
    $submissions = hf_get_form_submissions($_GET['form_id']);
  }
});




add_action('admin_enqueue_scripts', function () {
  wp_enqueue_script('export-html-forms', esc_url(get_template_directory_uri()) . 'export-html-forms.js', array(), null, TRUE);
  wp_add_inline_script('export-html-forms', 'const wpApiSettings = ' . json_encode([
    'root' => esc_url_raw(rest_url()),
    'nonce' => wp_create_nonce('wp_rest'),

  ]), 'before');
});

add_action('hf_admin_output_form_tab_submissions', function () {
  // include export-button.php
  if (class_exists('HTML_Forms\Form')) {
    $submissions = hf_get_form_submissions($_GET['form_id']);
    var_dump($submissions);
  }

  require_once __DIR__ . '/export-button.php';
});

function arrayToCsvString($data)
{
  // Open a memory "file" for read/write
  $f = fopen('php://memory', 'rw');

  // Write the data to the "file"
  fputcsv($f, $data);

  // Rewind the "file" so we can read what we just wrote
  rewind($f);

  // Read the entire line into a variable
  $csvLine = fgets($f);

  // Close the "file"
  fclose($f);

  // Return the CSV string
  return $csvLine;
}

// Make url available /wp-json/export-html-forms/v1/download
add_action('rest_api_init', function () {
  register_rest_route('export-html-forms/v1', '/download', array(
    'methods' => 'GET',
    'callback' => function () {

      // Image exists, prepare a binary-data response.
      $submissions = hf_get_form_submissions($_GET['form_id']);
      return $submissions;

      $headers = false;
      $csv = '';

      foreach ($submissions as $submission) {
        if (!$headers) {
          foreach ($submission->data as $key => $data) {
            $csv .= $key . ',';
          }
          $csv .= "\n";
          $headers = true;
        }

        foreach ($submission->data as $key => $data) {
          $csv .= $data . ',';
        }
        $csv .= "\n";
      }
      return $csv;
    },
    'permission_callback' => function () {
      return current_user_can('administrator');
    }
  ));
});
