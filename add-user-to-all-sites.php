<?php
/**
 * Author URI:        https://github.com/barryceelen/
 * Author:            Barry Ceelen
 * Description:       A CLI command that allows you to add a user to all sites in a multisite network.
 * Domain Path:       /languages
 * License:           GPLv3+
 * Plugin Name:       Add User to All Sites
 * Plugin URI:        https://github.com/barryceelen/add-user-to-all-sites/
 * Text Domain:       add-user-to-all-sites
 * Version:           1.0.0
 * Requires PHP:      5.3.0
 * Requires at least: 3.1.0
 * GitHub Plugin URI: barryceelen/add-user-to-all-sites
 *
 * @package AddUserToAllSites
 */

if ( ! defined( 'WP_CLI' ) ) {
	return;
}

/**
 * CLI command class.
 */
class Add_User_To_All_Sites_Command {
	/**
	 * Adds a user to all sites in a multisite network.
	 *
	 * ## OPTIONS
	 *
	 * --email=<email>
	 * : The email address of the user.
	 *
	 * --role=<role>
	 * : The role that should be assigned to the user on each site. Defaults to subscriber if not set. If the user
	 *   already exists on a site its role will be updated.
	 *
	 * ## EXAMPLES
	 *
	 *     wp add_user_to_all_sites <user-email> --role=administrator
	 *
	 * @param array $args The array of arguments.
	 * @param array $assoc_args The array of assoc args.
	 * @return void
	 */
	public function __invoke( $args, $assoc_args ) {

		if ( ! is_email( $assoc_args['email'] ) ) {
			WP_CLI::error( "The {$assoc_args['email']} email address is not valid." );
		}

		$user = get_user_by( 'email', $assoc_args['email'] );

		if ( ! $user ) {
			WP_CLI::error( "No user found with the {$assoc_args['email']} email address." );
		}

		$error_count = 0;
		$role        = empty( $assoc_args['role'] ) ? 'subscriber' : trim( $assoc_args['role'] );
		$sites       = get_sites();

		foreach ( $sites as $site ) {

			$url    = untrailingslashit( $site->domain . $site->path );
			$result = add_user_to_blog( $site->blog_id, $user->ID, $role );

			if ( is_wp_error( $result ) ) {

				$message = $result->get_error_message();

				WP_CLI::log( "An error occurred adding user to {$url}: {$message}" );

			} else {
				WP_CLI::log( "User added to {$url}" );
			}
		}

		WP_CLI::log(
			sprintf(
				'Adding user %s to all sites completed%s.',
				$assoc_args['email'],
				0 === $error_count ? '' : " with {$error_count} errors."
			)
		);
	}
}

WP_CLI::add_command(
	'add_user_to_all_sites',
	'Add_User_To_All_Sites_Command',
	array(
		'before_invoke' => function () {
			if ( ! is_multisite() ) {
				WP_CLI::error( 'This is not a multisite installation.' );
			}
		},
	)
);
