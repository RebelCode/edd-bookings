# EDD Bookings

A simple bookings extension for Easy Digital Downloads.

Minimum Requirements:

* **PHP**: 5.3+
* **WordPress**: 4.0+
* **Easy Digital Downloads**: 2.4.0+

Website: www.eddbookings.com

License: [GPLv3](https://www.gnu.org/licenses/gpl-3.0.en.html)

# Description

This extension adds a booking system to WordPress, allowing customers to book and pay for appointments, meetings, consultations and other scheduled events, service or resources that need to be scheduled by date and time against a payment.

Site administrators are provided with a powerful interface that provides complete control over dates and prices for bookings. Once configured, customers are presented with an attractive and intuitive calendar interface that shows all available time slots. To book a date and time, customers simply need to choose the date on the calendar and then proceed with the purchase.

All bookings are presented within the WordPress dashboard, giving the site administrator easy access at a glance to all the incoming bookings and payments.

## Frontend Submissions Integration

EDD Bookings is fully integrated with the Frontend Submissions extension, allowing you to operate multi-vendor marketplaces!

Vendors can submit booking products with the same controls as the site administrator. From the vendor dashboard, bookings are displayed to vendors with all of the necessary appointment information. Vendors even have their own calendar view.

If you are using the Commissions extension, you may also send vendors information about a new booking using the `{booking}` email tag.

# Feature Highlights

* Simple, intuitive configuration for dates and times
* Calendar builder for configuring dates and times available to be booked
* Supports single and multiple sessions per booking
* Works with all payment gateways
* Manage bookings from the WordPress admin area
* Fully integrated with Frontend Submissions

# Building the Plugin Archive

This process requires PHP 5.6 or later.

Obtain developer tools with

    composer install

Invoke the Phing `release` target with the `release=X.Y.Z` argument:

    bin/phing release -Dversion=x.y.z

The release files can be found in `build/` and the archive for uploading and installing into WordPress can be found in `releases/`. The build process will automatically include all non-dev dependencies.
