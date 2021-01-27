# Table-Builder
A chainable table schema constructor with (WPDB) DB Delta builder built in.


![alt text](https://img.shields.io/badge/Current_Version-0.2.1-yellow.svg?style=flat " ") 
[![Open Source Love](https://badges.frapsoft.com/os/mit/mit.svg?v=102)](https://github.com/ellerbrock/open-source-badge/)

![alt text](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg?style=flat " ") 
![alt text](https://img.shields.io/badge/PHPUnit-PASSING-brightgreen.svg?style=flat " ") 
![alt text](https://img.shields.io/badge/PHCBF-WP_Extra-brightgreen.svg?style=flat " ") 


For more details please visit our docs.
https://app.gitbook.com/@glynn-quelch/s/pinkcrab/


## Version ##
**Release 0.2.1**

We have made no changes to how this works from V0.1.*, but we have now moved to using composer purely for this package. You can still use it without composer, but the classes and interfaces would need to be added manually.

## Why? ##
For those of you who have used DB_Delta to create tables in WordPress, to say its a bit fussy, is an understatement. 

The PinkCrab Table_Builder module, makes creating you tables much easier as you have more expressive chainable API to define the schema, which can be passed to builder to create the table. 

Out of the box, this package comes with the DB_Delta builder only, but thanks to the SQL_Builder interface, other table formats can be created easily.

## Example ##

Creates a simple table with 3 columns (id, name and date). 

```php
<?php
use PinkCrab\Modules\Table_Builder\Table_Schema;
use PinkCrab\Modules\Table_Builder\Builders\DB_Delta;

// Define the table (Indvidual method calls)
$table = Table_Schema::create( 'simple_table' );

// Columns
$table->column( 'id' )->int(10)->auto_increment()->unsigned();
$table->column( 'name' )->text()->default( 'no_name' );	
$table->column( 'date' )->datetime( 'CURRENT_TIMESTAMP' );

// Set a primary key.
$table->primary( 'id' );	

			
// Construct builder.
global $wpdb;
$builder = new DB_Delta($wpdb); 

// $builder = App::make(DB_Delta::class); 
// Can be used if using the PinkCrab Framework

// Build table.
$table->create_table($builder);
```

## Testing ##

### PHP Unit ###
If you would like to run the tests for this package, please ensure you add your database details into the test/wp-config.php file before running phpunit.
````bash
$ phpunit
````
````bash 
$ composer test
````

### PHP Stan ###
The module comes with a pollyfill for all WP Functions, allowing for the testing of all core files. The current config omits the Dice file as this is not ours. To run the suite call.
````bash 
$ vendor/bin/phpstan analyse src/ -l8 
````
````bash 
$ composer analyse
````


## License ##

### MIT License ###
http://www.opensource.org/licenses/mit-license.html  

## Change Log ##
0.2.0 - Moved to composer, renamed all namespaces to match the composer format.
