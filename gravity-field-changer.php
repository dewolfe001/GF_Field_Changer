<?php

/**
 * Plugin Name: Gravity Forms Field Changer
 * Plugin URI: https://wordpress.org/plugins/gravity-field-changer/
 * Description: Change Gravity Form fields from one available field to another field type keeping as many of the form settings as possible.
 * Version: 0.91
 * Author: Shawn DeWolfe
 * Author URI: https://products.shawndewolfe.com/
 * Requires at least: 4.1
 * Tested up to: 4.9.8

 * Text Domain: gf-field-changer
 * 
 * @package gravity-field-changer
 * @category Core
 * @author dewolfe001

------------------------------------------------------------------------

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
**/

define( 'GF_FIELD_CHANGER_VERSION', '0.91' );
define( 'GF_FIELD_CHANGER_ID', 0 );

add_action( 'gform_loaded', array( 'GF_Field_Changer_Bootstrap', 'load' ), 5 );

class GF_Field_Changer_Bootstrap {

    public static function load() {
        if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
            return;
        }

        // 23/09/2018 ? require_once( 'class-gfimagefields.php' );


		// If Gravity Forms is enabled, require the image field class
		if ( class_exists( 'GF_Fields' ) ) {
			require_once( 'class-gf-field-changer.php' );
		}

        GFAddOn::register( 'GF_Field_Changer' );
    }
}

function gf_field_changer() {
	return false; // 23/09/2018 ... for now
    // 23/09/2018 return GFImageFields::get_instance();
}
