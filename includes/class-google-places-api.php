<?php

// Handle all interactions with the Google API
class google_places_api {

  // Retrieve API Key from options
	private static function get_apiKey() {
		$key = get_option( 'googlemaps_api_key' );
		return $key;
	}

	// Retrieve API version from options or default
	public static function get_version() {
		$version = get_option( 'googlemaps_api_version' );
		return empty($version) ? '3' : $version;
	}

	// Enqueue Google Maps Script if not enqueued
	public static function include_js_script() {
	  // We don't want this script loaded twice otherwise things will break
	  if(!wp_script_is('google-maps-api')){
      wp_enqueue_script( 'google-maps-api', "https://maps.googleapis.com/maps/api/js?key=" . self::get_apiKey() . "&v=" . self::get_version() );
    }
	}

	// Retrieve geographic information about an address
	public static function geocode( $address ) {
		// url encode the address
		$address = urlencode( $address );

		// google map geocode api url
		$url = "http://maps.google.com/maps/api/geocode/json?address={$address}";

		// get the json response
		$resp_json = file_get_contents( $url );

		// decode the json
		$resp = json_decode( $resp_json, true );

		// response status will be 'OK', if able to geocode given address
		if ( $resp['status'] == 'OK' ) {

			// get the important data
			$lati              = $resp['results'][0]['geometry']['location']['lat'];
			$longi             = $resp['results'][0]['geometry']['location']['lng'];
			$place_id          = $resp['results'][0]['place_id']; // the current way doesn't handle sublocations very well and may not be the POI place id
			$formatted_address = $resp['results'][0]['formatted_address'];

			// verify if data is complete
			if ( $lati && $longi && $formatted_address ) {

				// put the data in the array
				$data_arr              = array();
				$data_arr['latitude']  = $lati;
				$data_arr['longitude'] = $longi;
				$data_arr['formatted'] = $formatted_address;
				$data_arr['place_id']  = $place_id;

				return $data_arr;
			}
		}

		return null;
	}

	// Get information about place hours from Google Places
	public static function get_place_hours( $place_id ) {
		$current_time = new DateTime( "now" );
		$current_time->setTimezone( new DateTimeZone( 'America/Chicago' ) );
		$json = wp_remote_get( 'https://maps.googleapis.com/maps/api/place/details/json?' . 'placeid=' . $place_id . '&key=' . self::get_apiKey() );

		try {
			$place_object = json_decode( $json['body'] );
			// print_r( $place_object );

			return $place_object->result->opening_hours;
		} catch ( Exception $e ) {
			return false;
		}
	}
}
