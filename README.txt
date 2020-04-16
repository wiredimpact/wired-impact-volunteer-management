=== Wired Impact Volunteer Management ===
Contributors: wiredimpact
Tags: nonprofits, non profits, not-for-profit, volunteers, volunteer
Requires at least: 4.0
Tested up to: 5.4
Requires PHP: 5.2.4
Stable tag: 1.4.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A free, easy way to manage your nonprofit's volunteers.

== Description ==

A simple, free way to keep track of your nonprofit's volunteers and opportunities.

**How can the Wired Impact Volunteer Management plugin help your nonprofit?**

* **Post one-time and flexible volunteer opportunities on your website –** Promote volunteer opportunities on any page of your website using a simple block or shortcode.
* **Volunteers can sign up directly on your website –** Make volunteering even easier for your supporters by giving them the option to sign up for an opportunity directly on your website. A confirmation email will be sent to you and the volunteer once they sign up. 
* **Control the number of sign-ups available for opportunities –** Only need 10 people to help out at an event? Set a cap on the number of people who can sign up. Want as many volunteers as you can get? No problem. You don’t have to set a cap.
* **Send reminder emails anytime –** Schedule an automated reminder email three days in advance or send even hours before the opportunity for last-minute details.
* **Volunteer profiles that track participation and more –** Keep track of all of your volunteers’ involvement. See what they’ve helped out with, how long they’ve been helping and add notes that will help you stay organized.

*Thanks to [Habitat for Humanity East Bay/Silicon Valley](http://www.habitatebsv.org/) for being an awesome client and letting us use the photo above.*

== Installation ==

**How do I install the plugin?**

This is the easiest way to install the Wired Impact Volunteer Management plugin:

1.	In the WordPress backend, go to Plugins >> Add New
1.	Search for “Wired Impact Volunteer Management”
1.	Click “Install Now”
1.	Click “Activate”

If this doesn’t work, follow these steps:

1.	Download the Wired Impact Volunteer Management plugin
1.	Unzip the files
1.	Upload the wired-impact-volunteer-management folder to the /wp-content/plugins directory of your website
1.	Activate the Wired Impact Volunteer Management plugin through the “Plugins” menu in WordPress

== Frequently Asked Questions ==

= How do I get started? =

Once you’ve downloaded the plugin, you’ll want to adjust the settings to fit your specific needs. You can do this by visiting the Volunteer Management menu and clicking “Help & Settings”. Here, you can choose to include our styles, set a default contact and location for volunteer opportunities, and create a template for your confirmation and reminder emails.

= How do I create a new volunteer opportunity? =

1.	In the Volunteer Management menu, click “Opportunities” 
1.	On the Opportunities page, click “Add Volunteer Opportunity”
1.	Fill in all of the information fields
1.	Click the blue “Publish” button

= How do I display a list of volunteer opportunities on my website? =

If you’re using the classic editor (WordPress 4.9 or earlier), you can display the opportunities by including the following shortcodes in the page content: [one_time_volunteer_opps] (for one-time opportunities), [flexible_volunteer_opps] (for flexible opportunities)

If you’re using the block editor (WordPress 5.0 or later), you can display the opportunities by adding the Volunteer Opportunities block to your page content. Once added, you can use the block’s toolbar to display one-time or flexible opportunities.

It’s important to note, you can’t display a list of both types of opportunities on the same page. Please list either one-time or flexible opportunities on a page.

= What is the difference between the two types of opportunities? = 

One-time volunteer opportunities happen at a fixed date and time. One example might be a yearly trivia night fundraiser.

Flexible volunteer opportunities can happen on different days and times. One example might be weekly tutoring. Another example could be looking for a volunteer to help with your social media or blog.

= How do I add a volunteer to our database? =

You can’t manually add a volunteer to the database. Volunteers appear in the database after signing up for an opportunity.

= How do I add notes about a volunteer? =

1.	In the Volunteer Management menu, click “Volunteers” 
1.	Click the name of the volunteer you’d like to add a note about 
1.	Click “Edit Volunteer Info”
1.	Scroll to the bottom of the page to the Notes section 
1.	Add or edit any notes
1.	Click “Update User” to save any changes

= How do I remove a volunteer from a specific opportunity? =

If a volunteer is no longer able to help out at a specific opportunity, you can remove them from the sign up list. 

1.	Click “Opportunities” from the Volunteer Management menu
1.	Find the opportunity you need to remove the volunteer from 
1.	Click the opportunity to edit 
1.	Scroll down to the RSVP list, find the volunteer’s name and click “Remove RSVP”
1.	Confirm their removal by clicking the blue “Remove RSVP” button

= How do I create a recurring opportunity? =

If you have a recurring opportunity where different people will sign up each time, we recommend creating a one-time opportunity for each date and time. That will allow you to track the RSVPs separately.

If the recurring opportunity will have the same volunteers each time, we’d recommend you create one flexible volunteer opportunity.

== Screenshots ==

1. Advertise Volunteer Opportunities More Easily
1. Make Signing Up to Volunteer Simple
1. Control the Number of Volunteers Needed
1. Send Customized Reminder Emails
1. View Volunteer Profiles
1. Easily Preview the Opportunities List in the Admin Using the Volunteer Opportunities Block

== Changelog ==

= 1.4.2 =
* Updated the transforms property of the Volunteer Opportunities block.
* Tested up to WordPress 5.4.

= 1.4.1 =
* Tested up to WordPress 5.3.

= 1.4 =
* Added a block to use in WordPress 5.0+ to display volunteer opportunities.
* Added the ability to change the 'Help & Settings', 'Volunteers', and 'Volunteer' subpage names using the 'wivm_submenu_page_name' filter.
* Added the ability to hide the 'Help' tab using the 'wivm_show_help_tab' filter. 
* Modified the CSS styling used for the datepicker to minimize conflicts with other plugins and themes.
* Tested up to WordPress 5.2.

= 1.3.12 =
* Make the Volunteer Opportunity custom post type available via the REST API so it utilizes the new Gutenberg content editor.
* Tested up to WordPress 5.0.

= 1.3.11 =
* Improved accessibility by removing all tabindex HTML attributes from the frontend form and the WordPress admin.

= 1.3.10 =
* Added ability to overwrite the default WordPress page navigation using a WordPress filter.
* Added a class to the sign up form heading so it's easier to style.

= 1.3.9 =
* Fixed rare object caching cron issue which caused the automated reminder email to be sent multiple times to volunteers.

= 1.3.8 =
* Fixed potential plugin conflict where read more text for volunteer opportunities could appear in the wrong place.

= 1.3.7 =
* Fixed bug so the volunteer phone numbers and notes are no longer shared between subsites on a multisite setup.
* Further prevented the volunteer sign up form from showing multiple times on a page if another plugin uses the_content() code in other places.

= 1.3.6 =
* Tested up to WordPress 4.9.
* Fixed bug where the number of custom emails sent to volunteers for an opportunity might have displayed incorrectly.

= 1.3.5 =
* Fixed bug where translating "Volunteer Mgmt" would break the Help & Settings admin page.

= 1.3.4 =
* Fixed bug where date and timepicker conflicted with other plugins in the WordPress admin.

= 1.3.3 =
* Fixed multisite bug where volunteers weren't displaying on the Volunteers page if they had already signed up through another subsite.
* Fixed multisite bug where volunteer opportunity URLs were broken if the plugin had been network-activated.

= 1.3.2 =
* Tested up to WordPress 4.8 and adjusted admin headings for improved accessibility. 

= 1.3.1 =
* Fixed bug where sidebar widget wasn't linking correctly to pages with opportunities on WordPress multisite installations.

= 1.3 =
* Added a honeypot spam protection feature to the volunteer signup form.

= 1.2 =
* Adjusted the plugin to allow for full translation including opportunity times and phone numbers.

= 1.1.1 =
* Fixed bug where automatic email reminders were not sending to volunteers in some cases.
* Tested up to WordPress 4.7.

= 1.1 =
* Added widget to allow admins to list volunteer opportunities in the sidebar or other widgetized areas.

= 1.0.4 =
* Added additional arguments to one hook and one filter for further custom development flexibility.

= 1.0.3 =
* Fixed bug where all users were shown in the volunteer list if no RSVPs had taken place.
* Adjusted plugin permissions for clearer and easier editing for different WordPress user roles.
* Tested up to WordPress 4.5.

= 1.0.2 =
* Fixed bug where some users were not being included in the volunteers list view when they should be.

= 1.0.1 =
* Fixed issue where template override directory was changed to match text domain.

= 1.0 =
* Tested and confirmed as stable version 1.0 release.
* Set up Google Analytics event tracking when someone RSVPs for a volunteer opportunity.

= 0.5.1 =
* Improved subject and body error messages for sending custom emails.
* Separated opportunity styling and form messages from certain classes, increasing compatibility with themes.

= 0.5.0 =
* Added ability to send custom emails to volunteers registered for an opportunity.
* Added new meta box to display the list of all emails sent to volunteers.
* Bugfix: Replace deprecated update_usermeta function with update_user_meta.

= 0.4.2 =
* Updated plugin to allow for translation

= 0.4.1 =
* Added ability to filter different opportunity types in the WordPress admin.
* Added ability to sort the opportunities by date in the WordPress admin.
* Tested up to WordPress 4.4.

= 0.3.1 =
* Made individual volunteer view responsive so it shows correctly on all device widths.
* Adjusted system used to add new settings in the future.

= 0.2.1 =
* Bugfix: Fix issue where admin notice would show again after the settings were changed.

= 0.2 =
* Show admin notice when plugin is activated directing people to tips on how to get started.
* Adjust how templates are loaded for a single volunteer opportunity to improve theme compatibility.

= 0.1 =
* Initial release.