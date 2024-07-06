=== AJdG User Cleaner ===
Contributors: adegans
Tags: users, delete, delete users, user, accounts, delete accounts, register, user registration, registration, protection, woocommerce, bbpress
Donate link: https://www.arnan.me/donate.html
Requires at least: 4.9
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.0.7
License: GPLv3

If an account is registered and nothing is done with it the account is deleted after two weeks.

== Description ==
If you have a lot of people (or bots) registering on your site that end up doing nothing with their account they may as well be deleted. \
**AJdG User Cleaner** works on a daily schedule to check and delete unused accounts that registered 2 weeks before. This means that the new registrant has 2 weeks to do something with their account. Doing something means; post a comment, create a page or create a blogpost.

Additionally **AJdG User Cleaner** supports WooCommerce and bbPress. \
If the user creates an order in WooCommerce or a topic or reply in bbPress the account is not deleted.
If either WooCommerce or bbPress is not active those will not count towards deleting the users.

This works very simple, **AJdG User Cleaner** checks applicable accounts for the required activities. \
The simplicity of the plugin also means that no warning or notification is given when deleting accounts. \
Accounts are not placed in a trash bin. Deleting of accounts is permanent.

The Administrator, Editor and Author roles are excempt from deletion.

This plugin has no settings and works quietly in the background.

= Features =
* Daily delete unused accounts
* Check if the user has created a post or page
* Check if the user has replied to a post (eg. placed a comment)
* Supports WooCommerce checks if the account has orders
* Supports bbPress checks if topics and replies are posted by the account
* Sends an email to the administration email address with how many accounts were deleted that day

== Installation ==

1. Navigate to Plugins > Add New in your dashboard.
2. Search for 'AJdG User Cleaner' or 'Arnan User Cleaner' in the plugin database and click install on the **AJdG User Cleaner** plugin.
3. Activate **AJdG User Cleaner** when done.
4. Useless users will be deleted in the background from now on. This plugin has no settings.

== Frequently Asked Questions ==

= I have other activities not covered by this plugin =
**AJdG User Cleaner** does not check for other activities. So the user may be deleted.
If you feel your use-case should be included let me know.

= How do I know the plugin works =
**AJdG User Cleaner** sends a email notification when the cycle is finished and users have been deleted.
Alternatively you can check your Users dashboard, if a bunch went 'missing'... Well, there you go.

= Will this delete administrator accounts? =
**AJdG User Cleaner** will never delete Administrator, Editor or Author accounts.
All other accounts can be deleted.

= Can I recover deleted accounts? =
No, there is no trashbin or undo button.
The deleted user can of-course register a new account.

= Will this delete all user data? =
All associated meta data will be deleted using the official wp_delete_user() function.

= Where are the settings? =
This plugin has no settings.

= Will this overload the site? =
Generally no. However, if you have many accounts, say 3000 or more, the first cycle of deletions may include many accounts.
This can slow down your site for a few seconds. After that, only the new accounts are checked.
As an example - when I first used this plugin on a real site it had about 7700 accounts. 2500+ got deleted, this took about 10 seconds.

= I've cleaned out old accounts but they are not deleted =
Simply de-activate and re-activate the plugin to start over.
Remember, this counts as a first run, so the first cycle may take a few seconds.

= I need help with this plugin =
You can ask your questions on my [support forum](https://ajdg.solutions/forums/?mtm_campaign=ajdg_usercleaner).

= This is cool, do you have more plugins? =
Yep, check out my website [AJdG Solutions](https://ajdg.solutions/plugins/?mtm_campaign=ajdg_usercleaner)


== Changelog ==

= 1.0.7 - 6 July, 2024 =
* Author accounts are now also excempt from deletion
* Minor code tweaks
* Tested to work with WordPress 6.6
* Tested to work with ClassicPress 2.1.1
* Updated support links
* Updated readme.txt

= 1.0.6 - 31 January, 2023 =
* Tested to work with WordPress 6.1.1
* Tested to work with ClassicPress 1.5.1
* Updated support links
* Updated readme.txt

= 1.0.5 - 11 October, 2022 =
* Tested to work with WordPress 6+
* Tested to work with ClassicPress 1.4+
* Updated support links

= 1.0.4 - 25 January, 2022 =
* Tested to work with WordPress 5.9
* Tested to work with ClassicPress 1.3.1

= 1.0.3 - 20 June, 2021 =
* Updated readme.txt
* Tested to work with WordPress 5.7

= 1.0.2 - 25 January, 2021 =
* Happy New Year
* Updated readme.txt
* Tested to work with WordPress 5.6

= 1.0.1 - 3 August, 2020 =
* Tested to work with WordPress 5.5

= 1.0 =
* First version
