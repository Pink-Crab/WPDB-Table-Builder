# Table-Builder
A chainable table schema constructor with (WPDB) DB Delta builder built in.


![alt text](https://img.shields.io/badge/Current_Version-0.3.0-yellow.svg?style=flat " ") 
[![Open Source Love](https://badges.frapsoft.com/os/mit/mit.svg?v=102)](https://github.com/ellerbrock/open-source-badge/)
![](https://github.com/Pink-Crab/Module__Table_Builder/workflows/GitHub_CI/badge.svg " ")
[![codecov](https://codecov.io/gh/Pink-Crab/Module__Table_Builder/branch/master/graph/badge.svg?token=UBWL8S4O8L)](https://codecov.io/gh/Pink-Crab/Module__Table_Builder)


For more details please visit our docs.
https://app.gitbook.com/@glynn-quelch/s/pinkcrab/


## Version ##
**Release 0.3.0**

**PLEASE NOTE 0.3.0 IS NOT FULLY COMPATIBLE WITH 0.2.0**

We have made no changes to how this works from V0.1.*, but we have now moved to using composer purely for this package. You can still use it without composer, but the classes and interfaces would need to be added manually.

## Why? ##
For those of you who have used DB_Delta to create tables in WordPress, to say its a bit fussy, is an understatement. 

The PinkCrab Table_Builder module, makes creating you tables much easier as you have more expressive chainable API to define the schema, which can be passed to builder to create the table. 

Out of the box, this package comes with the DB_Delta builder only, but thanks to the SQL_Builder interface, other table formats can be created easily.



## Defining a Tables Schema

You can define a tables schema in a few different ways.

```php
<?php

$schema_a = new Schema('my_table', function(Schema $schema){
    // Set columns
    $schema->column('id')->unsigned_int(11)->auto_increment();
    $schema->column('user')->int(11);
    
    // Set keys and indexes.
    $schema->index('id')->primary();
    $schema->index('user')->unique();
});

$schema_b = new Schema('my_table');
// Set columns
$schema_b->column('id')->unsigned_int(11)->auto_increment();
$schema_b->column('user')->int(11);

// Set keys and indexes.
$schema_b->index('id')->primary();
$schema_b->index('user')->unique();
```
> Please note unless like previous versions, the column and index data can not be defined fluently.

## Indexes and Foreign Keys

You can setup a variety of Indexes and Foreign_Key's for your table(s). These can be set as the schema example above.

### Index

You can create index for any column of your table and denote the field as either just an index, unique, primary, full text or as a hash.

```php
<?php

$schema_a = new Schema('my_table', function(Schema $schema){
    // Set columns
    $schema->column('id')->unsigned_int(11)->auto_increment();
    $schema->column('user')->int(11);
    $schema->column('details')->text();
    
    // Set keys and indexes.
    $schema->index('id')->primary();
    $schema->index('user')->unique();
    $schema->index('details')->full_text();
});
```
The above would generate

```sql
CREATE TABLE my_table(
    id INT AUTO_INCREMENT
    user INT(11),
    details TEXT,
    PIRMARY KEY ix_id (id),
    UNIQUE INDEX ix_user (user),
    FULLTEXT INDEX ix_details (details)
);
```

If you wish to use more than 1 column for an index, please add them as single indexes and the builder will combine. You must also set a custom keyname for all grouped indexes.

```php 
$schema = new Schema('my_table', function(Schema $schema){
    // Set columns
    $schema->column('id')->unsigned_int(11)->auto_increment();
    $schema->column('user')->int(11);
    $schema->column('details')->text();
    
    // Set keys and indexes.
    $schema->index('id')->primary();

    $schema->index('user', 'unique_keys')->unique();
    $schema->index('details', 'unique_keys')->unique();
});
```
The above would generate

```sql
CREATE TABLE my_table(
    id INT AUTO_INCREMENT
    user INT(11),
    details TEXT,
    PIRMARY KEY ix_id (id),
    UNIQUE INDEX unique_keys (user, details)
);
```
### Foreign Key
Like regular indexes, foreign keys can be assigned against a table. When the table is built, it will assume the reference table exists, so ensure that you create them in the correct order if you are creating all tables at once.






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
// or
$buider->create( $table );

// You can also drop tables with.
$buider->drop( $table );
```
> A tablename and valid columns are required, will throw Exceptions if any values are missing

## Testing ##

### PHP Unit ###
If you would like to run the tests for this package, please ensure you add your database details into the test/wp-config.php file before running phpunit.
````bash
$ phpunit
````
````bash 
$ composer test

# To generate coverage report (/coverage-report/*)
$ composer coverage
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
* 0.2.2 - No change, branches a mess
* 0.2.1 - Added in more tests, now has 100% test coverage. Added in more valdation around columns, tablename and indexes. Previously threw php errors for missing or malformed data. Now throw exceptions if Table has no name, a column is lacking key, null, type or length and all indexes which are foreign keys, must have a valid refierence table and column. No changes public methods.
* 0.2.0 - Moved to composer, renamed all namespaces to match the composer format.
