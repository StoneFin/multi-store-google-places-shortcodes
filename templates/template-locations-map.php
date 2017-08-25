<?php
if(isset($locations)) {
  ?>
  <div class="wp-location-map <?php echo $class; ?>"
       style="<?php echo $style; ?>" data-marker_info='<?php echo json_encode($locations);?>'></div>
  <?php
}else{

}