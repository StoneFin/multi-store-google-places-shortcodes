(function($) {

    // Create a new Google Map
    function new_map( $el ) {
        // Get Map Markers
        var $markers = $($el).data('marker_info');

        var args = {
            zoom        : 16,
            center      : new google.maps.LatLng(0,0),
            mapTypeID   : google.maps.MapTypeId.ROADMAP
        };

        var map = new google.maps.Map( $el[0], args);

        map.markers = [];
        // setup map markers
        $markers.forEach(function(element){
           add_marker( element, map);
        });

        center_map( map );

        return map;
    }

    // Add a map marker to the given map
    function add_marker( $marker, map ){
        var latlng = new google.maps.LatLng( $marker.latitude, $marker.longitude );

        // create marker
        var marker = new google.maps.Marker({
            position	: latlng,
            map			: map,
            title       : $marker.name
        });

        // add to array
        map.markers.push( marker );

        // if marker contains HTML, add it to an infoWindow
        if( $marker.formatted_address )
        {
            // create info window
            var infowindow = new google.maps.InfoWindow({
                content		: $marker.formatted_address
            });

            // show info window when marker is clicked
            google.maps.event.addListener(marker, 'click', function() {
                if( prev_infowindow ) {
                    prev_infowindow.close();
                }

                prev_infowindow = infowindow;
                infowindow.open( map, marker );

            });
        }
    }

    /*
        Center a given Google Map on its markers
     */
    function center_map( map ) {

        // vars
        var bounds = new google.maps.LatLngBounds();

        // loop through all markers and create bounds
        $.each( map.markers, function( i, marker ){

            var latlng = new google.maps.LatLng( marker.position.lat(), marker.position.lng() );

            bounds.extend( latlng );

        });

        // only 1 marker?
        if( map.markers.length == 1 )
        {
            // set center of map
            map.setCenter( bounds.getCenter() );
            map.setZoom( 16 );
        }
        else
        {
            // fit to bounds
            map.fitBounds( bounds );
        }

    }

    var map = null;
    var prev_infowindow = null;

    $(document).ready(function () {
        $('.wp-location-map').each(function(){
           map = new_map( $(this) );
        });
    });
})(jQuery);