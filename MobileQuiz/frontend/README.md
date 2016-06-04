# MobileQuiz FrontEnd

This folder is only about the frontend. It does not use ILIAS in any way, 
therefore it could be even moved to a different host. The main reason it is located 
here is to make deployment easier.

For licenses please refer to top level location of this software and its documentation.


## INSTALLATION INSTRUCTIONS FOR THE FRONTEND:

The frontend is deployed automatically with the backend. You will have to adjust
the database access configuration though. Therefore please change the settings in the
following file to your needs:

frontend/includes/config.php

If you wish you can move the frontend directory somewhere else, even to a different
host. But keep in mind that it needs access to ILIAS’s database. Also change the
variable frontend url in the file

classes/class.ilObjMobileQuizGUI.php

to reflect the new location.