<?php
/*
Plugin Name: AJdG User Cleaner
Plugin URI: https://ajdg.solutions/?mtm_campaign=usercleaner
Author: Arnan de Gans
Author URI: https://www.arnan.me/?mtm_campaign=usercleaner
Description: Delete unused accounts. If an account is registered and nothing is done with it the account is deleted after two weeks. This plugin has no settings.
Text Domain: ajdg-user-cleaner
Version: 1.0.6
License: GPLv3
*/

/* ------------------------------------------------------------------------------------
*  COPYRIGHT NOTICE
*  Copyright 2020-2023 Arnan de Gans. All Rights Reserved.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from its use.
------------------------------------------------------------------------------------ */

register_activation_hook(__FILE__, 'ajdg_usercleaner_activate');
register_deactivation_hook(__FILE__, 'ajdg_usercleaner_deactivate');
add_action('ajdg_usercleaner', 'ajdg_usercleaner');
add_filter('plugin_action_links_' . plugin_basename( __FILE__ ), 'ajdg_usercleaner_action_links');

/*-------------------------------------------------------------
 Name:      ajdg_usercleaner_action_links
 Purpose:	Plugin page link
 Since:		1.0
-------------------------------------------------------------*/
function ajdg_usercleaner_action_links($links) {
	$links['ajdg-usercleaner-help'] = sprintf('<a href="%s" target="_blank">%s</a>', 'https://ajdg.solutions/forums/forum/user-cleaner/?mtm_campaign=usercleaner&mtm_kwd=action-links', 'Support');
	$links['ajdg-usercleaner-ajdg'] = sprintf('<a href="%s" target="_blank">%s</a>', 'https://ajdg.solutions/?mtm_campaign=usercleaner&mtm_kwd=action-links', 'ajdg.solutions');

	return $links;
}

/*-------------------------------------------------------------
 Name:      ajdg_usercleaner_activate
 Purpose:	Set the daily routine on activation
 Since:		1.0
-------------------------------------------------------------*/
function ajdg_usercleaner_activate() {
	ajdg_usercleaner();
	if(!wp_next_scheduled('ajdg_usercleaner')) {
		wp_schedule_event(time(), 'daily', 'ajdg_usercleaner');
	}
}

/*-------------------------------------------------------------
 Name:      ajdg_usercleaner_deactivate
 Purpose:	Clean up after de-activation
 Since:		1.0
-------------------------------------------------------------*/
function ajdg_usercleaner_deactivate() {
	wp_clear_scheduled_hook('ajdg_usercleaner');
	delete_option('ajdg_user_cleaner');
}

/*-------------------------------------------------------------
 Name:      ajdg_usercleaner
 Purpose:	Check and possibly remove users
 Since:		1.0
-------------------------------------------------------------*/
function ajdg_usercleaner() {
	global $wpdb;

	require_once(ABSPATH.'wp-admin/includes/user.php' );

	$timer = time() - 1209600; // Two weeks
	$timer = date('U', $timer);
	$deleted = 0;

	// Grab all applicable users
	$last_user_id = get_option('ajdg_user_cleaner', 1);
	$all_users = $wpdb->get_col("SELECT `ID` FROM `{$wpdb->prefix}users` WHERE UNIX_TIMESTAMP(`user_registered`) < '{$timer}' AND `ID` > {$last_user_id};");

	$do_wc = (function_exists('wc_get_customer_order_count')) ? 'yes' : 'no';
	$do_bbp = (function_exists('bbp_get_user_topic_count_raw')) ? 'yes' : 'no';

	// Check users
	foreach($all_users as $user_id) {
		$user = get_userdata($user_id);

		if(!in_array('administrator', $user->roles, true) AND !in_array('editor', $user->roles, true)) {
			// Has published posts?
			$posts = count_user_posts($user_id, 'post');
			// Has published pages?
			$pages = count_user_posts($user_id, 'page');
			// Has comments?
			$comments = get_comments(array('count' => true, 'user_id' => $user_id));
			// Has WooCommerce orders?
			$orders = ($do_wc == 'yes') ? wc_get_customer_order_count($user_id) : 0;
			// Has bbPress Topics?
			$topics = ($do_bbp == 'yes') ? bbp_get_user_topic_count_raw($user_id) : 0;
			// Has bbPress Replies?
			$replies = ($do_bbp == 'yes') ? bbp_get_user_reply_count_raw($user_id) : 0;

			if($posts == 0 AND $pages == 0 AND $comments == 0 AND $orders == 0 AND $topics == 0 AND $replies == 0) {
				wp_delete_user($user_id);
				$deleted++;
			} else {
				$last_user_id = $user_id;
			}
		}
		unset($user_id, $user, $posts, $pages, $comments, $orders, $topics, $replies);
	}

	// Save last checked user ID so we don't recheck valid accounts
	update_option('ajdg_user_cleaner', $last_user_id);

	// Notify the website administrator if accounts have been deleted
	if($deleted > 0) {
		$notify_email = get_option('admin_email');
		$message = "<p>Hello,</p>";
		$message .= "<p>{$deleted} accounts were deleted.</p>";
		$message .= "<p>Have a nice day!<br /><a href=\"https://ajdg.solutions/plugins/\" target=\"_blank\">AJdG Solutions plugins</a></p>";
	    $headers[] = "Content-Type: text/html; charset=UTF-8";
		$headers[] = "Reply-To: {$notify_email} <{$notify_email}>";
		wp_mail($notify_email, "[AJdG User Cleaner] Accounts deleted!", $message, $headers);
	}

	unset($all_users, $deleted, $last_user_id, $do_wc, $do_bbp);
}
?>
