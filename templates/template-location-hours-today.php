<?php
$day_conversion_table = array( 0 => 5, 1 => 0, 2 => 1, 3 => 2, 4 => 3, 5 => 4, 6 => 6 );
if(isset($locations)){
  foreach ($locations as $location) {
    $hours = $location->hours;
    if(empty($hours)){
      ?>
      <div class="wp-location-hours-container">
        <div class="wp-location-hours-status">
          <p>Failed to Retrieve Open Hours for <?php echo $location->name;?></p>
        </div>
      </div>
      <?php
    }else {
      ?>
      <div class="wp-location-hours-container <?php echo $class; ?>" style="<?php echo $style; ?>">
        <div class="wp-location-hours-title">
          <p><?php echo $location->name; ?></p>
        </div>
        <div class="wp-location-hours-status">
          <p>Doors are: <?php echo( $hours->open_now ? "Open" : "Closed" ); ?></p>
        </div>
        <div class='wp-location-hours-table'>
            <div class="wp-location-hours-table-row">
              <span class="wp-location-hours-table-column"><?php echo $hours->weekday_text[ $day_conversion_table[ date( 'w' ) ] ]; ?></span>
            </div>
        </div>
      </div>
      <?php
    }
  }
}else{
  ?>
  <div class="wp-location-hours-container">
    <div class="wp-location-hours-status">
      <p>No Locations Found</p>
    </div>
  </div>
  <?php
}