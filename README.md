# Transient Cleaner

<img src="https://ps.w.org/artiss-transient-cleaner/assets/icon-128x128.png" align="left">Remove expired transients from your options table. The original and best!

**This plugin is designed only for WordPress 5.8 or below, as transient cleaning is part of core functionality after that point. 

In addition, this is the final version of this plugin, and it will no longer be maintained, other than urgent security or bug fixes.**

"Transients are a simple and standardized way of storing cached data in the WordPress database temporarily by giving it a custom name and a timeframe after which it will expire and be deleted."

Unfortunately, expired transients only get deleted when you attempt to access them. If you don't access the transient then, even though it's expired, WordPress will not remove it. This is [a known "issue"](http://core.trac.wordpress.org/ticket/20316 "Ticket #20316") but due to reasons, which are explained in the FAQ, this has not been adequately resolved.

Why is this a problem? Transients are often used by plugins to "cache" data (my own plugins included). Because of this it means that expired data can be left and build up, resulting in a bloated database table.

Meantime, this plugin is the hero that you've been waiting for. Simply activate the plugin, sit back and enjoy a much cleaner, smaller options table. It also adds the additional recommendation that after a database upgrade all transients will be cleared down.

I'd like to thank WordPress Developer Andrew Nacin for his early discussion on this. Also, I'd like to acknowledge [the useful article at Everybody Staze](http://www.staze.org/wordpress-_transient-buildup/ "WordPress _transient buildup") for ensuring the proposed solution made sense, and [W-Shadow.com](http://w-shadow.com/blog/2012/04/17/delete-stale-transients/ "Cleaning Up Stale Transients") for the cleaning code.

Iconography is courtesy of the very talented [Janki Rathod](https://www.fiverr.com/jankirathore) ♥️

<p align="right"><a href="https://wordpress.org/plugins/artiss-transient-cleaner/"><img src="https://img.shields.io/wordpress/plugin/dt/artiss-transient-cleaner?label=wp.org%20downloads&style=for-the-badge">&nbsp;<img src="https://img.shields.io/wordpress/plugin/stars/artiss-transient-cleaner?color=orange&style=for-the-badge"></a></p>