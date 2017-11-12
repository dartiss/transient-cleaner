=== Transient Cleaner ===
Contributors: dartiss
Donate link: https://artiss.blog/donate
Tags: cache, clean, database, housekeep, options, table, tidy, transient, update, upgrade
Requires at least: 4.6
Tested up to: 4.7.4
Stable tag: 1.5.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Housekeep expired transients from your options table. The original and best!

== Description ==

Housekeep expired transients from your options table. The original and best!

"Transients are a simple and standardized way of storing cached data in the WordPress database temporarily by giving it a custom name and a timeframe after which it will expire and be deleted."

Unfortunately, expired transients only get deleted when you attempt to access them. If you don't access the transient then, even though it's expired, WordPress will not remove it. This is [a known "issue"](http://core.trac.wordpress.org/ticket/20316 "Ticket #20316") but due to reasons, which are explained in the FAQ, this has not been adequately resolved.

Why is this a problem? Transients are often used by plugins to "cache" data (my own plugins included). Because of the housekeeping problems this means that expired data can be left and build up, resulting in a bloated database table.

Meantime, this plugin is the hero that you've been waiting for. Simply activate the plugin, sit back and enjoy a much cleaner, smaller options table. It also adds the additional recommendation that after a database upgrade all transients will be cleared down.

Technical specification...

* Licensed under [GPLv2 (or later)](http://wordpress.org/about/gpl/ "GNU General Public License")
* Designed for both single and multi-site installations
* PHP7 compatible
* Fully internationalized, ready for translations **If you would like to add a translation to his plugin then please head to our [Translating WordPress](https://translate.wordpress.org/projects/wp-plugins/artiss-transient-cleaner "Translating WordPress") page**

But, most importantly, there are no premium features and no adverts - this is 100% complete and free! See the notes below for how to get started as well as the more advanced features.

I'd like to thank WordPress Developer Andrew Nacin for his early discussion on this. Also, I'd like to acknowledge [the useful article at Everybody Staze](http://www.staze.org/wordpress-_transient-buildup/ "WordPress _transient buildup") for ensuring the proposed solution wasn't totally mad, and [W-Shadow.com](http://w-shadow.com/blog/2012/04/17/delete-stale-transients/ "Cleaning Up Stale Transients") for the cleaning code.

== The Settings Screen ==

Within `Administration` -> `Tools` -> `Transients` an options screen exists allowing you to tweak which of the various housekeeping you'd like to happen, including the ability to perform an ad-hoc run, and when you'd like the to be automatically scheduled.

You can even request an optimization of the options table to give your system a real "pep"!

== Running in Lite mode ==

A "lite" mode is available. By activating this the options screen will no longer appear and default settings will be used. The advantage? Improved performance to Admin and, especially if you're running multi-site, no chance of anybody "tinkering" with the settings.

To activate, add the following to your `wp-config.php` file...

`define( 'TC_LITE', true );`

== Using hooks ==

If you're the type of odd person who likes to code for WordPress (really?) then I've added a couple of hooks so you can call our rather spiffy housekeeping functions...

`housekeep_transients` - this will clear down any expired transients
`clear_all_transients` - this will remove any and all transients, expired or otherwise

== Installation ==

Transient Cleaner can be found and installed via the Plugin menu within WordPress administration (Plugins -> Add New). Alternatively, it can be downloaded from WordPress.org and installed manually...

1. Upload the entire `artiss-transient-cleaner` folder to your `wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress administration.

Voila! It's ready to go.

== Frequently Asked Questions ==

= Why hasn't this been fixed in the WordPress core? =

An attempt was made and lots of discussions ensued. Basically, some plugins don't use transients correctly and they use them as required storage instead of temporary cache data. This would mean any attempt by WordPress core to regularly housekeep transients may break some plugins and, hence, websites. WordPress didn't want to do this.

= Does that mean this plugin could break my site? =

If you have one of these badly written plugins, yes. However, I've yet to come across anybody reporting an issue.

= Have WordPress not done anything, then? =

Yes, they implemented the clearing down of all transients upon a database upgrade. If you have a multisite installation. And you're on the main site. They don't optimise the table after either, which this plugin does.

This could mean that the WordPress may run and ours as well but, well, if it's already been cleared then the second run isn't going to do anything so it doesn't add any overheads - it just ensures the optimisation occurs, no matter what.

= How often will expired transients be cleared down? =

Once a day and, by default, at midnight. However, the hour at which is runs can be changed in the settings screen.

It should be noted too that this will only run once the appropriate hour has passed AND somebody has been onto your site (with anybody visiting, the scheduler will not run).

= In the administration screen it sometimes refers to the number of transients and other times the number of records. What's the difference? =

A transient may consist of one or more records (normally a timed transient - the type that expires - has two) and without checking and matching them all up it can sometimes be hard to work out. So, where possible, it'll tell you the number of transients but, where it can't, it'll refer to the number of records on the database.

== Screenshots ==

1. Administration screen showing contextual help screen

== Changelog ==

[Learn more about my version numbering methodology](https://artiss.blog/2016/09/wordpress-plugin-versioning/ "WordPress Plugin Versioning")

= 1.5.3 =
* Enhancement: README updates to reflect changed plugin directory
* Maintenance: Minimum WordPress level for the plugin has been raised to 4.6, so various changes have been made to accommodate that
* Maintenance: Because of the new minimum WordPress level loading of the language scripts is no longer required. The folder has also been removed, as has the link to it

= 1.5.2 =
* Bug: Oops. Although it seemed to work fine on my test system, it looks as if the code for the new 'lite' mode was causing some users errors. I've now (I hope) corrected that. Apologies.

= 1.5.1 =
* Maintenance: Beware the Atom editor and it's default setting of appending extra blank lines! Extra lines have now been removed from the bottom of various files
* Maintenance: Also took the opportunity to correct my site URLs, as my domain has recently changed (the old URLs still work as I'm smart enough to put redirects in place but, still, it's neater to do it properly)

= 1.5 =
* Enhancement: A new option has been added to allow you to run in "lite" mode, where no option screen will be present and default settings will be used. Useful for multi-site installations or just where you want to run with minimal performance impact
* Enhancement: Re-instated the code change that I removed in 1.4.1 - this time it performs a version check and only calls the extra function if available
* Enhancement: After WP 4.6 you no longer need to load the plugin's text domain. So I don't!
* Enhancement: Added a links sidebar to the help drop-down
* Maintenance: Changed the menu names so they no longer clash with other plugins
* Maintenance: Making use of yoda conditions to ensure stability of code
* Bug: Sorted bug which meant that changing the scheduled run time didn't work

= 1.4.2 =
* Maintenance: Updated branding, inc. adding donation links

= 1.4.1 =
* Bug: Awww... biscuits. I was being smart by including a call to a function to check something without realising you have to have WordPress 4.4 for it to work. Thankfully, it's not critical so I've removed it for now and will add a "proper" solution in future

= 1.4 =
* Enhancement: Re-written core code to work better with multisite installations
* Enhancement: Administration screen re-written to be more "in keeping" with the WordPress standard layout. More statistics about cleared transients are also shown
* Enhancement: Instead of piggy-backing the housekeeping schedule (which some people turn off) I've instead implemented my own - it defaults to midnight but, via the administration screen, you can change it to whatever hour floats your boat
* Enhancement: For those nerdy enough that they want to code links to our amazing cleaning functions, we've added some super whizzy hooks. Check the instructions about for further details
* Maintenance: This is now a Code Art production, so the author name has been updated and the donation link (including matching plugin meta) ripped out. I for one welcome our new overlords.
* Maintenance: Renamed the functions that began with atc_ to tc_
* Maintenance: I admit it, I've been naughty. I've been hard-coding the plugin folder in INCLUDES. Yes, I know. But I've fixed that now
* Maintenance: I've validated, sanitized, escaped and licked the data that's sent around the options screen. Okay, I didn't do that last one
* Bug: Some PHP errors were vanquished

= 1.3.1 =
* Maintenance: Added a text domain and domain path

= 1.3 =
* Enhancement: Added links to settings in plugin meta
* Enhancement: Updated admin screen headings for WP 4.3
* Enhancement: Now used time() instead of gmmktime(), so as to follow strict usage
* Bug: Big PHP error clean-up

= 1.2.4 =
* Maintenance: Updated links on plugin meta

= 1.2.3 =
* Bug: Removed PHP error

= 1.2.2 =
* Enhancement: Options are now only available to admin (super admin if a multisite)
* Bug: Removed reporting of "orphaned" transients - these are actually transients without a timeout

= 1.2.1 =
* Maintenance: Updated the branding of the plugin
* Enhancement: Added support link to plugin meta

= 1.2 =
* Maintenance: Split files because of additional code size
* Maintenance: Removed run upon activation
* Enhancement: Improved transient cleaning code efficiency (including housekeeping MU wide transients)
* Enhancement: Added administration screen (Tools->Transients) to allow ad-hoc runs and specify run options
* Enhancement: Show within new admin screen whether orphaned transients have been found (in this case full clear of the option table is recommended)
* Enhancement: Added internationalisation
* Enhancement: If external memory cache is in use display an admin box to indicate this plugin is not required

= 1.1 =
* Enhancement: Transients will be initially housekept when the plugin is activated

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.5.3 =
* Various maintenance changes