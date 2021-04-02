# Foreign_Key

> **Related Pages**
>
> » [Schema](Schema.md)

***

## public function __construct( string $column, ?string $keyname = null )
* @param string $column The column this Foreign_Key applies to
* @param string|null $keyname Sets a custom keyname to the Foreign_Key

Creates a new Foreign_Key for a defined column, with an optional keyname. If the keyname is not passed, will be set at fk_{column_name}

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

## public function get_column(): string
* @return string The column the Foreign Key is assigned to.

Returns the set column, used in constructor.

```php
$key = new Foreign_Key('column-a'); // Defined keyname
$key->get_column();                 // 'column-a'
```

***

## public function get_keyname(): string
* @return string The Foreign_Key keyname

Returns the keyname, either defualt or defined.

```php
$f_key = new Foreign_Key('id', 'id_key'); // Defined keyname
$f_key = new Foreign_Key('user');         // Defualt keyname (fk_{colmn_name})

$f_key->get_keyname(); // 'id_key;
$f_key->get_keyname(); // 'fk_user;
```

***


## public function reference_table( string $reference_table ): Foreign_Key
* @param string $reference_table The table which this is referencing.
* @return Foreign_Key 

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

## public function get_reference_table(): ?string
* @return string|null The set reference_table, or null if not set.

Gets the table that is used in the Foreign_Key reference data. If this has not been set, it will return null and should throw an exception if passed to a builder without being defined. 

This is maninly used intnerally within a Builder

```php
$f_key = new Foreign_Key('id', 'id_key') 
    ->reference_table('my_other_table')
    ->....; 

$f_key->get_reference_table(); // 'my_other_table'
```

***

## public function reference_column( string $reference_column ): Foreign_Key
* @param string $reference_column Column on reference tablet to use.
* @return Foreign_Key 

Once you have set your remote table, its just a case of referencing the column to be used.

```php
$schema = new Schema('table', function(Schema $schema): void{
    ....
    $schema->column('external')....
    
    $schema->foreign_key('something_external') 
        ->reference_table('my_other_table')
        ->reference_column('colum_a'); 
});
```

***

## public function get_reference_column(): ?string
* @return string|null The set reference_column, or null if not set.

Gets the columns that is used in the Foreign_Key reference data. If this has not been set, it will return null and should throw an exception if passed to a builder without being defined. 

This is maninly used intnerally within a Builder

```php
$f_key = new Foreign_Key('id', 'id_key') 
    ->reference_table('my_other_table')
    ->reference_column('colum_a'); 

$f_key->reference_column(); // 'my_other_table'
```

***