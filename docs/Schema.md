# Schema

### public function __construct(string $table_name, ?callable $configure = null )
* @param string $table_name The name of your table, if you wish to use a prefix, you can set this during the build
* @param callable|null $configure Used to set the schema values are the same time as declaring the schema object.

```php
$schema = new Schema('table', function(Schema $schema): void{
    $schema->column('id');
});
// Can be also created  as 
$schema = new Schema('table');
$schema->column('id');
```
**SETTERS**

### public function prefix( ?string $prefix = null ): self
* @param string|null $prefix
* @return self

You can set an optional table name prefix either at Schema definition or during the build/drop processes.
```php
$schema = new Schema('table');
$schema->prefix('my_'); // table name = my_table


// Using WPDB to get the current sites prefix (acounts for multisite)
global $wpdb;
$schema = new Schema('table');
$schema->prefix($wpdb->prefix); // table name = wp_table (assuming the prefix set in WPDB is wp_)
```

#### public function column( string $name ): Column
* @param string $name
* @return Column

Returns a partially constructed Column object, which has its own fluent API for defining the column details. See Column methods for a more detailed explanation.

```php
$schema = new Schema('table', function(Schema $schema): void{
    // Using shortcut types
    $schema->column('id')
        ->text(12)          // Sets as text with a length of 11
        ->default('empty'); // Sets defualt to empty

    // Using verbose type definitions.
    $schema->column('user_id')
        ->type('text')      // Sets type to TEXT
        ->length(12)        // Sets length to 11
        ->default('empty'); // Sets defualt to empty
});
```

#### public function index( string $column, ?string $keyname = null ): Index
* @param string $keyname
* @return \PinkCrab\Table_Builder\Index

Sets an index on the table


