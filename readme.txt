=== Drupal password encryption ===
Contributors: BevanR
Tags: drupal, password, import, migration, authentication, security
Requires at least: 4.1.1
Tested up to: 4.1.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Support Drupal's password encryption algorithms.  Most useful for users imported from a Drupal website.  Or simply for more secure password encryption.

== Description ==

The *Drupal password encryption* plugin enables support for Drupal's password encryption algorithms.

> A password encryption algorithm is the method is used to secure a password when preparing to save it to the database, such as when registering a new user account, changing a password, or checking if a password is correct when logging in.

This plugin is most useful for users imported from a Drupal website.  Users' passwords can be migrated (with no processing) from Drupal's `users.pass` database column to WP's `wp_users.user_pass` column.  Then, once the plugin is activated, users can login to the WP website using the same password they used to login to the Drupal website, without needing a password reset.

This plugin is also useful to simply enable stronger password encryption in WordPress.

Once this plugin is activated, any existing WP user (that was not migrated from Drupal) will still be able to login using their same password.  However, until they change or reset their password, it will remain encrypted using [PHPass](http://www.openwall.com/phpass/)—WP core's default password encryption algorithm.  This is possible because PHPass is one of several algorithms that Drupal supports.

The plugin works by overriding WP's pluggable functions `wp_hash_password()` and `wp_check_password()` to invoke the equivalent functions in [Drupal 7's `password.inc`](https://api.drupal.org/api/drupal/includes%21password.inc/7), which is included with the plugin's files.

This plugin may work on earlier versions of WordPress.  Please share your findings if you try it.

== Installation ==

1. Upload the `drupal-password-encryption/` directory to `wp-content/plugins/drupal-password-encryption/`.
1. Activate the plugin through the '*Plugins*' menu in WordPress.
1. Sleep more peacefully, knowing that if your database becomes compromised it will be significantly more difficult for the attacker to obtain the passwords of any users who register or change their password from now on.
1. Optionally, import users from a Drupal website, migrating `users.pass` in the Drupal website's database to `wp_users.user_pass` in the WP website's database.
1. Optionally, change the password of any user account whose security is more important, such as administrators.

== Frequently Asked Questions ==

= Will **existing** users need to reset their password before they can login? =

No.  Drupal supports WP core's password encryption algorithm too.  So they can continue to login using the same password even after the plugin is activated.

= Will **imported** users need to reset their password before they can login? =

No.  Once the plugin is activated and passwords correctly migrated, the plugin will allow imported users to login with the same password as they used on the Drupal website.

= What will the imported user's username and email address be? =

This plugin does not migrate any of the user's data for you.  Therefore imported users' usernames and email addresses will depend on how you import them from the source.

= Why does Drupal support multiple password encryption algorithms? =

Drupal has improved its password encryption algorithm several times.  But stored encrypted passwords can not be upgraded without the unencrypted password, which is only available when logging in.

Drupal upgrades stored passwords using older algorithms when that user logs in.  But it must continue to support all older password encryption algorithms until all of the stored passwords have been upgraded.  That might never be achieved.

= Does the plugin upgrade passwords encrypted by WP core? =

Not when logging in, like Drupal does, as described above.  However when a password is changed or reset Drupal's default encryption algorithm is used.

= What is wrong with WP core's password algorithm? =

Nothing per se.

However as computer hardware gets faster and hackers build more tools, encrypted data gets easier to break by "brute force".  More advanced encryption algorithms keep data security ahead of the curve and make it harder to hack encrypted data.

WP core's password algorithm (PHPass) is older than some alternatives.  Many tools (e.g. rainbow tables) are available to make it relatively easy for hackers to get plain text passwords from PHPass-encrypted data.

Of course, the attacker first needs a copy of your encrypted data (your database) before they can start trying to retrieve plain text passwords from the encrypted data.
