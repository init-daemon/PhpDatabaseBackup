# PhpDatabaseBackupà
* php class allowing to backup database (mysql)
```php

$dbbackup = PhpDatabaseBackup([
	        'dbhost' => DB_HOST,
	        'dbuser' => DB_USER,
	        'dbpass' => DB_PASS,
	        'dbname' => DB_NAME,
      	]);
$dbbackup->mysql();
```
