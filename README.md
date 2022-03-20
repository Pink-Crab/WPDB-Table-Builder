# Table-Builder
A chainable table schema constructor with (WPDB) DB Delta builder built in.


![alt text](https://img.shields.io/badge/Current_Version-1.1.0-green.svg?style=flat " ") 
[![Open Source Love](https://badges.frapsoft.com/os/mit/mit.svg?v=102)](https://github.com/ellerbrock/open-source-badge/)
![](https://github.com/Pink-Crab/Module__Table_Builder/workflows/GitHub_CI/badge.svg " ")
[![codecov](https://codecov.io/gh/Pink-Crab/WPDB-Table-Builder/branch/master/graph/badge.svg?token=UBWL8S4O8L)](https://codecov.io/gh/Pink-Crab/WPDB-Table-Builder)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Pink-Crab/WPDB-Table-Builder/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Pink-Crab/WPDB-Table-Builder/?branch=master)


> **Related Pages**  
> » [Schema](docs/Schema.md)  
> » [Column](docs/Column.md)  
> » [Index](docs/Index.md)  
> » [Foreign_Key](docs/Foreign_Key.md)  


[You can read the docs on the Perique.info](https://perique.info/lib/Table%20Builder/)


## Version
**Release 1.1.0**

## Why?
For those of you who have used `dbDelta` to create tables in WordPress, to say it's a bit fussy is an understatement. 

The `PinkCrab\Table_Builder` module makes creating tables much easier by providing a more expressive fluent API to define the schema, which can be passed to the Builder to create the table. 

Out of the box, this package comes with the `DB_Delta` builder engine only, but thanks to the `SQL_Builder` interface, engines for other table formats can be created easily.



## Defining a Table's Schema

You can define a table's schema in a few different ways.

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
> [See Schema Docs](docs/Schema.md)

## Columns

The schema is defined from various columns, each column is defined as its own object with a collection of [methods](docs/Column.md#setters) and [helper/shortcuts](docs/Column.md#type-helpers-shortcuts)

## Indexes and Foreign Keys

You can setup a variety of indexes and foreign keys for your table(s). These can be set as in the schema example above.

### Index

You can create an index for any column of your table and denote the field as either unique, primary, full text, hash, or just a regular index.

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
The above would generate:

```sql
CREATE TABLE my_table(
    id INT AUTO_INCREMENT
    user INT(11),
    details TEXT,
    PRIMARY KEY ix_id (id),
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
The above would generate the following for MySQL:

```sql
CREATE TABLE my_table(
    id INT AUTO_INCREMENT
    user INT(11),
    details TEXT,
    PRIMARY KEY ix_id (id),
    UNIQUE INDEX unique_keys (user, details)
);
```

> See [INDEX docs](docs/Index.md)  

### Foreign Key
Like regular indexes, foreign keys can be assigned to a table. When the table is built, it will assume the reference table exists, so ensure that you create them in the correct order if you are creating all tables at once.

```php
<?php

$schema = new Schema('my_table', function(Schema $schema){
    // Set columns
    $schema->column('id')->unsigned_int(11)->auto_increment();
    $schema->column('user')->int(11);
    $schema->column('details')->text();
    
    // Set keys and indexes.
    $schema->index('id')->primary();
    
    $schema->foreign_key('user', 'custom_keyname')
        ->reference('users', 'id');
});
```
The above would produce for MySQL (provided the user table exists with an ID column):

```sql
CREATE TABLE my_table(
    id INT AUTO_INCREMENT
    user INT(11),
    details TEXT,
    PRIMARY KEY ix_id (id),
    FOREIGN INDEX custom_keyname (user) REFERENCES users(id)
);
```

> See [Foreign Key docs](docs/Index.md)  


## Creating & Dropping Tables

You can populate the builder with any engine. Included in this package is the `DB_Delta_Engine` engine, which internally uses the WordPress `dbDelta` function to create and drop tables.

### Create
```php
$schema = new Schema('table', function(Schema $schema): void{
    ... create schema ...
});

// Create instance of builder with DB Delta engine.
$engine  = new DB_Delta_Engine($wpdb);
$builder = new Builder($engine);

// Create table.
try{
    // Returns true for success, false for WPDB errors being present.
    $response = $builder->create_table($schema);
} catch (\Exception $e) {
    // Do something to catch validation errors.
}

```
### Drop
```php
$schema = new Schema('table', function(Schema $schema): void{
    ... create schema ...
});

// Create instance of builder with DB Delta engine.
$engine  = new DB_Delta_Engine($wpdb);
$builder = new Builder($engine);

// Create table.
try{
    // Returns true for success, false for WPDB errors being present.
    $response = $builder->drop_table($schema);
} catch (\Exception $e) {
    // Do something to catch validation errors.
}

```


## License ##

### MIT License ###
http://www.opensource.org/licenses/mit-license.html  

## Change Log ##
* 1.1.0 - Added JSON column support, fixed precision issue with floating point column, improved docs (with help from [ZebulanStanphill](https://github.com/ZebulanStanphill))
* 1.0.0 - Added 2 new methods to the engine interface and wpdb implementation to return the queries used to create table and drop table. 
* 0.3.0 - Changed how much of the API works; some of the externals have changed. It no longer accepts fully fluent creation, and index/foreign keys have been separated.
* 0.2.2 - No change, branches a mess.
* 0.2.1 - Added in more tests, now has 100% test coverage. Added in more valdation around columns, tablename and indexes. Previously threw PHP errors for missing or malformed data. Now throws exceptions if Table has no name, a column is lacking key, null, type or length and all indexes which are foreign keys, must have a valid reference table and column. No changes to public methods.
* 0.2.0 - Moved to Composer, renamed all namespaces to match the Composer format.

## Contributions ##
If you would like to contribute to this project, please feel to create an issue, then submit a PR.

> [Glynn Quelch](https://github.com/gin0115/)  
> [ZebulanStanphill](https://github.com/ZebulanStanphill)

