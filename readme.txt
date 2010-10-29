=== BuddyPress ScholarPress Courseware ===
Contributors: sushkov, jeremyboggs, boonebgorges
Tags: buddypress, lms, courseware, education, teaching
Requires at least: WordPress 3.0, BuddyPress 1.2.5
Tested up to: WordPress 3.0.1 / BuddyPress 1.2.5.2
Stable tag: 0.1.5

A LMS for BuddyPress.

== Description ==

A BuddyPress GSoC 2010 project.

Here's the features list:

* Class Dashboard
* Courses
  * Works for both learning models: European/United States
* Bibliography
  * Web API's integration with WorldCat/ISBNdb
  * BibTex Import
* Assignments
  * Responses
  * Forum integration
  * Gradebook
    * CSV Import
* Schedules
  * Calendar
    * Month, Week, Day view
    * Integrates with Assignment due dates
    * iCal export
* Customization using an external CSS

== Installation ==

Download and upload the plugin to your plugins folder. Activate it!

== Frequently Asked Questions ==

Before asking questions, please check the [BuddyPress Courseware Handbook](http://scholarpress.github.com/buddypress-courseware/handbook.html).

== Changelog ==

= 0.1.4 =
* Added Italian translation, thanks to [Luca Camellini](http://buddypress.org/community/members/luccame/)
* Fixed the issue with STYLESHEETPATH | stylesheet_directory 
* Updated the contextual help with plugins recommendations and handbook info, thanks to [Kyle](http://thecorkboard.org/)
* Fixed the extended search functionality to work properly on no queries
* Fixed the MS issue, where do_not_allow was fired by obsolete capability edit_file
* Final stable release for branch 0.1, dedicated to Moni

= 0.1.3 =
* Fixed some spelling errors
* Fixed critical query bug in get_objects_in_term() with term_id, props boonebgorges and ebar
* Fixed localization paths

= 0.1.2 =
* Fixed courseware dashboard and header permissions issues
* Fixed limited size listing of courses/assignments/schedules
* Fixed the issue where schedule end_date was reset by a js file
* Added pot file
* Celebrating my twenty-three :)

= 0.1.1 =
* Minor bugfixes, mostly permissions related issues in courses.
* The calendar now shows the assignment due_dates, even if no schedules exist.

= 0.1 =
* First stable release.
