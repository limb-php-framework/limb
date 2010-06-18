db.migration.php - simple CLI interface for DbMigration.class.php. Provides easy tracking of database changes.

Usage:

php db.migration.php <action> <options>

First argument must be one of the following actions:

ACTIONS (options marked like <option_name>):

dump:
    Dumps database <dsn> to <schema> and <data> files.
    You can ignore schema using --ignore=scheme or data using --ignore=data.

init:
    Sets up <version> of databse <dsn> (0 for new db) to 'schema_info' table. Creates table if needed.
    Then makes dump action.
    You can ignore schema using --ignore=scheme or data using --ignore=data.

load:
    Cleans up database <dsn> and Loads <schema>, <data> instead 

diff:
    Shows sql schema difference between database <dsn> and <schema> including all not yet applied <migrations> 

migrate:
    Applies all <migrations> to database <dsn> newer than version in 'schema_info' table.
    You can get "dry run" with --test option.

create_migration:
    Makes diff between <dsn> and <schema>. If difference exists creates new sql patch in <migrations> named as version_<name>.sql


OPTIONS:

--dsn=<URI> - URI of database e.g. mysql://user:password@localhost:3306/database_name?charset=UTF8

--schema=<path> - path to sql schema file.

--data=<path> - path to sql data file.

--migrations=<dir/path> - path to directory with sql migration files.

--version=<int> - version number (system uses UNIX_TIMESTAMP as version identifier)

--name=<migration_patch_name> - name for new migration patch file.

--test - used to test migration process and correct posible errors before applying it to life database.

--ignore=(schema|data) - ignore selected type while dumping database to  <schema> and <data> files.


You can set default values for <dsn>, <schema>, <data>, <migrations> in migration.conf.php. Format is simple php array:

<?php
$migration_conf = array(
    'dsn' => '',
    'schema' => '',
    'data' => '',
    'migrations' => '',
  );

Also you can use local config migration.conf.override.php (use direct names if you don't want to redefine all options)

<?php
$migration_conf['dsn'] = '';
etc...

