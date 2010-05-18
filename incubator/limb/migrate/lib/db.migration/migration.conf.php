<?php
$migration_conf = array(
      'dsn' => 'mysql://root@127.0.0.1/lime_tests?charset=utf8',
      'schema' => DB_MIGRATION_ROOT . '/sql/schema.sql',
      'data' => DB_MIGRATION_ROOT . '/sql/data.sql',
      'migrations' => DB_MIGRATION_ROOT . '/sql/migrations/',
      );