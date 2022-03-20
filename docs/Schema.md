# Schema

> **Related Pages**
>
> » Columns
> 
> » [Index](Index.md)
> 
> » [Foreign_Key](Foreign_Key.md)

***

## __construct(string $table_name, ?callable $configure = null )
> @param string $table_name The name of your table, if you wish to use a prefix, you can set this during the build  
> @param callable|null $configure Used to set the schema values are the same time as declaring the schema object.  

```php
$schema = new Schema('table', function(Schema $schema): void{
    $schema->column('id');
});
// Can be also created  as 
$schema = new Schema('table');
$schema->column('id');
```

***

## prefix( ?string $prefix = null ): self
> @param string|null $prefix  
> @return Schema    

You can set an optional table name prefix either at Schema definition or during the build/drop processes.
```php
$schema = new Schema('table');
$schema->prefix('my_'); // table name = my_table


// Using WPDB to get the current sites prefix (accounts for multisite)
global $wpdb;
$schema = new Schema('table');
$schema->prefix($wpdb->prefix); // table name = wp_table (assuming the prefix set in WPDB is wp_)
```

***

## column( string $name ): Column
> @param string $name  
> @return Column  

Returns a partially constructed Column object, which has its own fluent API for defining the column details. See Column methods for a more detailed explanation.

```php
$schema = new Schema('table', function(Schema $schema): void{
    // Using shortcut types
    $schema->column('id')
        ->text(12)          // Sets as text with a length of 11
        ->default('empty'); // Sets default to empty

    // Using verbose type definitions.
    $schema->column('user_id')
        ->type('text')      // Sets type to TEXT
        ->length(12)        // Sets length to 12
        ->default('empty'); // Sets default to empty
});
```

***

## has_column( string $name ): bool
> @param string $name  
> @return Column  

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

## get_columns(): array
> @return Column[]  

Returns an array of Column objects.

```php
$schema = new Schema('table', function(Schema $schema): void{
    // Using shortcut types
    $schema->column('id')->int(12)->auto_increment();
    $schema->column('user_id')->type('text')->default('empty'); 
});

$schema->get_columns(); // [Column{name: id..}, Column{name: user_id....}]
```

***

## remove_column( string $name ): self 
> @param string $name  
> @return Schema    
> @throws Schema_Validation (Code 301) If column doesn't exist.  

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

## index( string $column, ?string $key_name = null ): Index
> @param string $key_name  
> @return \PinkCrab\Table_Builder\Index  

Sets an Index for the table, returns a partially populated Index instance bound to the defined column. The key_name can either be defined or is generated as "ix_{column_name}". Multiple Indexes set with matching key_names will be defined as a single expression ```INDEX key_name (col1,col2,col3)```

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

## has_indexes(): bool
> @return bool  

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

## get_indexes(): array
> @return Index[] 

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

## foreign_key( string $column, ?string $key_name = null ): Foreign_Key
> @param string $column Column index is applied to  
> @param string|null $key_name The indexes reference  
> @return \PinkCrab\Table_Builder\Foreign_Key  

Creates a Foreign Key relationship with another table. Can be set with a custom or default key_name. Returns a partially applied 

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

## has_foreign_keys(): bool
> @return bool  

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

## get_foreign_keys(): array
> @return Foreign_Key[]  

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

# Column Type Helpers

## json(): Column
> @return Schema  

Defines a column as JSON
> **IF USING MYSQL A DEFAULT CAN NOT BE DEFINED, YOU CAN USING MARIADB**
```php
$schema = new Schema('table', function(Schema $schema): void{
    
    $schema->column('id')->unsigned_int(12)->auto_increment();
    $schema->index('id')->primary();
    
    // Using helper
    $schema->column('json_data')->json();
    // Using Type
    $schema->column('json_data')->type('json');
});
```
***

## varchar( ?int $length = null ): Column
> @param int|null $length  
> @return Schema  

Defines a `VARCHAR(length)` with an optional length
```php
$schema = new Schema('table', function(Schema $schema): void{
    
    $schema->column('id')->unsigned_int(12)->auto_increment();
    $schema->index('id')->primary();
    
    // Using helper
    $schema->column('some_string')->varchar(16);
    // Using Type
    $schema->column('some_string')->type('varchar')->length(16);
});
```

***

## text( ?int $length = null ): Column
> @param int|null $length  
> @return Schema  

Defines a `TEXT(length)` with an optional length
```php
$schema = new Schema('table', function(Schema $schema): void{
    
    $schema->column('id')->unsigned_int(12)->auto_increment();
    $schema->index('id')->primary();
    
    // Using helper
    $schema->column('some_string')->text(16);
    // Using Type
    $schema->column('some_string')->type('text')->length(16);
});
```

***

## int( ?int $length = null ): Column
> @param int|null $length  
> @return Schema  

Defines a `INT(length)` with an optional length
```php
$schema = new Schema('table', function(Schema $schema): void{
    
    $schema->column('id')->unsigned_int(12)->auto_increment();
    $schema->index('id')->primary();
    
    // Using helper
    $schema->column('some_string')->int(16);
    // Using Type
    $schema->column('some_string')->type('int')->length(16);
});
```

***

## float( ?int $length = null ): Column
> @param int|null $length  
> @return Schema  

Defines a `FLOAT(length)` with an optional length
```php
$schema = new Schema('table', function(Schema $schema): void{
    
    $schema->column('id')->unsigned_int(12)->auto_increment();
    $schema->index('id')->primary();
    
    // Using helper
    $schema->column('some_string')->float(16);
    // Using Type
    $schema->column('some_string')->type('float')->length(16);
});
```

***

## double( ?int $length = null ): Column
> @param int|null $length  
> @return Schema  

Defines a `DOUBLE(length)` with an optional length
```php
$schema = new Schema('table', function(Schema $schema): void{
    
    $schema->column('id')->unsigned_int(12)->auto_increment();
    $schema->index('id')->primary();
    
    // Using helper
    $schema->column('some_string')->double(16);
    // Using Type
    $schema->column('some_string')->type('double')->length(16);
});
```

***

## unsigned_int( ?int $length = null ): Column
> @param int|null $length  
> @return Schema  

Defines a `UNSIGNED INT(length)` with an optional length
```php
$schema = new Schema('table', function(Schema $schema): void{
    
    $schema->column('id')->unsigned_int(12)->auto_increment();
    $schema->index('id')->primary();
    
    // Using helper
    $schema->column('some_string')->unsigned_int(16);
    // Using Type
    $schema->column('some_string')->type('unsigned_int')->length(16);
});
```

***


## unsigned_medium( ?int $length = null ): Column
> @param int|null $length  
> @return Schema  

Defines a `UNSIGNED INT(length)` with an optional length
```php
$schema = new Schema('table', function(Schema $schema): void{
    
    $schema->column('id')->unsigned_medium(12)->auto_increment();
    $schema->index('id')->primary();
    
    // Using helper
    $schema->column('some_string')->unsigned_medium(16);
    // Using Type
    $schema->column('some_string')->type('unsigned_int')->length(16);
});
```

***

## datetime( ?string $default = null ): Column
> @param string|null $default  
> @return Schema  

Defines a `DATETIME` with an optional default value
```php
$schema = new Schema('table', function(Schema $schema): void{
    
    $schema->column('id')->unsigned_int(12)->auto_increment();
    $schema->index('id')->primary();
    
    // Using helper
    $schema->column('some_string')->datetime('2012-12-31 23:59:59');
    // Using Type
    $schema->column('some_string')->type('datetime')->default('2012-12-31 23:59:59');
});
```