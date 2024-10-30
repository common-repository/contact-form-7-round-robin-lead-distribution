=== Contact Form 7 Round Robin Lead Distribution ===
Tags: multiple email, round robin, lead distribution, sales team, enquiry distribution, enquiry sharing, lead sharing
Requires at least: 5.0
Tested up to: 5.5.3
Stable tag: 1.2.1
Contributors: justdave007
Author URI: https://icreate.agency
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=MHPNTMG42TGQG

CF7 Lead Distribution - Share web leads fairly (sequentially) with a group of recipients.

== Description ==

This plugin extends Contact Form 7. It allows web form enquiries to be distributed sequentially amongst a group of recipients. Sometimes called *Round Robin Distribution* this is commonly used to share web leads fairly amongst a sales team.

For example if there are 3 in the sales team: Bob, Bill and Ben then:

* The first web enquiry is sent to `Bob`
* The second web enquiry goes to `Bill` 
* The third to `Ben`
* And the fourth back around to `Bob` again

Everyone gets a fair share.  Everyone's happy, including you for finding this handy little plugin. ;-)


== Installation ==

= Option 1 - Install Straight from WordPress (easiest) =

1. In WordPress Admin, Select `Add New` From the `Plugins` Menu
2. Search for "Round Robin" - Find `Contact Form 7 Round Robin Lead Distribution` in the list
3. Install and Activate the Plugin

= Option 2 - Manually Upload  =

1. Download the latest version
2. Unzip the download
3. Upload the entire `contact-form-7-round-robin-lead-distribution` folder to the `/wp-content/plugins/` directory. 
4. Activate the plugin through the `Plugins` menu in WordPress.


= Using Round Robin once installed = 

1. Create a CF7 form (Install CF7 if you haven't already done so)
2. Under the Contact Menu item, use the `RR User Manager` to setup your recipients
3. Under the Contact Menu item, use the `RR Forms Manager` to assign recipients to the desired CF7 form. 
4. That's it, web leads will be shared evenly between the recipients. Actual sends are kept in a log.

*Check out the screenshots section for a visual guide*  

== Frequently Asked Questions == 

= Is there a limit to how many recipients I can set? =

No.

= Does it record a log of who email was distributed to? =

Yes.  You can access this under the Contact Menu. "RR Mail Tracker"


= I have an idea for a feature =

Yes sure, I'm interested to hear it. Please email to support@icreatesolutions.com.au


== Screenshots ==

1. Before you begin. Install plugin 'Contact Form 7' and setup your forms.
2. Step 1. Add your users. (email recipients)
3. Step 2. Assign the users to the selected form.
4. Round Robin should now be working.  Distributed leads are logged.
5. Round Robin, works by overwriting CF7's "Mail To:" field each time a notification is sent.

== Changelog ==

= 1.2.1 =
* Updated latest stable version in readme

= 1.2 =
* Fixed for CF7 version 5.3

= 1.1 =
* Added feature for adding holiday dates to users

= 1.0.9 =
* Updated email validation, was previously rejecting valid addresses

= 1.0.8 = 
* A upgrade to CF7 broke this plugin. (Sorry if you experienced this)
* This has been fixed in this version.  Current version only works with CF7 3.9 and greater.

= 1.0.7 =
* Fixed checkbox display bug in Chrome

= 1.0.6 =
* Interface improvements
* Screenshots added
* Nifty logo added

= 1.0.5 =
* Misc Styling fixes

= 1.0.4 =
* Misc Styling fixes

= 1.0.3 =
* Bug fix

= 1.0.2 =
* Interface improvements
* Added Icon
* Menu rearrange

= 1.0.1 =
* Interface improvements

= 1.0.0 =
* Initial release.