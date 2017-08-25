<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

if ( ! class_exists( "msgps" ) ) {// this class is created in includes/class-msgps-location.php and is loaded in multi-store-google-places-shortcodes.php
  require_once "includes/class-msgps-location.php";
}


if ( ! class_exists( "google_places_api" ) ) {// this class is created in includes/class-google-places-api.php and is loaded in multi-store-google-places-shortcodes.php
  require_once( "includes/class-google-places-api.php" );
}

// Keep add_action calls grouped up here for easy viewing

add_action( 'init', 'msgps_custom_post_type' );//when the plugin is initialized - we want this function to be called
add_action( 'save_post', 'msgps_box_save' ); //when a location is saved - we want this function to be called
add_action( 'admin_notices', 'msgps_save_notice__error' ); //when admin notices are popped up - we want our fucntion called too.

// Keep shortcode calls grouped up here for easy viewing.  First parameter is the code, second is the name of the function that does the work.
add_shortcode( 'msgps_hours', 'msgps_hours_shortcode' );
add_shortcode( 'msgps_hours_long', 'msgps_hours_long_shortcode' );
add_shortcode( 'msgps_hours_short', 'msgps_hours_short_shortcode' );
add_shortcode( 'msgps_hours_today', 'msgps_hours_today_shortcode' );
add_shortcode( 'msgps_map', 'msgps_map_shortcode' );

// Register a custom post type with WordPress
function msgps_custom_post_type() {

  // $labels contains configuration for names - basically we could use this to make our plugin muli-lingual, simply redefine this array and the UI can reflect it
  $labels = array(
    'name'                => _x( 'Locations', 'Store Locations' ),
    'singular_name'       => _x( 'Location', 'Store Location' ),
    'add_new'             => _x( 'Add New', 'book' ),
    'add_new_item'        => __( 'Add New Location' ),
    'edit_item'           => __( 'Edit Location' ),
    'new_item'            => __( 'New Location' ),
    'all_items'           => __( 'All Locations' ),
    'view_item'           => __( 'View Locations' ),
    'search_items'        => __( 'Search Locations' ),
    'not_found'           => __( 'No locations found' ),
    'not_found_in_trash'  => __( 'No locations found in the Trash' ),
    'parent_item_colon'   => '',
    'menu_name'           => 'Locations'
  );

  // $args contains configuration options for the new custom post type
  $args = array(
    'labels'              => $labels,
    'description'         => 'Holds location data',
    'public'              => true,
    'menu_position'       => 5,
    'supports'            => array('title', 'thumbnail'),
    'has_archive'         => true,
    'publicly_queryable'  => false,
    'register_meta_box_cb'=> 'msgps_fields'
  );

  // Register the new post type with WordPress using given options
  register_post_type( 'wp-location', $args);
}

// Register a meta_box to hold custom fields
function msgps_fields(){
    add_meta_box(
            'msgps_data_box',
            __('Location Data', 'msgps'),
            'msgps_box_content',
            'wp-location', // In our case screen should be the same as the newly registered post_type
            'normal',
            'high'
    );
}

// Provide content for the msgps meta_box
function msgps_box_content( $post ){
  include( 'pages/msgps-location-page.php' );
}

// Handling wp-location post save
function msgps_box_save( $post_id ){

  // Don't save anything if the user didn't save it
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
    return;

  // Make sure the nonce is valid to help prevent malicious access
  if ( !wp_verify_nonce( $_POST['msgps_box_content_nonce'], 'test9000' ))
    return;

  if ( 'page' == $_POST['post_type'] ) {
    if ( !current_user_can( 'edit_page', $post_id ) )
      return;
  } else {
    if ( !current_user_can( 'edit_post', $post_id ) )
      return;
  }

  $location = $_POST['location']; // get the data that was posted

  // We need these fields to be populated if we want to pull Google Places information from them
  if(empty($location['address1']) || empty($location['name']) || empty($location['city']) || empty($location['province']) || empty($location['postal_code']) ){
    add_filter( 'redirect_post_location', 'add_notice_query_var', 99); // que up a warning for missing fields but still let them update
    $missing_required_fields = true;
  }

  // Lets only try to generate lat/long if we have the required fields
  if(!isset($missing_required_fields)){
    if ( empty( $location['longitude'] ) || empty( $location['latitude'] ) || empty( $location['place_id'] ) ) {
      // try and get the Geometry from Google
      $formatted = msgps_format_address( $location );
      $temp      = msgps_geocode( $formatted );
    }

    if($temp != null){
      // are our coordinates empty
      if ( ( empty( $location['latitude'] ) || empty( $location['longitude'] ) ) ) {
        $location['latitude']  = floatval( $temp['latitude'] );
        $location['longitude'] = floatval( $temp['longitude'] );
      }

      // dont overwrite manually entered place_id
      if ( empty( $location['place_id'] ) ) {
        $location['place_id'] = ! empty( $temp['place_id'] ) ? $temp['place_id'] : null;
      }
    }
  }

  update_post_meta( $post_id, 'location', $location );
}

//Add query args we can use to notify user of data that couldn't be saved
function add_notice_query_var( $redirect ) {
  remove_filter( 'redirect_post_location','add_notice_query_var', 99 );
  $location = $_POST['location'];
  $args = array();
  if(empty($location['address1'])){
    $args['missing_address'] = 'true';
  }
  if( empty($location['name']) ){
    $args['missing_name'] = 'true';
  }
  if( empty($location['city']) ){
    $args['missing_city'] = 'true';
  }
  if( empty($location['province']) ){
    $args['missing_province'] = 'true';
  }
  if( empty($location['postal_code']) ){
    $args['missing_postal_code'] = 'true';
  }
  return add_query_arg( $args, $redirect );
}


// Create error notices for missing fields
function msgps_save_notice__error() {
  $class = 'notice notice-error';

  if($_GET['missing_address']){
    $message = __( 'Please enter Address', 'sample-text-domain' );
    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
  }
  if($_GET['missing_name']){
    $message = __( 'Please enter Location', 'sample-text-domain' );
    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
  }
  if($_GET['missing_city']){
    $message = __( 'Please enter City', 'sample-text-domain' );
    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
  }
  if($_GET['missing_province']){
    $message = __( 'Please select a State', 'sample-text-domain' );
    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
  }
  if($_GET['missing_postal_code']){
    $message = __( 'Please enter a Postal Code', 'sample-text-domain' );
    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
  }

}

// Format address
function msgps_format_address( $location ) {
  $formatted = "";
  if ( is_array( $location ) ) {
    $formatted = $location['name']. " " .$location['address1'] . " " . $location['address2'] . ", " . $location['city'] . " " . $location['province'] . " " . $location['postal_code'];
    if ( ! empty( $location['country'] ) ) {
      $formatted .= ", " . $location['country'];
    }
  } elseif ( is_object( $location ) ) {
    $formatted = $location->name. " " .$location->address1 . " " . $location->address2 . ", " . $location->city . " " . $location->province . " " . $location->postal_code;
    if ( ! empty( $location->country ) ) {
      $formatted .= ", " . $location->country;
    }
  }

  return $formatted;
}

// function to geocode address, it will return NULL if unable to geocode address
function msgps_geocode( $address ) {
  return google_places_api::geocode( $address );
}

// Create a map centered on given Location id
// calling this multiple times on the same page will work but info window functions may not work as expected
function msgps_map_shortcode( $atts = [] ) {
  if ( empty( $atts ) ) {
    $atts = array();
  }
  // include the google js script with apiKey
  google_places_api::include_js_script();

  // include the styling for this plugin
  wp_enqueue_style( "wp-location-css", plugins_url( "css/msgps.css", __FILE__ ) );

  $location = array();
  if ( array_key_exists( "id", $atts ) ) {
    // load location by Id
    $new_location = MSGPS_Location::get_location_by_id( $atts['id'] );
    $new_location->fetch_place_hours();

    $locations[] = $new_location;

  } else {
    // load all locations
    $post_ids = get_posts(array('post_type' => 'wp-location', 'post_status' => 'publish', 'fields' => 'ids'));
    foreach ($post_ids as $post_id){

      $new_location = MSGPS_Location::get_location_by_id($post_id);
      $new_location->fetch_place_hours();

      $locations[] = $new_location;

    }
  }

  // return if there is no location to display information for
  if ( empty( $locations ) ) {
    return;
  }

  $style = array_key_exists( "style", $atts ) ? $atts["style"] . ";" : "";
  $class = array_key_exists( "class", $atts ) ? $atts["class"] . ";" : "";

  //include map js
  if(!wp_script_is('wp-location-map')) {
    wp_enqueue_script( 'wp-location-map', plugins_url( "js/msgps_map.js", __FILE__ ), array( 'jquery' ) );
  }

  include('templates/template-locations-map.php');
}

// Shortcode to Output building hours
function msgps_hours_shortcode( $atts = [] ) {
    if(empty($atts)){
        $atts = array();
    }
  $defaulted_atts = shortcode_atts( array(
    "type"  => "long",
    "style" => "",
    "class" => ""
  ), $atts );

  // include the styling for this plugin
  wp_enqueue_style( "wp-location-css", plugins_url( "css/msgps.css", __FILE__ ) );

  $locations = array();
if ( array_key_exists( "id", $atts ) ) {
    // load location by Id
    $new_location = MSGPS_Location::get_location_by_id( $atts['id'] );
    $new_location->fetch_place_hours();

      $locations[] = $new_location;

  } else {
  // load all locations
  $post_ids = get_posts(array('post_type' => 'wp-location', 'post_status' => 'publish', 'fields' => 'ids'));
  foreach ($post_ids as $post_id){

    $new_location = MSGPS_Location::get_location_by_id($post_id);
    $new_location->fetch_place_hours();

      $locations[] = $new_location;

  }
}

  if ( empty( $locations )) {
    return;
  }

  $style = $defaulted_atts["style"];
  $class = $defaulted_atts["class"];

  ob_start();
  switch ( $defaulted_atts['type'] ) {
    case 'long':
      include( 'templates/template-location-hours-long.php' );
      break;
    case 'short':
      include( 'templates/template-location-hours-short.php' );
      break;
    case 'today':
      include( 'templates/template-location-hours-today.php' );
      break;
  }
	$html = ob_get_contents();
  ob_end_clean();
  return $html;
}

// Shortcode to Output condensed location hours
function msgps_hours_short_shortcode( $atts = [] ) {
    if(empty($atts)){
        $atts = array();
    }

    $atts["type"] = "short";

    return msgps_hours_shortcode( $atts );
}

// Shortcode to Output location hours without condensing hours that are the same
function msgps_hours_long_shortcode( $atts = [] ) {
  if(empty($atts)){
    $atts = array();
  }
    $atts["type"] = "long";

    return msgps_hours_shortcode( $atts );

}

// Shortcode to Output location hours for the current day
function msgps_hours_today_shortcode( $atts = [] ) {
  if(empty($atts)){
    $atts = array();
  }
    $atts["type"] = "today";

    return msgps_hours_shortcode( $atts );

}
