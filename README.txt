---------------------------------------------------------------------------------------------------
Bugfix Release - July 19th 2012

Boosterworks v0.63 Known Issues:
No known issues

Current version bugfixes:
	Fixed an error that could cause loss of the "shift for" field when moving a waitlisted user into the confirmed roster for an event

Current version new features:

---------------------------------------------------------------------------------------------------
Feature Release - July 15th 2012

Boosterworks v0.62 Known Issues:
	No known issues

Current version bugfixes:

Current version new features:
	Added option for an admin to make an account inactive. Inactive accounts will not recieve broadcast emails, except for events they have already signed up for.

---------------------------------------------------------------------------------------------------
Bigfix Release - August 27th 2011

Boosterworks v0.61 Known Issues: 
	No known issues

Current version bugfixes:
	Fixed 'Page not found' error when sending email to all registered users
	Cleaned up email processing procedures to eliminate errors when a user did not specify a secondary email address

---------------------------------------------------------------------------------------------------
Feature Release - July 24 2011

Boosterworks v0.6 Known Issues: 
	Moving from waitlist to confirmed shift of events with multiple time slots causes unpredictable behaviour 

Current version bugfixes:
	Consolidated database queries used to generate calendar, average query reduction of 70% per page load
	Fixed calendar display for multiple events on the same date
	Fixed typos in transportation dropdown
	Fixed error when saving user profile after changing family group

Current version new features:
	Admins can now move users into shift slots from the waitlist, and vice versa
	The times a user has waitlisted for are now shown in the admin control panel
	Added number of open slots to listing of confirmed volunteers in admin event details
	Redesigned contact page

Other Notes: 
	Removed test-only Drupal related functionality
	Migrated to specific database environments for development and production
	Profiles table: Altered column 'id' to 'profile_id'
	Suppressing output of errors related to $transportation in admin events controller is hackish, not ideal  
	

---------------------------------------------------------------------------------------------------
Feature Release - June 18 2011

Boosterworks v0.5 Known Issues: 
	Shift display ordering is unpredictable, need to sort via Javascript not MySQL
	Calendar produces massive number of queries - more filtering needed at database level	

Current version bugfixes:
	Fixed outdated page title in template

Current version new features:
	Require users to specify how they are getting to an event when signing up for a shift
	Now allow administrators to add/remove admin rights to other users within the system
	Added links for administrators to edit users

---------------------------------------------------------------------------------------------------
Bugfix Release - June 10 2011

Boosterworks v0.4 Known Issues: 
	Shift display ordering is unpredictable, need to sort via Javascript not MySQL
	Calendar produces massive number of queries - more filtering needed at database level	

Current version bugfixes:
	Corrected link to shift list for full events or events with no slots created, so that users can sign up for waitlist spots
	Changed email address that mail is dispatched from to boosterworks@tarponspringsband.com
	Removed link to depricated event reporting menu

Current version new features:
	Administrators can edit homepage/help and info pages via administrator control panel

		

---------------------------------------------------------------------------------------------------
Bugfix Release - June 2 2011

Boosterworks v0.3 Known Issues: 
	Shift display ordering is unpredictable, need to sort via Javascript not MySQL
	Calendar produces massive number of queries - more filtering needed at database level

Current version bugfixes:
	Fixed bug in shift creation where DB structure was revised but query was not
	Fixed "silent" error on registration when provided email matched another user's, but no error message was displayed
	Fixed bug where, if an error was found when registering, the form would not remember the previously provided value for the "is student" field


Current version new features:
	Admin waitlist viewing by event
	Add shifts/slots to event after creation
	Delete entire events
	Remove user (un-signup) from shift
	Moved homepage text and 'Help and Info' text into database, to enable future editing by administrators 
		
