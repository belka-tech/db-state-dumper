# DB State Dumper

This tool creates periodic dumps of the current database state and saves them as .csv files. 
By default, it is designed to run once per minute, providing a time-series history of database snapshots. 
These dumps can be used for monitoring, debugging, and future investigations of system behavior.

The state is collected using SQL queries that you can fully configure.
You can define one or multiple queries depending on your needs.
Example configurations with useful queries are provided in `config.example.pg.php` (PostgreSQL) and `config.example.mysql.php` (MySQL).

## Usage

1. Configure the config file (see config.example.pg.php for an example).

2. Add the command to crontab:
    ```bash
    ./bin/state-dumper config.php data_dir
    ```
    - `config.php` - Path to the configuration file (one file per database)
    - `data_dir` - Directory where the output will be stored (one dir per database)

3. To remove old dumps, add the following command to cron for daily execution:
   ```bash
   ./bin/remove-old-dumps.sh data_dir {days}
   ```

   - `data_dir` ﹣ Directory where the output is stored
   - `days` ﹣ Number of days after which the dump will be deleted

### Crontab example
Tool does not include its own scheduler, so you should use cron (or another task scheduler) to run it, for example once per minute.
```
* * * * * cd /opt/state-dumper/ && bin/state-dumper configs/prod-main-db-01.php /opt/state-dumper/storage/production/prod-main-db-01 # Make new state-dumps
0 23 * * * cd /opt/state-dumper/ && bin/remove-old-dumps.sh /opt/state-dumper/storage/production/prod-main-db-01 30 # Remove old state-dumps

* * * * * cd /opt/state-dumper/ && bin/state-dumper configs/prod-main-db-replica-01.php /opt/state-dumper/storage/production/prod-main-db-replica-01 # Make new state-dumps
0 23 * * * cd /opt/state-dumper/ && bin/remove-old-dumps.sh /opt/state-dumper/storage/production/prod-main-db-replica-01 30 # Remove old state-dumps

```

### Result files structure
```
root@hostname:/opt/state-dumper/storage/production/prod-main-db-01/2025-08-27$ ll | head
total 12032
drwxr-sr-x  2 www-data www-data   69632 Aug 27 23:59 ./
drwxrwsr-x 34 www-data www-data    4096 Aug 29 00:00 ../
-rw-rw-r--  1 www-data www-data    8792 Aug 27 00:00 0000_activity.csv
-rw-rw-r--  1 www-data www-data    4599 Aug 27 00:01 0001_activity.csv
-rw-rw-r--  1 www-data www-data    3820 Aug 27 00:02 0002_activity.csv
-rw-rw-r--  1 www-data www-data    2877 Aug 27 00:03 0003_activity.csv # State for 2025-08-27 00:03 (0003)
-rw-rw-r--  1 www-data www-data    2531 Aug 27 00:04 0004_activity.csv
-rw-rw-r--  1 www-data www-data    4022 Aug 27 00:05 0005_activity.csv
-rw-rw-r--  1 www-data www-data    5493 Aug 27 00:06 0006_activity.csv
```
