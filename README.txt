=== Wired Impact Volunteer Management ===
Contributors: wiredimpact
Tags: nonprofits, non profits, not-for-profit, volunteers, volunteer
Requires at least: 4.0
Tested up to: 6.4
Requires PHP: 5.2.4
Stable tag: 2.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A free, easy way to manage your nonprofit's volunteers.

== Description ==

A simple, free way to keep track of your nonprofit's volunteers and opportunities.

**How can the Wired Impact Volunteer Management plugin help your nonprofit?**

* **Post one-time and flexible volunteer opportunities on your website –** Promote volunteer opportunities on any page of your website using a simple block.
* **Volunteers can sign up directly on your website –** Make volunteering even easier for your supporters by giving them the option to sign up for an opportunity directly on your website. A confirmation email will be sent to you and the volunteer once they sign up. 
* **Control the number of signups available for opportunities –** Only need 10 people to help out at an event? Set a cap on the number of people who can sign up. Want as many volunteers as you can get? No problem. You don’t have to set a limit.
* **Send reminder emails anytime –** Schedule an automated reminder email a few days in advance or send a one-off email with last-minute details.
* **Volunteer profiles that track participation and more –** Keep track of all of your volunteers’ involvement. See what they’ve helped out with in the past, future opportunities they're signed up for, how long they’ve been volunteering and notes that will help you stay organized.

*Thanks to [Habitat for Humanity East Bay/Silicon Valley](http://www.habitatebsv.org/) for being an awesome organization and letting us use the photo above.*

== Installation ==

**How do I install the plugin?**

This is the easiest way to install the Wired Impact Volunteer Management plugin:

1.	In the WordPress backend, go to Plugins >> Add New Plugin
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

Once you’ve downloaded the plugin, you’ll want to adjust the settings to fit your specific needs.

You can do this by visiting the Volunteer Management menu and clicking “Help & Settings”. Here, you can do things like:

* Choose to include our styles
* Set a default contact and location for volunteer opportunities
* Pick your default form settings
* Write a template message for your confirmation and reminder emails

= How do I create a new volunteer opportunity? =

1.	In the Volunteer Management menu, click “Opportunities” 
1.	On the Opportunities page, click “Add Volunteer Opportunity”
1.	Fill in all of the “Volunteer Opportunity Details” fields
1.	Click the blue “Publish” button

= How do I display a list of volunteer opportunities on my website? =

You can display the opportunities by adding the Volunteer Opportunities block to your page content. Once added, use the block’s toolbar to show one-time or flexible opportunities.

It’s not currently possible to display both types of opportunities in a single list. We recommend showing only one type of opportunity per page.

= What is the difference between the two types of opportunities? = 

One-time volunteer opportunities happen at a fixed date and time. One example might be a trivia night fundraiser where you need volunteers to help check in attendees.

Flexible volunteer opportunities can happen on different days and times. One example might be weekly tutoring. Another example could be looking for a volunteer to help with your social media or blog.

= How do I add a volunteer to our database? =

Volunteers appear in the database after signing up for an opportunity. You can’t manually add a volunteer to the database.

= How do I add notes about a volunteer? =

1.	In the Volunteer Management menu, click “Volunteers” 
1.	Click the name of the volunteer you’d like to add a note about 
1.	Click “Edit Volunteer Info”
1.	Scroll to the bottom of the page to the Notes section 
1.	Add or edit any notes
1.	Click “Update User” to save any changes

= How do I remove a volunteer from a specific opportunity? =

If a volunteer is no longer able to help out with a specific opportunity, you can remove them from the signup list. 

1.	Click “Opportunities” from the Volunteer Management menu
1.	Find the opportunity you need to remove the volunteer from 
1.	Click the opportunity to edit 
1.	Scroll down to the RSVP list, find the volunteer’s name and click “Remove RSVP”
1.	Confirm their removal by clicking the blue “Remove RSVP” button

You can also remove the RSVP through that individual volunteer’s profile.

= How do I create a recurring opportunity? =

If you have a recurring opportunity where different people will sign up each time, we recommend creating a one-time opportunity for each date and time. That will allow you to track the RSVPs separately.

If the recurring opportunity will have the same volunteers each time, we recommend creating one flexible volunteer opportunity.

= How can I adjust the built-in volunteer signup form? =

There are three options for handling volunteer signups within the plugin:

1. Use the built-in volunteer signup form which includes name, phone number and email address fields. This form can be modified, but only through code using the PHP template files included inside the plugin.
1. Choose the option not to show a form, then manually embed a third-party form within the content of each volunteer opportunity.
1. If you're utilizing the [Gravity Forms](https://www.gravityforms.com/) plugin, you can show a form you've built within your volunteer opportunities. Information can be passed into the volunteer management system using a form feed.

Within the Help & Settings page, you can set a default form to use when new opportunities are created. You can also modify which form is displayed when editing an individual volunteer opportunity.

== Screenshots ==

1. Advertise Volunteer Opportunities More Easily
1. Make Signing Up to Volunteer Simple
1. Control the Number of Volunteers Needed
1. Send Customized Reminder Emails
1. View Volunteer Profiles
1. Easily Preview the Opportunities List in the Admin Using the Volunteer Opportunities Block

== Changelog ==

= 2.4 =
* Updated microcopy throughout the plugin to improve clarity.

= 2.3 =
* Added custom Gravity Forms notification merge tags to allow volunteer opportunity details to be included dynamically.
* For clarity, hid the RSVP and volunteer email admin meta boxes if no volunteer RSVPs will be stored for an opportunity.

= 2.2 =
* Introduced settings to enable or disable certain email notifications to volunteers, admins and volunteer opportunity contacts.

= 2.1 =
* Added the ability to pass volunteer data submitted in Gravity Forms to the volunteer management system.
* Hides the chosen Gravity Forms form if no more volunteer spots are available.
* Removed bug fix for the 'Visual' tab of the editor used to email volunteers now that the issue is fixed in WordPress Core.
* Stopped loading jQuery UI styles across the entire admin since it was conflicting with other plugins.

= 2.0 =
* Included the ability for admins to remove the built-in signup form from volunteer opportunities.
* Integrated with the Gravity Forms plugin to allow admins to replace the built-in signup form with a Gravity Forms form.
* Added Cypress end-to-end test coverage for some of the plugin's functionality.
* Tested up to WordPress 6.4.

= 1.5 =
* Upgraded Google Analytics tracking to work with Google Analytics 4.

= 1.4.9 =
* Tested up to WordPress 6.1.

= 1.4.8 =
* Fixed strange formatting in the Volunteer Opportunities block toolbar.
* Upgraded build tools to use Webpack 5.
* Tested up to WordPress 6.0.

= 1.4.7 =
* Fixed bug where users without the capability to edit other users were seeing instructions to edit volunteer notes.

= 1.4.6 =
* Adjusted the Volunteer Opportunities block registration so it can't be added to Volunteer Opportunities, or as a widget on the Widgets admin page or in the customizer.
* Fixed PHP notices caused by undefined variables when a new instance of the Volunteer Management Opportunities widget was created.
* Tested up to WordPress 5.8.

= 1.4.5 =
* Updated the Toolbar component of the Volunteer Opportunities block to adhere to the WordPress development guidelines for the component, and to fix a deprecation in which a Toolbar component requires a label prop.
* Added a fix for using the 'Visual' tab of the editor used to email volunteers.
* Tested up to WordPress 5.7.

= 1.4.4 =
* Tested up to WordPress 5.6.

= 1.4.3 =
* Fixed issue when sorting volunteers by phone number.
* Updated NPM packages for improved security.
* Updated ServerSideRender block component to load directly from 'wp' global.
* Tested up to WordPress 5.5.

= 1.4.2 =
* Updated the transforms property of the Volunteer Opportunities block.
* Fixed support of the Additional CSS Class for the Volunteer Opportunities block.
* Added anchor support for the Volunteer Opportunities block.
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
* Made the Volunteer Opportunity custom post type available via the REST API so it utilizes the new Gutenberg content editor.
* Tested up to WordPress 5.0.

= 1.3.11 =
* Improved accessibility by removing all tabindex HTML attributes from the frontend form and the WordPress admin.

= 1.3.10 =
* Added ability to overwrite the default WordPress page navigation using a WordPress filter.
* Added a class to the signup form heading so it's easier to style.

= 1.3.9 =
* Fixed rare object caching cron issue which caused the automated reminder email to be sent multiple times to volunteers.

= 1.3.8 =
* Fixed potential plugin conflict where read more text for volunteer opportunities could appear in the wrong place.

= 1.3.7 =
* Fixed bug so the volunteer phone numbers and notes are no longer shared between subsites on a multisite setup.
* Further prevented the volunteer signup form from showing multiple times on a page if another plugin uses the_content() code in other places.

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
* Replaced deprecated update_usermeta function with update_user_meta.

= 0.4.2 =
* Updated plugin to allow for translation.

= 0.4.1 =
* Added ability to filter different opportunity types in the WordPress admin.
* Added ability to sort the opportunities by date in the WordPress admin.
* Tested up to WordPress 4.4.

= 0.3.1 =
* Made individual volunteer view responsive so it shows correctly on all device widths.
* Adjusted system used to add new settings in the future.

= 0.2.1 =
* Fixed bug where admin notice would show again after the settings were changed.

= 0.2 =
* Added admin notice when plugin is activated directing people to tips on how to get started.
* Adjusted how templates are loaded for a single volunteer opportunity to improve theme compatibility.

= 0.1 =
* Initial release.