=== Campaign Archive Block for Mailchimp ===

Contributors: Kau-Boy
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7914504
Stable Tag: 2.2.1
Requires at least: 5.0
Tested up to: 6.7
Requires PHP: 5.6
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.txt
Tags: blocks, campaign, archive, mailchimp, block

Adds a block to show an archive for Mailchimp campaigns.

== Description ==

In order to be able to use the plugin, you have to get API credentials from Mailchimp. You can find them in the [API keys section](https://us1.admin.mailchimp.com/account/api/) of your Mailchimp account. If you already use the plugin "Mailchimp" or "Mailchimp for WooCommence", the API key from these will automatically be used.

The plugin allows you to show all campaigns from the connected Mailchimp account. You can show up to 100 campaign (this is a hard limit of the Mailchimp marketing API). For each campaign, you can either show the "Title" or the "Subject". You can also show the author/sender as well as the send date (and time).

This is not an official Mailchimp plugin. Mailchimp® is [a registered trademark](https://mailchimp.com/legal/copyright/) of The Rocket Science Group.

== Frequently Asked Questions ==

= I get a warning telling me, that I have to set up API credentials! =

In order to be able to use the plugin, you have to get API credentials from Mailchimp. You can find them in the [API keys section](https://us1.admin.mailchimp.com/account/api/) of your Mailchimp account. You can also find a guide on how to "[Generate your API key](https://mailchimp.com/developer/marketing/guides/quick-start/#generate-your-api-key)" in the Mailchimp Marketing API documentation. This API key have to be saved in the block settings.

= I already use the plugin "Mailchimp" or "Mailchimp for WooCommence", do I need to get a new API key? =

If you have successfully connected one of the two plugins to your Mailchimp account, this plugin will automatically use the existing API key from those plugins, so you are good to go without any further configuration. You can still use another key in the block settings.

= I've just sent a new campaign/newsletter, but it is not showing up! =

For better front end performance the campaign archive ist cached for 60 minutes. You just have to wait one hour (at most) for the new campaign to show up. You can also change the cache curation using the `cabfm_cache_minutes` filter.

== Screenshots ==

1. The block in the editor with its settings to adjust the output of the block and the API key settings
2. The block before setting up the API key

== Changelog ==

= 2.2.1 =
* Update dependencies and recompile assets
* Add Plugin Check GitHub Action

= 2.2.0 =
* Adding the two filters `cabfm_campaigns_query_args` and `cabfm_campaigns_markup` to enable changes on the queried or rendered campaigns.

= 2.1.0 =
* If one of the plugins "Mailchimp" or "Mailchimp for WooComemrce" already has an API key, use this for the plugin as well
* Reduce caching duration to 60 minutes and add the `cabfm_cache_minutes` filter to change this value

= 2.0.1 =
* Migrate previously entered API keys into new block settings

= 2.0.0 =
* Remove the settings page and use the block settings for the API key
* Prepare the block to be listed in the block directory

= 1.0.3 =
* Remove horizontal margin from the unordered list as well

= 1.0.2 =
* Optimize frontend CSS and remove horizontal margin from the list

= 1.0.1 =
* Remove function loading the translations from the plugin folder

= 1.0.0 =
* First stable version
