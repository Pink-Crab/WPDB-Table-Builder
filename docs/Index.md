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
    $schema->column('id')....
    $schema->index('id', 'id_key'); // Pass through to constructor
});
```

Multiple columns can be created against a single Index. Just create multiple Indexes with matching keynames.
```php
$index = new Index('id', 'id_key');

// When used as part of a schema definition.
$schema = new Schema('bookings', function(Schema $schema): void{
    $schema->column('id')....
    ...
    $schema->index('user', 'ix_booking')->unique();
    $schema->index('booking_ref', 'ix_booking')->unique();
    $schema->index('confirmation_ref', 'ix_booking')->unique();
});
```
```sql
# MySql

CREATE TABLE bookings(
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
    $schema->column('id')...
    ...
    $schema->index('id')->primary();
});
```

***

## public function is_primary(): bool
* @return bool

Returns true if the index is a primary key

```php
$schema = new Schema('some_table', function(Schema $schema): void{
    $schema->column('id')...
    ...
    $schema->index('id')->primary();
});

$schema->get_indexes()[0]->is_primary(); // true
```

***

## public function unique( bool $unique = true ): self
* @param bool $unique  is unique

Denotes if a column has a unique value.

```php
$index = new Index('user_ref', 'user')->unique();

// When used as part of a schema definition.
$schema = new Schema('some_table', function(Schema $schema): void{
    $schema->column('id')...
    ...
    $schema->index('user_ref')->unique();
});
```
```sql
# MySql

CREATE TABLE some_table(
    ...
    UNIQUE INDEX ix_user (user_ref)
);
```

***

## public function is_unique(): bool
* @return bool

Returns true if the index is unique

```php
$schema = new Schema('some_table', function(Schema $schema): void{
    $schema->column('id')...
    ...
    $schema->index('user_ref')->unique();
});

$schema->get_indexes()[0]->is_unique(); // true
```

***

## public function full_text( bool $unique = true ): self
* @param bool $unique  is unique

Denotes if a column has a unique value.

```php
$index = new Index('user_ref', 'user')->full_text();

// When used as part of a schema definition.
$schema = new Schema('some_table', function(Schema $schema): void{
    $schema->column('id')...
    ...
    $schema->index('user_ref')->full_text();
});
```
```sql
# MySql

CREATE TABLE some_table(
    ...
    FULLTEXT ix_user_ref (user_ref)
);
```

## public function is_full_text(): bool
* @return bool

Checks if using a full text index.

```php
$schema = new Schema('some_table', function(Schema $schema): void{
    $schema->column('id')...
    ...
    $schema->index('user_ref')->full_text();
});

$schema->get_indexes()[0]->is_full_text(); // true
```

***