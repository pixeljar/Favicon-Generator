=== Plugin Name ===
Contributors: brandondove, lordleiter
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3441397
Tags: favicon
Requires at least: 2.8
Tested up to: 2.9.2
Stable tag: 1.5

== Description ==

This plugin will allow you to upload an image in the format of a jpeg, gif or png and will convert the image into a <a href="http://en.wikipedia.org/wiki/Favicon" target="_blank">favicon</a> formatted file. This file will be placed in your WordPress root directory and a link tag will then be added to your html output.

== Installation ==

1. Upload the `favicon-generator` folder to your `/wp-content/plugins/` directory.
2. Make sure that the `favicon-generator` folder and the `favicon-generator/uploads` folder have their permissions set to 777.
3. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= What file types do you support? =

We support JPEG, GIF and PNG. Note: In order for this plugin to properly generate images, your server must have PHP configured with the GD2 library.

= How do I make this work? =

1. Find the Favicon Generator section in WordPress.
2. Upload an image. Generally speaking this would be your logo or something of that nature.
3. That's it! Your website now has an awesome favicon.

= Why isn't my favicon showing up? =

Some browsers hang on to old favicon images in their cache. This is an unfortunate side effect of caching. If you make a change to your favicon and don't immediately see the change, don't start banging your head against the wall. This is not an indication that this plugin is not working. Try <a href="http://en.wikipedia.org/wiki/Bypass_your_cache" target="_blank">emptying your cache</a> and quitting the browser.

If that doesn't work, it could also be because your theme doesn't make use of WordPress' wp_head() function. If this is the case, sadly you'll have to edit your theme. This function is what allows us to automagically insert the favicon code. Most modern themes comply with this WordPress standard, so this really shouldn't be the problem unless you built your own custom theme or are using a really old theme.

== Version History ==

= Version 1.1 =

1. Added PHP 4 Compatibility
2. Updated the ImageIco library from <http://www.jpexs.com/php.html>
3. Cannot delete active favicon
4. Added highlight to active favicon

= Version 1.2 =

1. Forgot to add the new ImageIco library via SVN. This version has it. Sorry about the mixup.

= Version 1.3 =

1. AAAARGH! Include error was happening because I forgot about case sensitivity in Linux filesystems. Sorry, I'm on a mac.

= Version 1.4 =

1. Supressed errors that appeared if the upload directory wasn't created. Added in creation of the upload directory to the init routines.

= Version 1.5 =

1. Added support for ads from pluginsponsors.com to be placed on the admin page. A guy's gotta earn a living, right?
2. Tested functionality against WordPress version 2.9.2
3. Increased the minimum WordPress version to 2.8 because, come on...who's still running WordPress 2.1?
4. Added checks to make sure certain necessary directories and permissions were set properly.
5. Suppressed ugly warnings that were displayed when the upload directories weren't set properly.
6. Added support for l10n.

== Screenshots ==

1. Favicon Generator configuration screen
2. Shows the favicon in place