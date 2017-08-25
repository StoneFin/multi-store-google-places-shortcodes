<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

add_action('admin_menu', 'multi_store_google_places_shortcodes_settings_page');
add_action('load_multi_store_google_places_shortcodes_options', 'multi_store_google_places_shortcodes_settings_page_save_options' );

// add a Settings page beneath the Store Location custom Post type
function multi_store_google_places_shortcodes_settings_page(){
  add_submenu_page(
    'edit.php?post_type=wp-location',
    'Multi Store Google Places ShortCodes Settings',
    'Settings',
    'manage_options',
    'multi_store_google_places_shortcodes_options',
    'multi_store_google_places_shortcodes_settings_page_render'
  );
}

// render the settings page
function multi_store_google_places_shortcodes_settings_page_render(){
  include 'templates/multi-store-google-places-shortcodes-admin-settings-options.php';
}

// Save the settings page when it is submitted
function multi_store_google_places_shortcodes_settings_page_save_options(){

  $action = 'multi-store-google-places-shortcodes-settings-page-save';
  $nonce = 'multi-store-google-places-shortcodes-settings-page-save-nonce';

  // Prevent people from changing settings if they aren't supposed to be here
  if ( !multi_store_google_places_shortcodes_user_can_save( $action, $nonce ) ){
    return;
  }

  if ( isset( $_POST['googlemaps_api_key'] ) ){
    update_option( 'googlemaps_api_key', $_POST['googlemaps_api_key'] );
    $_GET['saved_api_key'] = true;
  }

  if ( isset( $_POST['googlemaps_api_endpoint'] ) ){
    update_option( 'googlemaps_api_endpoint', $_POST['googlemaps_api_endpoint'] );
  }

  if ( isset( $_POST['googlemaps_api_version'] ) ){
    update_option( 'googlemaps_api_version', $_POST['googlemaps_api_version'] );
  }
}

// Check if user can save this
function multi_store_google_places_shortcodes_user_can_save( $action, $nonce ) {
  $is_nonce_set = isset( $_POST[$nonce] );
  $is_valid_nonce = false;

  if( $is_nonce_set ){
    $is_valid_nonce = wp_verify_nonce( $_POST[$nonce], $action );
  }

  return ( $is_nonce_set && $is_valid_nonce );
}