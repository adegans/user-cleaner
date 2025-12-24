<?php
/*
Plugin Name: User Cleaner
Plugin URI: https://ajdg.solutions/product/user-cleaner/
Author: Arnan de Gans
Author URI: https://www.arnan.me/
Description: Delete unused accounts. If an account is registered and nothing is done with it the account is deleted after two weeks. This plugin has no settings.
Version: 1.1
License: GPLv3

Requires at least: 5.8
Requires PHP: 8.0
Requires CP: 2.0
Tested CP: 2.6
Premium URI: https://ajdg.solutions/
GooseUp: compatible
*/

/* ------------------------------------------------------------------------------------
*  COPYRIGHT NOTICE
*  Copyright 2020-2026 Arnan de Gans. All Rights Reserved.

*  COPYRIGHT NOTICES AND ALL THE COMMENTS SHOULD REMAIN INTACT.
*  By using this code you agree to indemnify Arnan de Gans from any
*  liability that might arise from its use.
------------------------------------------------------------------------------------ */

register_activation_hook(__FILE__, 'ajdg_usercleaner_activate');
register_uninstall_hook(__FILE__, 'ajdg_usercleaner_deactivate');
add_action('ajdg_usercleaner', 'ajdg_usercleaner');
add_filter('plugin_row_meta', 'ajdg_usercleaner_meta_links', 10, 2);

/*-------------------------------------------------------------
 Name:      ajdg_usercleaner
 Purpose:	Check and possibly remove users
-------------------------------------------------------------*/
function ajdg_usercleaner() {
	global $wpdb;

	require_once(ABSPATH.'wp-admin/includes/user.php' );

	$two_weeks_ago = time() - 1209600;
	$deleted = 0;

	// Grab all applicable users
	$last_user_id = get_option('ajdg_user_cleaner', 1);
	$all_users = $wpdb->get_col("SELECT `ID` FROM `{$wpdb->prefix}users` WHERE UNIX_TIMESTAMP(`user_registered`) < '{$two_weeks_ago}' AND `ID` > {$last_user_id};");

	$do_wc = (function_exists('wc_get_customer_order_count')) ? 'yes' : 'no';
	$do_bbp = (function_exists('bbp_get_user_topic_count_raw')) ? 'yes' : 'no';

	// Check users
	foreach($all_users as $user_id) {
		$user = get_userdata($user_id);

		if(!in_array('administrator', $user->roles, true) AND !in_array('editor', $user->roles, true) AND !in_array('author', $user->roles, true)) {
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

/*-------------------------------------------------------------
 Name:      ajdg_usercleaner_activate
 Purpose:	Set the daily routine on activation
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
-------------------------------------------------------------*/
function ajdg_usercleaner_deactivate() {
	wp_clear_scheduled_hook('ajdg_usercleaner');
	delete_option('ajdg_user_cleaner');
	delete_option('ajdg_activate_ajdg-user-cleaner'); // Must match slug
	delete_transient('ajdg_update_ajdg-user-cleaner'); // Must match slug
}

/*-------------------------------------------------------------
 Name:	  	ajdg_wc_coupon_catcher_meta_links
 Purpose:	Extra links on the plugins dashboard page
-------------------------------------------------------------*/
function ajdg_wc_coupon_catcher_meta_links($links, $file) {
	if($file !== 'ajdg-user-cleaner/ajdg-user-cleaner.php') return $links;

	$links['ajdg-help'] = sprintf('<a href="%s" target="_blank">%s</a>', 'https://support.ajdg.net/knowledgebase.php', 'Plugin Support');
	$links['ajdg-more'] = sprintf('<a href="%s" target="_blank">%s</a>', 'https://ajdg.solutions/plugins/', 'More plugins');	
	if(!is_plugin_active('gooseup/gooseup.php')) {
		$links['ajdg-gooseup'] = sprintf('<a href="%s" target="_blank">%s</a>', 'https://ajdg.solutions/product/gooseup-plugin-update-system/', 'Get GooseUp to enable updates');
	}

	return $links;
}
?>