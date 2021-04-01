# Schema

> **Related Pages**
>
> » Columns
> 
> » Index
> 
> » Foreign_Key

***

## public function __construct(string $table_name, ?callable $configure = null )
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

***

## public function prefix( ?string $prefix = null ): self
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

***

## public function column( string $name ): Column
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

***

## public function has_column( string $name ): bool
* @param string $name
* @return Column

Checks if a column has been set based in its name.

```php
$schema = new Schema('table', function(Schema $schema): void{
    // Using shortcut types
    $schema->column('id')->int(12)->auto_increment();
    $schema->column('user_id')->type('text')->default('empty'); 
});

$schema->has_column('user_id'); // TRUE
```

***

## public function get_columns(): array
* @return array<Column>

Retruns an array of Column objects.

```php
$schema = new Schema('table', function(Schema $schema): void{
    // Using shortcut types
    $schema->column('id')->int(12)->auto_increment();
    $schema->column('user_id')->type('text')->default('empty'); 
});

$schema->get_columns(); // [Column{name: id..}, Column{name: user_id....}]
```

***

## public function remove_column( string $name ): self 
* @param string $name
* @return self
* @throws Exception If columnn doesnt exist.

Removes a colum from the table, can be used to conditionally remove a column before being created. Will throw an exception of the column doesnt exist.

```php
$schema = new Schema('table', function(Schema $schema): void{
    // Using shortcut types
    $schema->column('id')->int(12)->auto_increment();
    $schema->column('user_id')->type('text')->default('empty'); 
    $schema->column('site_id')->type('text')->nullable();
});

// Only use site_id for multisites.
if(! is_multisite()){
    $schema->remove_column('site_id');
}
```

***

## public function index( string $column, ?string $keyname = null ): Index
* @param string $keyname
* @return \PinkCrab\Table_Builder\Index

Sets an Index for the table, returns a partially populated Index instance bound to the defined column. The keyname can either be defined or is generated as "ix_{column_name}". Multiple Indexes set with matching keynames will be defined as a single expression ```INDEX keyname (col1,col2,col3)```

```php
$schema = new Schema('table', function(Schema $schema): void{
    
    $schema->column('id')->unsigned_int(12)->auto_increment();
    $schema->column('user')->int(12);
    $schema->column('booking_ref')->varchar(16);

    $schema->index('id')->primary();
    $schema->index('user')->unique();
    $schema->index('booking_ref')->unique();
});
```
***

## public function has_indexes(): bool
* @return bool

Returns if the table has indexes applied.

```php
$schema = new Schema('table', function(Schema $schema): void{
    
    $schema->column('id')->unsigned_int(12)->auto_increment();
    $schema->column('user')->int(12);
    $schema->column('booking_ref')->varchar(16);
    $schema->index('id')->primary();
});

$schema->has_indexes(); // TRUE
```
***

## public function get_indexes(): array
* @return array<Index>

Returns an array of all indexes currently set to the Schema.

```php
$schema = new Schema('table', function(Schema $schema): void{
    
    $schema->column('id')->unsigned_int(12)->auto_increment();
    $schema->column('user')->int(12);
    $schema->column('booking_ref')->varchar(16);
    $schema->index('id')->primary();
    $schema->index('booking_ref')->unique();
});

$schema->get_indexes(); // [Index{column: id..}, Index{column: booking_ref..}]
```

***

## public function foreign_key( string $column, ?string $keyname = null ): Foreign_Key
* @param string $column Column index is applied to
* @param string|null $keyname The indexes reference
* @return \PinkCrab\Table_Builder\Foreign_Key

Creates a Foreign Key relationship with another table. Can be set with a custom or default keyname. Returns a partially applied 

```php
$schema = new Schema('table', function(Schema $schema): void{
    
    $schema->column('id')->unsigned_int(12)->auto_increment();
    $schema->column('user')->int(12);

    $schema->foreign_key('user', 'user_fk')
        ->reference_table('users')
        ->reference_column('id');
});
```

***

## public function has_foreign_keys(): bool
* @return bool

Returns if the table has Foreign Keys applied.

```php
$schema = new Schema('table', function(Schema $schema): void{
    
    $schema->column('id')->unsigned_int(12)->auto_increment();
    $schema->column('user')->int(12);
    $schema->column('booking_ref')->varchar(16);
    $schema->foreign_key('user', 'user_fk')
        ->reference_table('users')
        ->reference_column('id');
});

$schema->has_foreign_keys(); // TRUE
```
***

## public function get_foreign_keys(): array
* @return array<Foreign_Key>

Returns an array of all Foreign Keys currently set to the Schema.

```php
$schema = new Schema('table', function(Schema $schema): void{
    
    $schema->column('id')->unsigned_int(12)->auto_increment();
    $schema->column('user')->int(12);
    $schema->column('booking_ref')->varchar(16);
    $schema->index('id')->primary();
        $schema->foreign_key('user', 'user_fk')
        ->reference_table('users')
        ->reference_column('id');
});

$schema->get_indexes(); // [Foreign_Key{column: user..}]
```