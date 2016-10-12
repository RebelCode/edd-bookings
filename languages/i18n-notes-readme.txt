<?php

/**
 * To translate config files, such as config/settings.xml, configure PoEdit like so:
 *
 * 1. Go to `File > Preferences > Extractors`
 * 2. Click `New`
 * 3. Enter the following data:
 * 		Language:
 * 			XML
 * 		List of extensions separated by semicolons (e.g. *.cpp;*.h):
 * 			*.xml;*.XML
 * 		Command to extract translations:
 * 			‪xgettext -o %o %C %F
 * 	  	An item in keywords list:
 * 	  		(leave empty)
 * 	  	An item in input files list:
 * 	  		%f
 * 	  	Source code charset:
 * 	  		--from-code=%c
 * 	4. Copy the two files in the `/languages/xml` folder and paste them in your PoEdit's installation where it saves these files.
 * 	   These are typically in its `GettextTools/share/gettext/its` path.
 */
