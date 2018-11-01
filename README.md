# php-report-engine
A framework for building reports in PHP.
Version 0.9 - meaning that it works in a production environment and is used in them, but installation difficulty will vary depending on your system.

To Use:
Open the directory reportengine/reports and add a new file similar to the samples provided. 
This file will include a function that builds the dataset for your report or chart. It will also include another function for displaying it.
The Report Engine will look for these functions and provide a view area for them with a refresh button and progress bar. 
The reports are generated separately from the page you place thme on so a user may click to refresh and then close the browser and return later. 
Upon thier return, the progress bar will pick up wherever it is in the process. If the report is done it will be displayed.

To Install:
1. Copy reportengine directory into your project. 
2. Include Report.php and then call ReportEngine\Report::get(); and at least add the name of the report as a parameter (You may add a specific id as a parameter as well if you are, for example, reporting on users and you wnat a different report for every user: then put the user's id into the speicific id parameter).
3. Open ReportEngine\ReportConfig and change the WEBROOT constant to a url that leads to the reportengine directory. This will be used to build urls for listeners.
