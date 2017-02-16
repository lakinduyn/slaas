=== Holiday Calendar ===
Contributors: mva7
Tags: calendar, calendar localization, calendar widget, calendario, calendars, Calender, event, event calendar, event list, event manager, event page, event widget, events, events calendar, free calendar, holiday, holidays, holiday calendar, upcoming holidays, Kalender, online calendar, Organizer, upcoming events, upcoming events widget
Requires at least: 3.3
Tested up to: 4.6.1
Stable tag: 1.15.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

*NEW!* An awesome calendar plugin that just works! Shows the upcoming holidays and/or your own events.

== Description ==

This calendar comes as a widget that shows your upcoming events. If you want you can automatically import the holidays from http://www.theholidaycalendar.com.

Two views are available: calendar view and list view.

If you choose to import the holidays they are fetched from a webservice. This happens asynchronous so your website will not get slow.

For more information about the webservice visit: http://www.mva7.nl/the-holiday-calendar-webservice-manual.html

== Installation ==
**Widget**

1. Upload `the-holiday-calendar` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Appearance -> Widgets and add our widget to your website
4. Add your own events through the event page in the admin screen

**Short code**

Add this to your content:

[thc-calendar title="Upcoming Events" showholidays="yes" country="us" displaymode="calendar" firstday="su" numberofholidays="3" showpoweredby="yes"]

* title = any text *(default: empty)*
* showholidays = yes / no *(default: no)*
* country = us, in, jp, br, ru, de, gb, fr, mx, kr, au, ie *(default: us)*
* displaymode = calendar / list *(default: list)*
* firstday = mo / su *(default: su)*
* numberofholidays = 3, 4, 5 *(default: 3)*
* showpoweredby = yes / no *(default: no)*

== Frequently Asked Questions ==

= Which countries are currently supported? =

For now we only provide holidays for:

* Australia
* Brazil
* France
* Germany
* India
* Ireland
* Italy
* Japan
* Mexico
* Russia
* South Korea
* United Kingdom
* United States

In the near future we plan to support other countries also.

== Screenshots ==

1. The plugin in action
2. Calendar view is also supported

== Changelog ==
= 1.15.3 =
* Fixed sorting in event manager

= 1.15.2 =
* Fix for old events that reappeared

= 1.15.1 =
* Some post release fixes

= 1.15 =
* Added times to custom events
* Fixed multi month event bug
* dd-mm-yyyy format now supported
* Removed php warning
* Fixed korean date format

= 1.14 =
* Added Italian holidays

= 1.13.2 =
* Fixed incorrect html

= 1.13.1 =
* Removed warning

= 1.13 =
* Added support for short codes.

= 1.12 =
* Added start and end date for custom events
* Information about the holiday is now displayed. (Initially only for UK and US holidays but other countries will follow)
* Translated read more link in French, German, Portuguese, Russian and Spanish.
* Improved caching (plugin is now even faster)
* Improved compatibility with Wordpress 4.3

= 1.11.3 =
* Security fix to prevent cross site scripting

= 1.11.2 =
* When the date was not present in the url, in some cases the date in the event titles were wrong.

= 1.11.1 =
* Some sites showed something like thcexcerpt555911463397c as event excerpt. Fixed now.

= 1.11 =
* Added settings screen
* Current widget properties are automatically migrated to settings
* Event date now also visible on custom event pages
* Fixed unclickable read more links

= 1.10.2 =
* Current day was also highlighted in other months. Fixed now.

= 1.10.1 =
* Month navigation fix, some months were abbreviated to two characters instead of three
* Attempt to support older PHP versions

= 1.10 =
* Month navigation in calendar mode

= 1.9.3 =
* Holidays still showed up after being disabled. Fixed now.

= 1.9.2 =
* Added Irish holidays

= 1.9.1 =
* Minor fix in translations

= 1.9 =
* Month and weekday translations in calendar mode (only for the supported countries, see FAQ)

= 1.8.1 =
* Read more link also available in calendar mode

= 1.8 =
* Number of holidays setting
* Read more links on holiday posts
* SEO friendly URL for custom events

= 1.7.1 =
* Added Australian holidays

= 1.7 =
* Added date column to event list in admin screen
* Events clickable in list mode
* Include events setting now also works in calendar mode
* Fixed strange post properties occurring in some themes

= 1.6 =
* Event pages now have the same appearance as any other page

= 1.5.1 =
* When no events, an error occured. Fixed now

= 1.5 =
* Added detail page with event list
* Days now clickable in calendar mode

= 1.4.4.1 =
* Also centered table caption

= 1.4.4 =
* Calendar content now centered by default

= 1.4.3 =
* Improved styling calendar view

= 1.4.2 =
* Current day is highlighted (not always visible though)

= 1.4.1 =
* Fixed error when quotes in holiday name

= 1.4 =
* Added calendar view
* Fixed validation error while adding event

= 1.3 =
* Add your own events
* Holidays are now optional

= 1.2 =
* Added more date formats

= 1.1 =
* Added support for 9 more countries
* Fixed width issue holiday names

= 1.0 =
* First release
