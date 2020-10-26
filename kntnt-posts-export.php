<?php


/**
 * @wordpress-plugin
 * Plugin Name:       Kntnt Posts Export
 * Plugin URI:        https://www.kntnt.com/
 * GitHub Plugin URI: https://github.com/Kntnt/kntnt-post-export
 * Description:       Provides a tool to export posts with images, attachments, author, terms and metadata that can be imported with Kntnt Posts Import.
 * Version:           0.3.3
 * Author:            Thomas Barregren
 * Author URI:        https://www.kntnt.com/
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 */


namespace Kntnt\Posts_Export;

// Uncomment following line to debug this plugin.
// define( 'KNTNT_POSTS_EXPORT_DEBUG', true );

require 'autoload.php';

defined( 'WPINC' ) && new Plugin;