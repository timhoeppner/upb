**************************
UPB 2.2.1 RELEASE
**************************


Getting Started:
== INSTALLATION ==
  CHMOD the root UPB directory to 0777
  CHMOD the config.php to 0644
  Execute the install.php
  DELETE update1.x-2.0.php, update2_2_1.php, and install.php, they are a security risk


== UPGRADING FROM 2.1.1b ==
  Backup everything in your forum directory by downloading it to your harddrive
  Upload all the files found in the zip file and overwrite any changed files EXCEPT config.php
  Execute the update2_2_1.php
  It should have no errors, if there is then please report them to myupb.com
  DELETE update1.x-2.0.php, update2_2_1.php, and install.php, they are a security risk


== UPGRADES 2.0 B1 - 2.0.2b ==
  Backup everything in your forum directory by downloading it to your harddrive
  Upload all the files found in the zip file and overwrite any changed files EXCEPT config.php
  Execute the install-uploads.php
  It should have no errors, if there is then please report them to myupb.com
  DELETE update1.x-2.0.php, install.php, and install-uploads.php, they are a security risk


== UPGRADING FROM 1.x TO 2.0B1 ==
    It is hightly recommended that you backup your db folder before running the upgrade file
  CHMOD the root UPB directory to 0777
  CHMOD the config.php to 0644
  CHMOD the db folder and folders inside the db folder to 0777
  CHMOD the all files inside the db folder to 0644
  CHMOD each skin's folder, located in the skins folder (ex: /upb/skins/default/), to 777
  Execute the update1.x-2.0.php