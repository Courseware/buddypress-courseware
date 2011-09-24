=== BuddyPress ScholarPress Courseware ===
Contributors: sushkov, jeremyboggs, boonebgorges, johnjamesjacoby, chexee
Tags: buddypress, lms, courseware, education, teaching, quizzes, tests, gradebook, courses, lectures, assignments
Requires at least: WordPress 3.2, BuddyPress 1.5
Tested up to: WordPress 3.2.1 / BuddyPress 1.5
Stable tag: 0.9
Donate link: http://stas.nerd.ro/pub/donate/

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

== Installation ==

Please follow the [standard installation procedure for WordPress plugins](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins).

== Upgrade Notice ==

If you are updating from version 0.1.x please backup your database/files!
Courseware 0.9 is not fully backwards compatible!!!

== Frequently Asked Questions ==

Before asking questions, please check the [BuddyPress Courseware Handbook](http://scholarpress.github.com/buddypress-courseware/handbook.html).

== Changelog ==

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
* [Full changelog](https://github.com/scholarpress/buddypress-courseware/issues?state=closed)

= 0.1.6 =
* Updated wording, @props mrjarbenne. Closes [#27](https://github.com/scholarpress/buddypress-courseware/issues/issue/27)
* Importer should work now with php5.1. Closes [#31](https://github.com/scholarpress/buddypress-courseware/issues/issue/31)
* Close image now loads on response screens. Closes [#35](https://github.com/scholarpress/buddypress-courseware/issues/issue/35)
* Courseware is now compatible with MS admin menu, @props [djpaul](http://buddypress.org/community/members/djpaul). Closes [#36](https://github.com/scholarpress/buddypress-courseware/issues/issue/36)
* Fixed the user search according to wp 3.1 changes. Closes [#37](https://github.com/scholarpress/buddypress-courseware/issues/issue/37)

= 0.1.5 =
* Fixed the issue with aliens can post responses.
* Added options to make responses private. Closes [#18](https://github.com/scholarpress/buddypress-courseware/issues/issue/18).
* Removed `due_date` field as required from assignments. Closes [#23](https://github.com/scholarpress/buddypress-courseware/issues/issue/23).
* Fixed screen permissions for assignments,courses and schedule.
* Added datatables to schedule delete screen.
* Fixed the Teacher persmission for course editor screen. Closes [#26](https://github.com/scholarpress/buddypress-courseware/issues/issue/26)
* Added error handling for assignments, fixed the late profile sync issue that closes [#25](https://github.com/scholarpress/buddypress-courseware/issues/issue/25)
* Added datatables for schedule listing.
* Added titles for schedules. Closes [#16](https://github.com/scholarpress/buddypress-courseware/issues/issue/16)
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

Please visit the [project page](http://scholarpress.github.com/buddypress-courseware/) for media files.
