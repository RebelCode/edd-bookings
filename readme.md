# EDD Bookings

A simple bookings extension for Easy Digital Downloads.

Minimum Requirements:

* **PHP**: 5.3+
* **WordPress**: 4.0+
* **Easy Digital Downloads**: 2.4.0+

Website: www.eddbookings.com

License: [GPLv3](https://www.gnu.org/licenses/gpl-3.0.en.html)

# Description

Adds booking options to your EDD Downloads, allowing them to be booked by your customers as services.

The extension will allow you to toggle booking options on and off for individual downloads. Downloads with bookings enabled can have their booking-related information set or edited through the Downloads new/edit page. The options include: session length, cost, duration variability for customer selected booking lengths and date availability.

The add-on will add a date and time (where applicable) picker on the front-end for downloads with bookings enabled, through which customers can select and book dates and times. A "Bookings" screen in the back-end gives the admin a list of bookings purchased by customers, as well as a calendar view for purchased bookings.

# Building the Plugin Archive

This process requires PHP 5.6 or later.

Obtain developer tools with

    composer install

Invoke the Phing `release` target with the `release=X.Y.Z` argument:

    bin/phing release -Dversion=x.y.z

The release files can be found in `build/` and the archive for uploading and installing into WordPress can be found in `releases/`. The build process will automatically include all non-dev dependencies.
