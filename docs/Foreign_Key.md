# Foreign_Key

> **Related Pages**
>
> » [Schema](Schema.md)

***

## public function __construct( string $column, ?string $keyname = null )
* @param string $column The column this Index applies to
* @param string|null $keyname Sets a custom keyname to the index

Creates a new index for a defined column, with an optional keyname. If the keyname is not passed, will be set at ix_{column_name}

```php
$index = new Index('id', 'id_key');

// When used as part of a schema definition.
$schema = new Schema('table', function(Schema $schema): void{
    $schema->column('id')....
    $schema->index('id', 'id_key'); // Pass through to constructor
});
```