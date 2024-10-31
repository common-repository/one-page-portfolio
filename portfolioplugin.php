<?php
/** 
 * Plugin name: one page portfolio
 * Plugin URI: http://wordpress.org/extend/plugins/one-page-portfolio/
 * Author: Pengyi Wang
 * Author URI: http://www.themeright.net
 * Version: 1.2.3
 * Description: create one page portfolio, easy way.
 * 
 * License: GPL v3
 * Copyright 2012  Volkmar Kantor  (email : info@totalmedial.de)

 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

register_activation_hook( __FILE__, 'portfolio_set_default_options');

function portfolio_set_default_options(){
        
        if (get_option('opp_portfolio_options') === false) {

              
              
              $new_options['thumbnail_size'] = "thumbnail";
              $new_options['post_title_display'] = true;
              $new_options['desc_display_current'] = true;
              add_option('opp_portfolio_options', $new_options);

        }
        

	}


include_once(dirname(__FILE__).'/functions/settings.php');
include_once(dirname(__FILE__).'/html/workpage.php');
?>