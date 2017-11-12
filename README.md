# Transient Cleaner

Housekeep expired transients from your options table. The original and best!

"Transients are a simple and standardized way of storing cached data in the WordPress database temporarily by giving it a custom name and a timeframe after which it will expire and be deleted."

Unfortunately, expired transients only get deleted when you attempt to access them. If you don't access the transient then, even though it's expired, WordPress will not remove it. This is [a known "issue"](http://core.trac.wordpress.org/ticket/20316 "Ticket #20316") but due to reasons, which are explained in the FAQ, this has not been adequately resolved.

Why is this a problem? Transients are often used by plugins to "cache" data (my own plugins included). Because of the housekeeping problems this means that expired data can be left and build up, resulting in a bloated database table.

Meantime, this plugin is the hero that you've been waiting for. Simply activate the plugin, sit back and enjoy a much cleaner, smaller options table. It also adds the additional recommendation that after a database upgrade all transients will be cleared down.

I'd like to thank WordPress Developer Andrew Nacin for his early discussion on this. Also, I'd like to acknowledge [the useful article at Everybody Staze](http://www.staze.org/wordpress-_transient-buildup/ "WordPress _transient buildup") for ensuring the proposed solution wasn't totally mad, and [W-Shadow.com](http://w-shadow.com/blog/2012/04/17/delete-stale-transients/ "Cleaning Up Stale Transients") for the cleaning code.
