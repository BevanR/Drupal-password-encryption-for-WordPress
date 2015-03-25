<?php
/**
 * A WP adaptor for Drupal 7's password hashing algorithm.
 *
 * Drupal's own password.inc file is included, unmodified.  All but one of the
 * functions it defines are used by this adapter.
 *
 * @see password.inc
 */

require_once('password.inc');


/**
 * Overrides WP's password hashing function.
 *
 * @param $password
 *
 * @return bool|string
 */
function wp_hash_password($password)
{
    // Use Drupal's password hashing function..
    return user_hash_password(trim($password)) ?: '*';
}

/**
 * Overrides WP's password checking function.
 *
 * @param string $password
 * @param string $hash
 * @param string $user_id
 *
 * @return bool
 */
function wp_check_password($password, $hash, $user_id = '')
{
    // A pseudo Drupal user account object for user_check_password().
    $account = (object) ['pass' => $hash];

    // Use Drupal's password hashing function.
    return user_check_password($password, $account);
}

/**
 * Stubs Drupal's variable_get().
 *
 * @param string $name
 * @param mixed  $default
 *
 * @return mixed
 */
function variable_get($name, $default)
{
    // Pass through the default value.
    return $default;
}

/**
 * Returns a string of highly randomized bytes (over the full 8-bit range).
 *
 * Copied verbatim from Drupal because password.inc requires it.
 * @see https://api.drupal.org/api/drupal/includes%21bootstrap.inc/function/drupal_random_bytes/7
 *
 * This function is better than simply calling mt_rand() or any other built-in
 * PHP function because it can return a long string of bytes (compared to < 4
 * bytes normally from mt_rand()) and uses the best available pseudo-random
 * source.
 *
 * @param $count
 *   The number of characters (bytes) to return in the string.
 */
function drupal_random_bytes($count)
{
    // $random_state does not use drupal_static as it stores random bytes.
    static $random_state, $bytes, $has_openssl;

    $missing_bytes = $count - strlen($bytes);

    if ($missing_bytes > 0) {
        // PHP versions prior 5.3.4 experienced openssl_random_pseudo_bytes()
        // locking on Windows and rendered it unusable.
        if (!isset($has_openssl)) {
            $has_openssl = version_compare(PHP_VERSION, '5.3.4',
                    '>=') && function_exists('openssl_random_pseudo_bytes');
        }

        // openssl_random_pseudo_bytes() will find entropy in a system-dependent
        // way.
        if ($has_openssl) {
            $bytes .= openssl_random_pseudo_bytes($missing_bytes);
        }

        // Else, read directly from /dev/urandom, which is available on many *nix
        // systems and is considered cryptographically secure.
        elseif ($fh = @fopen('/dev/urandom', 'rb')) {
            // PHP only performs buffered reads, so in reality it will always read
            // at least 4096 bytes. Thus, it costs nothing extra to read and store
            // that much so as to speed any additional invocations.
            $bytes .= fread($fh, max(4096, $missing_bytes));
            fclose($fh);
        }

        // If we couldn't get enough entropy, this simple hash-based PRNG will
        // generate a good set of pseudo-random bytes on any system.
        // Note that it may be important that our $random_state is passed
        // through hash() prior to being rolled into $output, that the two hash()
        // invocations are different, and that the extra input into the first one -
        // the microtime() - is prepended rather than appended. This is to avoid
        // directly leaking $random_state via the $output stream, which could
        // allow for trivial prediction of further "random" numbers.
        if (strlen($bytes) < $count) {
            // Initialize on the first call. The contents of $_SERVER includes a mix of
            // user-specific and system information that varies a little with each page.
            if (!isset($random_state)) {
                $random_state = print_r($_SERVER, true);
                if (function_exists('getmypid')) {
                    // Further initialize with the somewhat random PHP process ID.
                    $random_state .= getmypid();
                }
                $bytes = '';
            }

            do {
                $random_state = hash('sha256', microtime() . mt_rand() . $random_state);
                $bytes .= hash('sha256', mt_rand() . $random_state, true);
            } while (strlen($bytes) < $count);
        }
    }
    $output = substr($bytes, 0, $count);
    $bytes = substr($bytes, $count);

    return $output;
}
