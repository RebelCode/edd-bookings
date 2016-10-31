# EDD Bookings

A simple bookings extension for Easy Digital Downloads.

*License*: GPLv3

# Building the Plugin Archive

This process requires PHP 5.6 or later.

Obtain developer tools with

    composer install

Invoke the Phing `release` target with the `release=X.Y.Z` argument:

    bin/phing release -Dversion=x.y.z

The release files can be found in `build/` and the archive for uploading and installing into WordPress can be found in `releases/`.
