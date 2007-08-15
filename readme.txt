*************************
UPB 2.0 BETA 1 RELEASE
*************************

Getting Started:
== INSTALLATION ==
  CHMOD the root UPB directory and the config.php to 777
  Execute the install.php
  WAIT UNTIL INSTALLATION IS FINISHED
  CHMOD the root UPB directory and the config.php back to 644
  DELETE update1.x-2.0.php and install.php, they are a security risk

== UPGRADES ==
    It is hightly recommended that you backup your db folder before running the upgrade file
  CHMOD the config.php in root UPB directory to 777
  CHMOD the db folder (and all files and folders inside, including files inside folders) in the root UPB directory to 777
  CHMOD each skin's folder, located in the skins folder (ex: /upb/skins/default/), to 777
  Execute the update1.x-2.0.php
  WAIT UNTIL THE UPGRADE IS FINISHED
  CHMOD the config.php in root UPB directory back to 644
  CHMOD each skin's folder, located in the skins folder (ex: /upb/skins/default/), to 644


For your information:
== WHAT'S DIFFERENT FROM 1.X ==
  The Code is completely rewritten.
  Admin Private Messaging has been deleted.
  New permissions for forums and categories.
  New method of finding users to send PMs to.**
  New Configuration storage method.
  New topics and posts storage method.*
  New PMs storage method.*
  New version of tdb.class.php.**
  More classes to centralize and group specific tasks and functions.
  A "Monitor Topic" has been added.
  Randomized the db folder name to add security

  * Note: new method uses a significantly less amount of files than 1.x, but 2.x files have a significantly bigger size.
  ** Note: Reduces processing time.

== BUGS AND ERRORS OF THE BETA 1 RELEASE == 
  Moving topics - some functionality, not fully functional
  Search - searching in posts not working; disabled until further notice
  Admin_restore - new to UPB, Works on some systems not on others, testing still in progress


For developers:
== MODIFICIATIONS TO THE "CONFIG SETTINGS" IN THE ADMIN PANEL ==
  To add a variable to the forum, you must add one to the config and config_org table in the database.tdb.  Please refer to config.class.php for notes and details
  To add a new scope of variables (i.e. $_CONFIG, $_REGIST, and $_STATUS), you must modify the config_org.dat, located in the data folder (see config_org.dat & config.class.php for notes, details and examples), and you must repeatedly add variables.  Note: each scope has to be 6 characters