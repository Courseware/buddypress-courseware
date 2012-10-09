=== BuddyPress Courseware ===
Contributors: sushkov, jeremyboggs, boonebgorges, johnjamesjacoby, chexee
Tags: buddypress, lms, courseware, education, teaching, quizzes, tests, gradebook, courses, lectures, assignments
Requires at least: WordPress 3.2, BuddyPress 1.5
Tested up to: WordPress 3.3 / BuddyPress 1.6
Stable tag: 0.9.6

A Learning Management System for BuddyPress

== Description ==

A BuddyPress [GSoC](http://www.google-melange.com/) 2010/2011 project.

Here's the features list:

* Class Dashboard
  * Progress Indicator
  * Overall status
  * Student evolution (based on received grates)
* Courses / Curriculum
* Lectures
  * Handbook/Tree style for content organization
  * Bookmarking of lectures
* Bibliography
  * Web API's integration with WorldCat/ISBNdb
  * BibTex Import
* Assignments
  * Responses
  * Quizzes/Tests form builder with automatic grading and response evaluation
  * Forum/bbPress integration
  * Gradebook
    * CSV Import
* Schedules
  * Calendar
    * Month, Week, Day view
    * Integrates with Assignments due date
    * iCal export/feed
* Customization using an external CSS
* Notifications/Emails
* Activity Streams
* Localizations
    * French by [Albert Bruc](http://www.ab-ae.fr/)
    * Italian by [Luca Camellini](http://buddypress.org/community/members/luccame/)

[wpvideo AD4hdKWn]

Plugin page header banner stolen from [SMBC#1092](http://www.smbc-comics.com/index.php?db=comics&id=1092)

== Installation ==

Please follow the [standard installation procedure for WordPress plugins](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins).

== Upgrade Notice ==

If you are updating from version 0.1.x please backup your database/files!
Courseware 0.9 is not fully backwards compatible!!!

Also after upgrade|install if you are experiencing publishing problems (content is not published),
make sure you updated your Courseware role in the Profile tab.

== Frequently Asked Questions ==

Before asking questions, please check the [BuddyPress Courseware Handbook](http://buddypress.coursewa.re/handbook.html).

== Changelog ==

= 0.9.7 =
* Fixed form builder select option uniqness.
* Fixed teachers dashboard count.
* Fixed typo not adding teacher role response capabilities.
* Updated jquery.fullcalendar.
* Fixed typo in jquery-ui css dependencies/version.
* Removed obsolete js dependencies. Now provided by WordPress core.
* Fixed jquery selector typo.
* Fix submenu, after removal of BuddyPress page.
* Small fixes after merge. Updated links.
* Fix use of deprecated function "add_contextual_help"
* Fixed a deprecation warning.
* Fix typos in readme.txt
* Finish renaming of the project. Remove scholarpress links, names.
* Fixed plugin directory banner.
* Updated translation for pt_BR (credits to Dianakc).

= 0.9.6 =
* Added pt_BR translation, huge thanks to Ruan Barbosa.
* Added banner image.
* Introducing `COURSEWARE_PRIVATE_UPLOADS` constant for disabling private uploads.
* Fixed a couple of typos.
* Added German localization. Huge thanks to @chaoti
* Fix a typo with inversed check of return in new response screen. Props @mattvogt.
* Add jquery-ui datetimepicker. Fixed the regression.

= 0.9.5 =
* Removed pass-by-reference calls. Props @mercime closes [#69](https://github.com/Courseware/buddypress-courseware/issues/69).
* Fixed the js issue on schedule pages. Closes [#71](https://github.com/Courseware/buddypress-courseware/issues/71).
* Public comments are now visible in assignment pages.
* Append the private message wit the grade notification.
* Skip notifications when grade is not updated. Closes [#72](https://github.com/Courseware/buddypress-courseware/issues/72).
* Cleaned-up the schedule forms.
* Updated to latest jquery.formbuilder, minified version.
* Fixed jquery ui sortable regression.
* Celebrating new home: [coursewa.re](http://coursewa.re)


= 0.9.4 =
* WordPress 3.3 BuddyPress 1.5(six-ish) compatibility
* Fixed dashboard date/time issue
* Fixed a some warnings, code cleanups
* Switched to `wp_editor()`
* Pot file updated, translations welcome
* Group course pre-creation [#61](https://github.com/Courseware/buddypress-courseware/issues/issue/61).Props @imjjss
* Search now checks through lectures too
* X-mas release! Happy past birthday to the WordPress core guys and galz!

= 0.9.3 =
* Another maintenance release
* Fixed `::is_response()` regression. Closes [#56](https://github.com/Courseware/buddypress-courseware/issues/issue/56). Props @enkerli
* New assignment screen now alerts if no lectures are available

= 0.9.2 =
* Fixed formbuilder regression. Props @enkerli

= 0.9.1 =
* Updated query var for taxonomies
* Make sure some objects are treated right.
* cleanups for every BPSP_C=<COMPONENT>::is_<COMPONENT>
* Cleanups in post types and taxonomies
* Fixed another typo
* Fixed some typos, improved formbuilder loading
* Fixed a typo not showing member take quiz button. Closes [#55](https://github.com/Courseware/buddypress-courseware/issues/issue/55)

= 0.9 =
* Major codebase changes!!!
* Assignments reworked (including quizzes, automatic grading)
* Lectures
* Cleaner UI/UX
* Progress indicator and bookmarking
* 3.2, 1.5 compatibility including distraction free writing mode
* Lots of security improvements
* Bookmarking tool
* Progress indicator tool
* Breadcrumbs
* Proper MS support
* French localisation from [Albert Bruc](http://www.ab-ae.fr/)
* [Full changelog](https://github.com/Courseware/buddypress-courseware/issues?state=closed)

= 0.1.6 =
* Updated wording, @props mrjarbenne. Closes [#27](https://github.com/Courseware/buddypress-courseware/issues/issue/27)
* Importer should work now with php5.1. Closes [#31](https://github.com/Courseware/buddypress-courseware/issues/issue/31)
* Close image now loads on response screens. Closes [#35](https://github.com/Courseware/buddypress-courseware/issues/issue/35)
* Courseware is now compatible with MS admin menu, @props [djpaul](http://buddypress.org/community/members/djpaul). Closes [#36](https://github.com/Courseware/buddypress-courseware/issues/issue/36)
* Fixed the user search according to wp 3.1 changes. Closes [#37](https://github.com/Courseware/buddypress-courseware/issues/issue/37)

= 0.1.5 =
* Fixed the issue with aliens can post responses.
* Added options to make responses private. Closes [#18](https://github.com/Courseware/buddypress-courseware/issues/issue/18).
* Removed `due_date` field as required from assignments. Closes [#23](https://github.com/Courseware/buddypress-courseware/issues/issue/23).
* Fixed screen permissions for assignments,courses and schedule.
* Added datatables to schedule delete screen.
* Fixed the Teacher persmission for course editor screen. Closes [#26](https://github.com/Courseware/buddypress-courseware/issues/issue/26)
* Added error handling for assignments, fixed the late profile sync issue that closes [#25](https://github.com/Courseware/buddypress-courseware/issues/issue/25)
* Added datatables for schedule listing.
* Added titles for schedules. Closes [#16](https://github.com/Courseware/buddypress-courseware/issues/issue/16)
* Fixed assignment `due_date` typo.
* Updated jquery.timepicker to 0.7
* Updated jquery.fullcalendar to 1.4.8
* The "bábú" release.

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

== Screenshots ==

Please visit the [project page](http://buddypress.coursewa.re) for media files.
