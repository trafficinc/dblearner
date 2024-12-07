# Simple Tool to Learn How a MySQL Database Interacts with the Application

This is a small tool to get to know which tables in MySQL are affected by actions in the application via database inserts, updates, and deletes. (Should work with MariaDB too, just haven't tested it.)

Update  `dbconfig.example.php` with database credentials, then change filename to `dbconfig.php`, and update the `config.php` file (make sure the directory exists for exports). Do the same with `config.example.php` => `config.php`.

** First run `php learndb.php get-tables` to update the tables for this tool to use.

1) Before you make a change that affects MySQL, run: $ `php learndb.php before`

2) Do something on the app UI, like fill out and submit a form or create, edit, delete a "whatever", ie. user, account, project, form, etc.

3) After that, run: $ `php learndb.php after`

4) To see what was changed in the database tables, run: $ `php learndb.php compare`

** You can also do an `after` and `compare` in one statement, `php learndb.php complete`

### Export Data to Files

 `php learndb.php export="new_project_output.txt"`

 This command will run a "compare", and export to the file name specified and to the file path specified in the `config.php` file.

 Or save output to a file: `php learndb.php compare > new_project_output.txt`

Now you will be able to see what database tables were effected! 

> Note: this works on inserts and deletes, and updates, it expects primary keys in tables, if not present it will take the first column in the table for update(s) output.  

> Updates show just the row that was updated, not the specific column in the row, but the row can be found by the "PK Row ID" in "Updates Found:". Sometimes the PK Row ID is "0", that means there is no autoincremented value in the first column.

## Example output:
```
Differences found:

    Difference at table 'mysql_crons_history' : 9908 vs 9921
  
    Difference at table 'mysql_events_opp' : 16 vs 17
  
Deletes found:

    Table 'mysql_crons_sample' [DELETE] : OLD: 9609 vs. NEW: 9501

Inserts found:

    Table 'mysql_crons_history' [INSERT] : OLD: 9908 vs. NEW: 9921
  
    Table 'mysql_events_yxy' [INSERT] : OLD: 16 vs. NEW: 17

Updates found: ...

    Table 'mysql_config_zzc'  [UPDATE] PK Column Name:  'id'  PK Row ID: 316
```
Todo: 
[+] Feature to skip tables in scan. Maybe good for performance and un-needed tables. Maybe include/exclude in config?