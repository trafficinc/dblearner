# Tool to Explore the MySQL Databases

This is a small tool to get to know which tables in MySQL are affected by actions via DB inserts and deletes.

Update  `dbconfig.example.php` with database credentials, then change filename to `dbconfig.php`, and update the `config.php` file (make sure the directory exists for exports).

** First run `php learndb.php get-tables` to update the tables for this tool to use.

1) Before you make a change in MySQL run: $ `php learndb.php before`

2) Do something on the app UI, like fill out and submit a form, ie. create a new project.

3) After you make a change in MySQL run: $ `php learndb.php after`

4) To see what was changed in the DB tables, run: $ `php learndb.php compare`

### Export Data to Files

 `php learndb.php export="new_project_output.txt"`

 This command will run a "compare", and export to the file name specified and to the file path specified in the `config.php` file.

 Or save output to a file: `php learndb.php compare > new_project_output.txt`

Now you will be able to see what database tables were effected! **Note: this works on inserts and deletes, and updates, it expects primary keys in tables, if not present it will take the first column in the table for update(s) output.  Updates show just the row that was updated, not the specific column in the row, but the row can be found by the "PK Row ID" in "Updates Found:". Sometimes the PK Row ID is "0", that means there is no autoincremented value in the first column.

## Example output:

Differences found:

&nbsp;&nbsp;Difference at table 'mysql_crons_history' : 9908 vs 9921
  
&nbsp;&nbsp;Difference at table 'mysql_events_arms' : 16 vs 17
  
Deletes found:

&nbsp;&nbsp;table 'mysql_crons_sample' [DELETE] : OLD: 9609 vs. NEW: 9501

Inserts found:

&nbsp;&nbsp;table 'mysql_crons_history' [INSERT] : OLD: 9908 vs. NEW: 9921
  
&nbsp;&nbsp;table 'mysql_events_arms' [INSERT] : OLD: 16 vs. NEW: 17

Updates found: ...

&nbsp;&nbsp;table 'mysql_config_z'  [UPDATE] PK Column Name:  'id'  PK Row ID: 316

