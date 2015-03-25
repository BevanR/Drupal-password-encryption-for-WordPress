<?php
namespace BevanR\DrupalPasswordEncryption\Test;

/**
 * auth.php is an unmodified copy of WP core's Auth test.
 *
 * @see http://develop.svn.wordpress.org/trunk/tests/phpunit/tests/auth.php
 */
require('auth.php');

/**
 * Extend and alter WP core's Auth test.
 */
class AuthTest extends \Tests_Auth
{
    /**
     * Overrides a WP core Auth test method to handle Drupal's password hashing algorithm:
     *   - Password length limit for hashing is 4096 in WP and 512 in Drupal.
     *   - Hashed passwords start with "$P$" in WP and "$S$" in Drupal.
     */
    public function test_password_length_limit()
    {
        $passwords = array(
            // @note These THREE lines are modified to test Drupal password-hashing.
            str_repeat('a', 511), // short
            str_repeat('a', 512), // limit
            str_repeat('a', 513), // long
        );

        $user_id = $this->factory->user->create(array('user_login' => 'password-length-test'));

        wp_set_password($passwords[1], $user_id);
        $user = get_user_by('id', $user_id);
        // phpass hashed password
        // @note This ONE line is modified to test Drupal password-hashing.
        $this->assertStringStartsWith('$S$', $user->data->user_pass);

        $user = wp_authenticate('password-length-test', $passwords[0]);
        // Wrong Password
        $this->assertInstanceOf('WP_Error', $user);

        $user = wp_authenticate('password-length-test', $passwords[1]);
        $this->assertInstanceOf('WP_User', $user);
        $this->assertEquals($user_id, $user->ID);

        $user = wp_authenticate('password-length-test', $passwords[2]);
        // Wrong Password
        $this->assertInstanceOf('WP_Error', $user);


        wp_set_password($passwords[2], $user_id);
        $user = get_user_by('id', $user_id);
        // Password broken by setting it to be too long.
        $this->assertEquals('*', $user->data->user_pass);

        $user = wp_authenticate('password-length-test', '*');
        $this->assertInstanceOf('WP_Error', $user);

        $user = wp_authenticate('password-length-test', '*0');
        $this->assertInstanceOf('WP_Error', $user);

        $user = wp_authenticate('password-length-test', '*1');
        $this->assertInstanceOf('WP_Error', $user);

        $user = wp_authenticate('password-length-test', $passwords[0]);
        // Wrong Password
        $this->assertInstanceOf('WP_Error', $user);

        $user = wp_authenticate('password-length-test', $passwords[1]);
        // Wrong Password
        $this->assertInstanceOf('WP_Error', $user);

        $user = wp_authenticate('password-length-test', $passwords[2]);
        // Password broken by setting it to be too long.
        $this->assertInstanceOf('WP_Error', $user);
    }
}
