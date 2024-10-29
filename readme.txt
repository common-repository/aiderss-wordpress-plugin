=== Plugin Name ===
Contributors: denishua
Tags: RSS,Feed
Donate link: http://fairyfish.net/donate/
Requires at least: 2.0
Tested up to: 2.5
Stable tag: 0.2

Show the top posts for your WordPress blog base on AideRSS

== Description ==

<p>AideRSS is a service that helps you to prioritize news feeds based on the amount of social activity around them. Using an algorithm called PostRank, which tracks the number of comments, Digg votes, del.icio.us bookmarks and more, it will process any feed you enter and spit out feeds of All Posts, Good Posts, Great Posts, Best Posts and the Top 20.</p>

<p>AideRSS have released API and PHP Class. Base on the PHP Class, I create a simple WordPress plugin which can display the top posts for the specified time period.</p>

== Installation ==

1. Upload the folder advanced-post-image to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php aide_get_top_posts('month',10); ?>` in your templates