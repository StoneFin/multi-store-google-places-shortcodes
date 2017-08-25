<?php

class MSGPS_Location {
  public $id;
  public $place_id;
  public $alt_ids;
  public $name;
  public $latitude;
  public $longitude;
  public $address1;
  public $address2;
  // City
  public $city;
  // State
  public $province;
  // Country
  public $country="United States";
  // Zip
  public $postal_code;
  public $hours;
  public $condensed_hours;
  public $permanently_closed;
  public $formatted_address;

  public static function get_location_by_id($id){
    $obj = new MSGPS_Location();
    $obj->id = $id;
    $location = get_post_meta($id, 'location', true);
    if(empty($location)){
      return false;
    }else{
      $obj->place_id = $location['place_id'];
      $obj->name = $location['name'];
      $obj->address1 = $location['address1'];
      $obj->address2 = $location['address2'];
      $obj->city = $location['city'];
      $obj->province = $location['province'];
      $obj->country = $location['country'];
      $obj->postal_code = $location['postal_code'];
      $obj->latitude = $location['latitude'];
      $obj->longitude = $location['longitude'];
      $obj->format_address();
    }

    return $obj;
  }

  public function fetch_place_hours(){
    if(empty($this->place_id)){
      $this->hours = false;
    }else{
      $this->hours = google_places_api::get_place_hours($this->place_id);
      $this->condense_weekday_text($this->hours);
    }
  }


// Condense Hours information So it doesn't duplicate as much
  private function condense_weekday_text( $hours ) {
    if(empty($hours)){
      $this->condensed_hours = false;
      return;
    }
    $day_conversion_table = array(
      0 => "Sun",
      1 => "Mon",
      2 => "Tue",
      3 => "Wed",
      4 => "Thu",
      5 => "Fri",
      6 => "Sat"
    );

    $days_to_hours          = array();
    $condensed_weekday_text = array();
    foreach ( $hours->periods as $day => $business_hours ) {
      foreach ( $business_hours as $type => $info ) {
        $days_to_hours[ $info->day ][ $type ] = $info->time;
      }
    }

    $timespans_to_days = array();

    foreach ( $days_to_hours as $day => $times ) {
      $matched = false;

      foreach ( $timespans_to_days as $groupID => $grouping ) {
        if ( $this->sameSpan( $grouping, $times ) ) {
          $timespans_to_days[ $groupID ]['days'][] = $day;
          $matched                                 = true;

          break;
        }
      }

      if ( ! $matched ) {
        $new                 = $times;
        $new['days'][]       = $day;
        $timespans_to_days[] = $new;
      }
    }

    $day_to_group = array();

    foreach ( $timespans_to_days as $groupingID => $group ) {
      foreach ( $group['days'] as $key => $day ) {
        $day_to_group[ $day ] = $groupingID;
      }
    }

    $start_day       = false;
    $end_day         = false;
    $groupingID      = false;
    $arraySize       = sizeof( $day_to_group );
    $separate_sunday = false;
    for ( $i = 1; $i < $arraySize; $i ++ ) {
      if ( ! $start_day ) {
        $start_day  = $i;
        $groupingID = $day_to_group[ $start_day ];
      }

      if ( $i + 1 != $arraySize ) {
        if ( $day_to_group[ $i + 1 ] != $groupingID ) {
          $end_day = $i;
        }
      } else {
        if ( $day_to_group[0] != $groupingID ) {
          $end_day         = $i;
          $separate_sunday = true;
        } else {
          $end_day = 0;
        }
      }

      if ( $end_day ) {
        // Print the sections
        $time_string = date( "g:i A", strtotime( $timespans_to_days[ $groupingID ]['open'] ) ) . " - " . date( "g:i A", strtotime( $timespans_to_days[ $groupingID ]['close'] ) );
        if ( $start_day == $end_day ) {
          $date_string = $day_conversion_table[ $start_day ] . ": " . $time_string;
        } else {
          $date_string = $day_conversion_table[ $start_day ] . "-" . $day_conversion_table[ $end_day ] . ": " . $time_string;
        }

        $condensed_weekday_text[] = $date_string;

        if ( $separate_sunday ) {
          $sundayGroupID            = $day_to_group[0];
          $sunday_time_string       = date( "g:i A", strtotime( $timespans_to_days[ $sundayGroupID ]['open'] ) ) . " - " . date( "g:i A", strtotime( $timespans_to_days[ $sundayGroupID ]['close'] ) );
          $date_string              = $day_conversion_table[0] . ": " . $sunday_time_string;
          $condensed_weekday_text[] = $date_string;
        }

        // Reset variables
        $start_day  = false;
        $end_day    = false;
        $groupingID = false;
      }
    }

    $this->condensed_hours = $condensed_weekday_text;
  }

// check if two spans are the same
  private function sameSpan( $span1, $span2 ) {
    return ( ( $span1['close'] === $span2['close'] ) && ( $span1['open'] === $span2['open'] ) );
  }

  // Format address
  private function format_address() {
    $formatted = "";

    $formatted = $this->name. " " .$this->address1 . " " . $this->address2 . ", " . $this->city . " " . $this->province . " " . $this->postal_code;
    if ( ! empty( $this->country ) ) {
      $formatted .= ", " . $this->country;
    }


    $this->formatted_address = $formatted;
  }
}
