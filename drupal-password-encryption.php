<?php
/*
Plugin Name: Drupal Password Encryption
Plugin URI: https://wordpress.org/plugins/drupal-password-encryption/
Description: Support Drupal's password encryption algorithms.  Most useful for users imported from a Drupal website.  Or simply for more secure password encryption.
Author: BevanR
Version: trunk
Author URI: http://www.JS.geek.nz
*/

/**
 * Override WP's password hashing algorithm.
 *
 * When activating the plugin, WP's definition of these pluggable functions is
 * already declared.  Only include this plugin's definition once they do not exist.
 *
 * @see wp-includes/pluggable.php
 */
if (!function_exists('wp_hash_password') && !function_exists('wp_check_password')) {
    require_once('drupal/adaptor.php');
}
