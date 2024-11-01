=== Share by Email ===
Contributors: lehelm
Donate link: https://www.lehelmatyus.com
Tags: share, email, share by email, share by email, send post by email
Requires at least: 3.8
Tested up to: 6.2
Requires PHP: 5.6
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Lightweight plugin that gives your readers an easy way to share your content via their email client. A classic Share via Email link.

== Description ==

Lightweight plugin that gives your readers an easy way to share your content via email.

A classic Share via Email link. It provides you with a shortcode that you can add to your pages, posts or any other content type that turns into a share link on your website. You can also add it to your template files to have it show up by default on all content.

Once a user clicks the "Share by email" link with icon it will open their default email client with pre-populated Subject and Email body. Such as:

**Subject:** Your website name | The title of the post they are sharing
**Email Body: ** You may be interested in this article: .. Excerpt of the post.. - Link to the post

== Features ==

* The plugin is designed to load minimal resources so your website stays fast
* You can customize what shows in your readers email client when they are sharing your posts.
* There are tokens available for you to further customize the email message of your readers
* Optimized for website speed unlike some of the bulky plugins that provide JS heavy features

== How to Use ==

* Install and activate the plugin
* Navigate to Settings > Share By Email > General Settings Tab and configure to look of the link
* Navigate to "Email Settings" Tab and configure the default email texts your readers will see
* Use tokens available for you to customize the message they see when they hit share
* Place **[sbe-share-by-email]** shortcode to anywhere on your page where you can add shortcodes, such as content, widgets etc.
* Or place `<?php echo do_shortcode("[sbe-share-by-email]"); ?>` in any of your template files
* Try the link and share your Content via Email

== Tokens Available ==

To customize the message you have the following tokens available:

* [blogname] - The name of your wordpress website that was set Appearance > Customize > Site Identity
* [title] - Title of the post your reader is on when sharing the link
* [excerpt] - The excerpt of the post your reader is on when sharing the link
* [link] - The URL of the post your reader is on when sharing the link


== Installation ==

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don’t need to leave your web browser. Log in to your WordPress dashboard, go to the Plugins menu and click Add New.

Type "Share by Email" and click Search Plugins. Once you’ve found this plugin you can install it by simply clicking “Install Now”.

= Manual installation =

To manually install the plugin downloading the plugin and uploading it to your webserver via your favorite FTP client application. The WordPress codex contains [instructions on how to do this here](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

1. Upload `share-by-email` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

= Frequently Asked Questions ==

= Can I modify the style  =

Yes, the link is by default minimally styled, so it's easy for you to add basic CSS in your theme to customize it to your liking. If you need a developers help to theme it you can always reach out to Here's a link to [www.Lehelmatyus.com](https://www.lehelmatyus.com/ "Lehelmatyus.com")

= Can I add it to a Widget =

Yes, as long as you can run the shortcode in a widget it should be fine. Check out "Shortcode Widget" plugin by "Gagan Deep Singh" to make it easier.

= Can I add it to a Template File =

Yes, just make sure you add it to a template file where the global $post variable is present if you are using Tokens in you email message.

== Screenshots ==

1. The Link
2. The link in Action
3. Settings
4. Email Settings

== Changelog ==

= 1.0.2 =
* Fix dropdown not updating on settings page issue.

= 1.0.1 =
* Fix special character issue in titles

= 1.0.0 =
* Initial public release
