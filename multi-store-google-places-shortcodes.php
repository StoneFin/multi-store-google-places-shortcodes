<?php
/*
Plugin Name: Multi Store Google Places ShortCodes Settings
Plugin URI: https://github.com/StoneFin/multi-store-google-places-shortcodes
Description: Wordpress plugin for adding shortcodes that a)display business hours from the Google Places API and b) a Google Map with pins on each store location
Version: 1
Author: Stone Fin Technology
Author URI: https://www.stonefin.com
Text Domain: multi-store-google-places-shortcodes
Domain Path: 
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see http://www.gnu.org/licenses/

You can contact us at info@stonefin.com
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
//include admin settings
require_once ("admin/multi-store-google-places-shortcodes-admin-settings.php");
//store location functionality
require_once ("store-locations.php");
?>