=== Wordbooker ===

Contributors: SteveAtty
Tags: facebook, minifeed, newsfeed, crosspost, WPMU, Facebook Share, Facebook Like, social media
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=8XNJMQCYDJT6U&lc=GB&currency_code=GBP&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Requires at least: 2.9
Tested up to: 3.3.1
Stable tag: 2.1.8

This plugin allows you to cross-post your blog posts to your Facebook Wall and to any Facebook Fan Page / Group that you are an administrator for. 

== Description ==

This plugin allows you to cross-post your blog posts to your Facebook Wall / Fan Page Wall / Group Wall. You can Post as an Extract, A Status Update or even as a Note. 

NOTE : You MUST have the PHP Curl module enabled and configured in such a way that it can connect to the Facebook Servers on a secure HTTP connection. If you do not have curl OR if your hosting company block curl access to externals sites you cannot use this plugin.


== IMPORTANT ==  

Wordbooker 2.0 is a completely new implementation of most of the original Wordbooker functionality. You will need to revisit the Options screen to reset your configuration


== Upgrading Wordbooker from Version 1.x ==

If you are upgrading from version 1.x then DO NOT deactivate the plugin before you upgrade as this will remove all the settings and remove the tables which means you will loose all your posting/comment history. To upgrade Wordbooker to Version 2 you should download the latest version of the plugin, delete the wordbooker folder on the server and then then upload the wordbooker folder into your wp-plugins folder. Once you've done that you need to go into the Plugins menu and DE-ACTIVATE and the RE-ACTIVATE Wordbooker. People running Networked Blogs can do a Network Activation at this point. PLEASE NOTE:  Version 2 does not, at the moment import information about posts made in Version 1.x but I am looking to add it as an option in a future release if there is a demand for it.


== Installation ==

1. [Download] (http://wordpress.org/extend/plugins/wordbooker/) the latest version of Wordbooker.
1. Unzip the ZIP file.
1. Upload the `wordbooker` directory to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress. Admins of Networked Blogs can active the plugin network wide.
1. Navigate to `Options` &rarr; `Wordbooker` for configuration and follow the on-screen prompts.


== Features ==

For more details on the various features please read the additional Features.txt file or check the [wordbooker](http://blogs.canalplan.org.uk/steve/category/wordbooker/) category on my blog which will contain information on the current and planned features list.

- Works with a complementary [Facebook application](http://www.facebook.com/apps/application.php?id=254577506873) to update your Facebook Wall and friends' News Feeds about your blog and page postings.
- Supports multi-author blogs: each blog author notifies only their own friends of their blog/page posts.
- Features a sidebar widget to display your current Facebook Status and picture. Multiple widgets can be supported in one single blog.
- Features a sidebar widget to display a "Fan"/Like box for any of your pages. Multiple widgets can be supported in one single blog.
- Features a Facebook Like Button which can be customised as to where it appears in your blog.
- Supports the posting of blog posts to Fan Pages and Groups (if you are an administrator of that page or group).


== Frequently Asked Questions ==

= Isn't Wordbooker the same as importing my blog posts into Facebook Notes? =

It is certainly similar, but not the same:

- Facebook Notes imports and caches your entire blog post - Wordbooker uses the Facebook API to actively update your Facebook Wall just as if you had posted an update yourself on facebook.com. It also means that you can make changes to your blog postings *after* initially publishing them.

- With Wordbooker, your blog postings will have their own space in your Facebook Wall - just as if you'd written directly on to the wall yourself.

- Your updates will show up with a nifty WordPress logo next to them instead of the normal "Notes" icon, plus a link back to the full entry on your blog.


= Why doesn't the Facebook Like / Facebook Share show up properly even though I've enabled it?

You may need to add the following to the HMTL tag in your theme : xmlns:fb="http://www.facebook.com/2008/fbml".
So it looks something like :  <html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:fb="http://www.facebook.com/2008/fbml">

 
= Why aren't my blog posts showing up in Facebook? =

- Wordbooker will not publish password-protected posts.

- Any errors Wordbooker encounters while communicating with Facebook will be recorded in error logs; the error logs (if any) are viewable in the "Wordbooker" panel of the "Options" WordPress admin page.



= My WordPress database doesn't use the default 'wp_' table prefix. Will this plugin still work? =

Yes, and its also WPMU/ WP Networked Blogs mode compliant.



= How do I reset my Wordbooker/WordPress configuration so I can start over from scratch? =

1. Click the "Reset configuration" button in the "Wordbooker" panel of the "Options" WordPress admin page.
1. Deactivate the Wordbook plugin from your WordPress installation.
1. [Uninstall Wordbooker](http://www.facebook.com/apps/application.php?id=254577506873) from your Facebook account.
1. Download the [latest version](http://wordpress.org/extend/plugins/wordbooker/)
1. Re-install and re-activate the plugin.


= What is the Enable Extended description for Share Link option do? =

If you're using the Share action link on your posts to Facebook it uses the META DESCRIPTION tag to extract something from your post. If you dont have an SEO system which populates this, or if you dont usally use post excerpts then selecting this option populates the tag with the first couple hundred characters of your post which gives a nice block of text in the post that will appear when people share your post on their wall.



= How do I report problems or submit feature requests? =

- Use the [Wordbooker Support Forums](http://wordbooker.tty.org.uk/forums/). Either start a new topic, or add to an existing topic.

- Alternatively, for major feature requests go to the Wordbooker Page on [GoogleCode](http://code.google.com/p/wordbooker/).


== KNOWN CONFLICTS ==

There will be conflicts with other plugins providing Facebook Like/Send Share functionality


== Screenshots ==

1. Wordbooker Options/Configuration : Blog Level options
1. Wordbooker Options/Configuration : User Level options
2. Wordbooker Options : Overrides when posting

== Changelog ==

= Version 2.1.8 23/02/2012 =
- Roll back the FB javascript to an earlier version which doesn't seem to have the problems that the current code does.
- Recode the DB upgrade script to fix some odd problems.
- Remove some redundant, for now, timeline action permissions.

= Version 2.1.7 23/02/2012 =
- Fix a typo in the FB javascript code which only seems to affect Safari.

= Version 2.1.6 22/02/2012 =
- Total embarassment : I somehow rolled a version with a function call in it that shouldn't have been there


= Version 2.1.5 21/02/2012 =
- Coded round the fact that get_users is a WP>3.0 function. So users on WP<3.1 dont get the option to chose the admin user for diagnostic messages
- Added extra diagnostic for comments that have already ben pulled from Facebook
- Reviewed and ratified all the diagnostic message levels.
- Fixed an obscure bug where the FB Comments block didn't show if you turned off Wordbooker's FB Like and Share buttons.
- Change image handling code so that filenames with single quotes in them don't blow things up. As Wordpress uses " " round image URLs this should be OK.
- Fixed a typo in the user guide - all the short tags had [[ ]] rather than [ ]
- Disabled the frictionless sharing option as it was doing some very odd things - like even though I was using an APP access token it was filling MY timeline up.
- Removed a duplicated xmlns tag.


= Version 2.1.4 16/02/2012 = 
- Rebuild for SVN after WP restored the plugin and messed things up


= Version 2.1.3 16/02/2012 = 
- Rebuild for SVN after WP restored the plugin and messed things up


= Version 2.1.2 16/02/2012 = 
- Lost in SVN after WP restored the plugin and messed things up


= Version 2.1.1 16/02/2012 =
- Removed the Thanks section to keep Wordpress mavens happy
- Recoded the strip_images function to hopefully get rid of the phantom url problem (urls with no image on the end of them).
- Fixed a bug in the comment inport/export where the check for disabling incoming comments used the outgoing comment disabled flag!


= Version 2.1.0 15/02/2012 = 
- Added comment handling - too many individual changes to comment
- Added a function to delete Wordbooker data when a post is deleted.
- Fixed a long standing bug relating to the og:image tag when posts have no image
- Fixed a long standing bug relating to the og:locale tag - this is now populated with the Wordpress Language value.
- Removed lots of old obsolete commented out debug code.
- Fixed a bug in the logic that identifies malformed image urls.
- Added option to use blog url or blog tag line under the post title in wall posts
- Added New Post Type - "Share" to the list of available post types.
- Fixed a bug with post settings being lost when posts were scheduled
- Fixed a bug where scheduled post diagnostics got recorded against the wrong user.
- Added an option to allow the user id that "admin" diagnostic messages should be recorded against
- Added code to remove wordbooker_channel.php - a file that was added but never used because FB never proved its worth.
- Added a feature to allow Facebook User images to replace Gravatars where a FB URL is detected.
- Added PHP Memory limit line to the support information block
- Added Curl SSL Timeout lines to try to make Wordbooker more reslient to lousy Facebook API performance


= Version 2.0.9 12/12/2011 =
- Finally fixed (I hope) the blank image problem for posts with no obvious image.
- Fixed a bug relating to scraping when og tag production is turned off.
- Added Russian Language files (Thanks Филипп Борисов for the hard work)
- Recoded the cron job to reduce FQL load.
- Checked for compatability with Wordpress 3.3


= Version 2.0.8 19/11/2011 =
- Commented out a debug statement that got left behind.
- Added table prefix line to the support information - trying to debug problems with differing versions of WPMU.
- Tweaked duplicate post fire detection code.


= Version 2.0.7 18/11/2011 =
- Fixed a problem related to Scheduled Posts not getting pushed to Facebook
- Fixed a problem relating to Quick Edit over riding existing post options on posts
- Fixed a problem when Wordbooker is used with the Transcript theme
- Rolled back a couple of the JSON-STRING parameters where they weren't needed
- Recoded part of the Cache Refresh process to try to make it more resilient to Facebook Server timeouts/failures.


= Version 2.0.5 / 2.0.6 15/11/2011 =
- QTranslate processing was missing round one of the post content extracts
- Added code to pull the "viewing" language from qtranslate and use that to change the language of the various FB Social widgets.
- Fixed a bug where parameters were not being passed properly to the notes publishing call
- Fix a bug where the Fan page drop down was always populated with the fan pages and groups of the first user in the wordbooker tables even if there was more than one row.
- Pushed language identification into a function to make it easier to add support for other multi-language plugins later.
- More language strings snagged and tagged
- Language files for French and German added. Thanks to Sebastian Pertsch and Christian Denat
- Changed the size of the wordbooker_blank image to resolve some issues with FB ignoring it.
- Fixed a bug in the Like/Share button logic which meanr that under certain circumstances the code for buttons wasn't included when it should have been.
- Put some checking in the cron code to handle Facebook API timing out during the refresh process and leaving things in a mess
- Fixed a typo in the "Disable Short Urls" option.
- Changed graph calls to use JSON_STRING parameter rather than just JSON (Undocumented Facebook API parameter).


= Version 2.0.4 06/11/2011 =
- Added a Memory usage line to the support information.
- Removed a duplicated constant definition
- Fixed a typo in the fb_widget include.
- Fixed a bug where Save Draft didn't save the Wordbooker options
- Completely changed the Diagnostic/Error log handling.
- Clarified some of the diagnostic messages in the cache refresh code
- Fixed a glitch in the FB Status widget when FB style time formats were used.
- Added L10n handling to the FB Status Widget
- Completed L10n handling in the FB Like Widget
- Added languages folder and first revision of the wordbooker pot file
- Added function call to handle localisation
- Recoded the image handling process to make it more reliable
- Added a trim diagnostic log call to the batch cron job
- Added a check for expired sessions in the cron code.
- Added a ID line in the support information
- Fixed a bug with the "TEST MODE" option
- Fixed a problem relating to base table prefixes in some Networked blog installs.
- Added an extra detail to the target drop down list to differentiate between pages and applications with the same name.


= Version 2.0.3 23/10/2011 =
- Fixed a bug in the code that populates the og:content tag
- Fixed a bug related to mbr string handling
- Changed calls from get_user_meta to get_usermeta which means plugin now works with 2.9 again
- When posting to your personal wall the diagnostic messages showed no target name.
- Logic for Remote publishing clients revised
- Added option to disable short urls on FB posts
- Added a post revision check to hopefully fix double posting issues
- Recoded the "get logged in user" logic in the Cache Refresh to fix an obscure bug related to lost FB IDs
- Changed the level of a couple of diagnostic messages to make sure they always show.
- Changed the logic for the primary and secondary targets so that secondary doesn't appear if you only have a personal wall and the drop down is removed from the primary.
- Added option to use the default og:image tag as the default image for posts with no image.


= Version 2.0.2 15/10/2011 =
- Fixed a bug with app-id/fb:admins which made comment moderation go wrong.
- Fixed a bug in the target handling code where a variable was not being set correctly leading to confusing error message
- Changed Curl calls to supress errors (for sites where curl is blocked/broken and craps the code out)
- Changed FB comment code to supress comment boxes on anything but single post pages (as it seems to upset Facebook).
- Fixed a bug concerning "Publish As" which meant it wasn't working properly.
- Restored an option to allow Non Wordbooker users to chose if a post should be published or not.
- Restored the option to supress like/share/send on Sticky Posts
- Fixed a bug with the og: description tag not being populated.
- Changed logic so og:tags are put out all the time unless you've checked the option to disable them all.
- Fixed a bug where an array was parsed for data even if it didn't exist.
- Fixed a bug where "post attribute" was missing.
- Fixed a bug where when extract length was set to more than 400 it got reset to either 10 or 256
- Added a check so that if a post has no images a blank is loaded to stop Facebook from scraping the page.
- Changed logic so that Posting Options checks if a post is to a page and if not defaults to a post. This should provide a short term work round for custom post types.
- Fixed a bug where the diagnostics reporting which target was active or not didn't show the right target.
- Fixed a bug where the new publish options were being lost when you scheduled a post or saved a draft and then exited and edited the post later.
- Fixed a bug where if you set the "Deactivate Wordbooker functionality" then it crashed out with a fatal error
- 24 hour time formats on the FB Status Widget weren't set correctly
- Added the X509 cert bundle for Curl installs without it properly installed.


= Version 2.0.1 11/10/2011 =
- Fixed a logic mistake concerning Share on Pages/Front pages
- A field  was missing from the post storage routine
- Fixed a bug where Like counts didn't work because the url was missing.
- Put an extra option into the Curl Call which might fix issues with certificate bundles.
- Fixed a bug with app-id/fb:admins which made comment moderation go wrong.


= Verson 2.0.0 30/09/2011 =
- Major new release - too many changes to document here

 
= version 1.0 :  02/01/2010 =
 - Base Release.


