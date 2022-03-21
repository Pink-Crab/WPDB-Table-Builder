# Foreign_Key

> **Related Pages**
>
> » [Schema](Schema.md)

***

> When creating a foreign key reference, ensure the column you are linking to is set as a unique/primary index. Also ensure your reference table exists, or is created before this one.

## __construct( string $column, ?string $key_name = null )
> @param string $column The column this Foreign_Key applies to  
> @param string|null $key_name Sets a custom key_name to the Foreign_Key  

Creates a new Foreign_Key for a defined column, with an optional key_name. If the key_name is not passed, will be set at fk_{column_name}

```php
$index = new Foreign_Key('id', 'id_key');

// When used as part of a schema definition.
$schema = new Schema('table', function(Schema $schema): void{
    $schema->column('id')....
    $schema->foreign_key('id', 'id_fk')   // Pass through to constructor
        ->...; 
});
```

***

## get_column(): string
> @return string The column the Foreign Key is assigned to.  

Returns the set column, used in constructor.

```php
$key = new Foreign_Key('column-a'); 
$key->get_column();                 // 'column-a'
```

***

## get_key_name(): string
> @return string The Foreign_Key key_name  

Returns the key_name, either defualt or defined.

```php
$f_key = new Foreign_Key('id', 'id_key'); // Defined key_name
$f_key = new Foreign_Key('user');         // Defualt key_name (fk_{colmn_name})

$f_key->get_keyname(); // 'id_key;
$f_key->get_keyname(); // 'fk_user;
```

***


## reference_table( string $reference_table ): Foreign_Key
> @param string $reference_table The table which this is referencing.  
> @return Foreign_Key   

Sets which table is used as the reference for this column.

```php
$schema = new Schema('table', function(Schema $schema): void{
    ....
    $schema->column('external')....
    
    $schema->foreign_key('something_external') 
        ->reference_table('my_other_table')
        ->....; 
});
```

***

## get_reference_table(): ?string
> @return string|null The set reference_table, or null if not set.  

Gets the table that is used in the Foreign_Key reference data. If this has not been set, it will return null and should throw an exception if passed to a builder without being defined. 

This is maninly used intnerally within a Builder

```php
$f_key = new Foreign_Key('id', 'id_key') 
    ->reference_table('my_other_table')
    ->....; 

$f_key->get_reference_table(); // 'my_other_table'
```

***

## reference_column( string $reference_column ): Foreign_Key
> @param string $reference_column Column on reference tablet to use.  
> @return Foreign_Key   

Once you have set your remote table, its just a case of referencing the column to be used.

```php
$schema = new Schema('table', function(Schema $schema): void{
    ....
    $schema->column('external')....
    
    $schema->foreign_key('something_external') 
        ->reference_table('my_other_table')
        ->reference_column('column_a'); 
});
```

***

## get_reference_column(): ?string
> @return string|null The set reference_column, or null if not set.  

Gets the columns that is used in the Foreign_Key reference data. If this has not been set, it will return null and should throw an exception if passed to a builder without being defined. 

This is maninly used intnerally within a Builder

```php
$f_key = new Foreign_Key('id', 'id_key') 
    ->reference_table('my_other_table')
    ->reference_column('column_a'); 

$f_key->get_reference_column(); // 'column_a'
```

***

## on_update( string $action ): Foreign_Key
> @param string $action The action to be carried out.  
> @return Foreign_Key   

Allow the setting of the ON_UPDATE event whenever working with data as part of the relationship.

```php
$schema = new Schema('table', function(Schema $schema): void{
    ....
    $schema->column('external')....
    
    $schema->foreign_key('something_external') 
        ->reference_table('my_other_table')
        ->reference_column('column_a')
        ->on_update('CASCADE'); 
});
```

***

## get_on_update(): ?string
> @return string The defined on_update operation, if not return null   

Get the set action to carry on an update of the reference data.

```php
$f_key = new Foreign_Key('id', 'id_key') 
    ->reference_table('my_other_table')
    ->reference_column('column_a')
    ->on_update('CASCADE');

$f_key->get_on_update(); // 'CASCADE'
```

***

## on_delete( string $action ): Foreign_Key
> @param string $action The action to be carried out on reference data delete.  
> @return Foreign_Key   

Allow the setting of the ON_DELETE event whenever working with data as part of the relationship.

```php
$schema = new Schema('table', function(Schema $schema): void{
    ....
    $schema->column('external')....
    
    $schema->foreign_key('something_external') 
        ->reference_table('my_other_table')
        ->reference_column('column_a')
        ->on_delete('CASCADE'); 
});
```

***

## get_on_delete(): ?string
> @return string The defined on_delete operation, if not return null   

Get the set action to carry on delete of the reference data. 

```php
$f_key = new Foreign_Key('id', 'id_key') 
    ->reference_table('my_other_table')
    ->reference_column('column_a')
    ->on_delete('CASCADE');

$f_key->get_on_delete(); // 'CASCADE'
```

***

## export(): object
> @return object Returns a simple object representation of the internal state.  
  
```php
$f_key = new Foreign_Key('id', 'id_key') 
    ->reference_table('my_other_table')
    ->reference_column('column_a')
    ->on_update('CASCADE')
    ->on_delete('CASCADE');

$f_key->export(); 
```
> Returns
```json
{
    "keyname"          : "id_key",
    "column"           : "id",
    "reference_column" : "my_other_table",
    "reference_table"  : "column_a",
    "on_update"        : "CASCADE",
    "on_delete"        : "CASCADE"
}
```
