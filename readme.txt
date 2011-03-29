=== Social Connect ===
Contributors: app2technologies, thenbrent
Tags: facebook, wordpress.com, twitter, google, yahoo, openid, windows live, social, login, register
Requires at least: 3.0
Tested up to: 3.1
Stable tag: 0.4

Give your visitors a way to comment, login and register with their Twitter, Facebook, Google, Yahoo, Windows Live or WordPress.com account.

== Description ==

Social Connect adds social login buttons on the login, register and comment forms of your WordPress site.

The buttons offer login and registration using a Twitter, Facebook, Google, Yahoo, Windows Live or WordPress.com account.

It makes it super easy for new members to register with your site and existing members to login.

== Installation ==

1. Upload everything into the "/wp-content/plugins/" directory of your WordPress site.
2. Activate in the "Plugins" admin panel.
3. Visit the "Settings | Social Connect" administration page to configure. 

== Frequently Asked Questions ==

= Does Social Connect work with WordPress Multisite? =

Yes.

= Does Social Connect work with the Theme My Login plugin? =

Yes.

= Do I need to add template tags to my theme? =

No social connect works with the default WordPress login and registration forms. 

= Why doesn't Social Connect Work? =

If you have White Label CMS installed, its javascript breaks all plugins which run javascript on the login form. 

For a quick fix and for more information see here: http://wordpress.org/support/topic/social-connect-does-not-work-at-all?replies=7#post-2029255

If you don't have White Label CMS installed, please double check your settings then post a question in the [Support Forums](http://wordpress.org/tags/social-connect?forum_id=10#postform) with the version of the plugin, WordPress, list of other plugins installed and any error messages you receive. 

== Screenshots ==

1. **Register** - On the registration form, buttons for 3rd party services are provided.

== Changelog ==

= 0.1 =
* Initial beta release. 

= 0.2 =
* Fix for directory name

= 0.3 =
* Social Connect moved to it's own top level menu in wp-admin.
* Enable/disable integration with each social provider.
* Simplified setup for Windows Live.
* Introduced diagnostics to check for required cryptographic extensions and server rewrite rules.

= 0.4 =
* Removing generic OpenID for security concerns: http://wordpress.org/support/topic/545420
* Only calling deprecated registration.php file if WP < 3.1 http://wordpress.org/support/topic/540156

