<?php
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
          <?php
          foreach ( $location->condensed_hours as $value ) {
            ?>
            <div class="wp-location-hours-table-row">
              <span class="wp-location-hours-table-column"><?php echo $value; ?></span>
            </div>
            <?php
          }
          ?>
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