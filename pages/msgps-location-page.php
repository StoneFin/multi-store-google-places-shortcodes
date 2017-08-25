<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}
// fetch current values
$location = get_post_meta($post->ID, 'location', true);
$states     = array(
  "Alabama",
  "Alaska",
  "Arizona",
  "Arkansas",
  "California",
  "Colorado",
  "Connecticut",
  "Delaware",
  "Florida",
  "Georgia",
  "Hawaii",
  "Idaho",
  "Illinois",
  "Indiana",
  "Iowa",
  "Kansas",
  "Kentucky",
  "Louisiana",
  "Maine",
  "Maryland",
  "Massachusetts",
  "Michigan",
  "Minnesota",
  "Mississippi",
  "Missouri",
  "Montana",
  "Nebraska",
  "Nevada",
  "New Hampshire",
  "New Jersey",
  "New Mexico",
  "New York",
  "North Carolina",
  "North Dakota",
  "Ohio",
  "Oklahoma",
  "Oregon",
  "Pennsylvania",
  "Rhode Island",
  "South Carolina",
  "South Dakota",
  "Tennessee",
  "Texas",
  "Utah",
  "Vermont",
  "Virginia",
  "Washington",
  "West Virginia",
  "Wisconsin",
  "Wyoming"
);
?>
<div class="wrap">
  <form>
    <?php wp_nonce_field( 'test9000', 'msgps_box_content_nonce' ); ?>
    <fieldset>
      <table class="form-table">
        <tbody>
        <tr class="form-field">
          <th scope="row">
            <label for="location[place_id]">Google Place Id:</label>
          </th>
          <td>
            <input type="text" name="location[place_id]" value="<?php echo isset($location['place_id']) ? $location['place_id'] : ''; ?>"
                   placeholder=""/>
          </td>
        </tr>
        <tr class="form-field">
          <td colspan="2">
            <button type="button" onClick="alert('Feature not Complete');">
              <span>Fill Form With Google Place Info</span>
            </button>
          </td>
        </tr>
        <tr class="form-field form-required">
          <td colspan="2">
            <span name="blah"><i>Find Place ID at <a href="https://developers.google.com/places/place-id" target="_blank">this</a> location</i></span>
          </td>
        </tr>
        <tr class="form-field form-required">
          <th scope="row">
            <label for="location[name]">Location Name: <span class="description">(required)</span></label>
          </th>
          <td>
            <input type="text" name="location[name]" value="<?php echo isset($location['name']) ? $location['name']: ''; ?>"
                   placeholder="Dr. Muffin's Residence"/>
          </td>
        </tr>
        <tr class="form-field form-required">
          <th scope="row">
            <label for="location[address1]">Address: <span class="description">(required)</span></label>
          </th>
          <td>
            <input type="text" name="location[address1]" value="<?php echo isset($location['address1']) ? $location['address1']: ''; ?>"
                   placeholder="123 Drury Lane"/>
          </td>
        </tr>
        <tr class="form-field">
          <th scope="row">
            <label for="location[address2]">Address2:</label>
          </th>
          <td>
            <input type="text" name="location[address2]" value="<?php echo isset($location['address2']) ? $location['address2']: ''; ?>"
                   placeholder=""/>
          </td>
        </tr>
        <tr class="form-field form-required">
          <th scope="row">
            <label for="location[city]">City:<span class="description">(required)</span></label>
          </th>
          <td>
            <input type="text" name="location[city]" value="<?php echo isset($location['city']) ? $location['city']: ''; ?>"
                   placeholder="Lincoln"/>
          </td>
        </tr>
        <tr class="form-field form-required">
          <th scope="row">
            <label for="location[province]">State:<span class="description">(required)</span></label>
          </th>
          <td>
            <select name="location[province]">
              <?php
              foreach ( $states as $state ) {
                ?>
                <option
                  <?php if ( (isset($location['province']) ? $location['province'] : '') == $state ) {
                    echo 'selected="selected"';
                  } ?>
                  value="<?php echo $state; ?>"><?php echo $state; ?></option>
                <?php
              }
              ?>
            </select>
          </td>
        </tr>
        <tr class="form-field">
          <th scope="row">
            <label for="location[country]">Country:</label>
          </th>
          <td>
            <input type="text" name="location[country]" value="<?php echo isset($location['country']) ? $location['country'] : ''; ?>"/>
          </td>
        </tr>
        <tr class="form-field form-required">
          <th scope="row">
            <label for="location[postal_code]">Postal Code: <span
                class="description">(required)</span></label>
          </th>
          <td>
            <input type="text" name="location[postal_code]" value="<?php echo isset($location['postal_code']) ? $location['postal_code'] : ''; ?>"
                   placeholder="68508"/>
          </td>
        </tr>
        <tr class="form-field">
          <th scope="row">
            <label for="location[latitude]">Latitude:</label>
          </th>
          <td>
            <input type="text" name="location[latitude]" value="<?php echo isset($location['latitude']) ? $location['latitude'] : ''; ?>"
                   placeholder=""/>
          </td>
        </tr>
        <tr class="form-field">
          <th scope="row">
            <label for="location[longitude]">Longitude:</label>
          </th>
          <td>
            <input type="text" name="location[longitude]" value="<?php echo isset($location['longitude']) ? $location['longitude'] : ''; ?>"
                   placeholder=""/>
          </td>
        </tr>
        </tbody>
      </table>
    </fieldset>
  </form>
</div>