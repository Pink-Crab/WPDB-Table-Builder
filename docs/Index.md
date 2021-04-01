# Index

> **Related Pages**
>
> » Schema

***

## public function __construct( string $column, ?string $keyname = null )
* @param string $column The column this Index applies to
* @param string|null $keyname Sets a custom keyname to the index

Creates a new index for a defined column, with an optional keyname. If the keyname is not passed, will be set at ix_{column_name}

```php
$index = new Index('id', 'id_key');

// When used as part of a schema definition.
$schema = new Schema('table', function(Schema $schema): void{
    $schema->column('id')
    $schema->index('id', 'id_key'); // Pass through to constructor
});
```

Multiple columns can be created against a single Index. Just create multiple Indexes with matching keynames.
```php
$index = new Index('id', 'id_key');

// When used as part of a schema definition.
$schema = new Schema('bookings', function(Schema $schema): void{
    $schema->column('id');
    ...
    $schema->index('user', 'ix_booking')->unique();
    $schema->index('booking_ref', 'ix_booking')->unique();
    $schema->index('confirmation_ref', 'ix_booking')->unique();
});
```
```sql
# MySql

CREATE TABLE bookings(
    id INT AUTO_INCREMENT
    ....
    UNIQUE INDEX ix_booking (user, booking_ref, confirmation_ref)
);
```

***

## public function primary( bool $primary = true ): self
* @param bool $primary  is primary key

Sets an index as the table primary key/index. Should only be used once, will throw exceptions on build if multiple exists.

```php
$index = new Index('id', 'id_key')->primary();

// When used as part of a schema definition.
$schema = new Schema('some_table', function(Schema $schema): void{
    $schema->column('id')
    ...
    $schema->index('id')->primary();
});
```
```sql
# MySql

CREATE TABLE some_table(
    id INT AUTO_INCREMENT
    ...
    PIRMARY KEY ix_id (id)
);
```