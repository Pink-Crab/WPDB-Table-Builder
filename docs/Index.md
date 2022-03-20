# Index

> **Related Pages**
>
> » [Schema](Schema.md)

***

## __construct( string $column, ?string $key_name = null )
> @param string $column The column this Index applies to  
> @param string|null $key_name Sets a custom key_name to the index  

Creates a new index for a defined column, with an optional key_name. If the key_name is not passed, will be set at ix_{column_name}

```php
$index = new Index('id', 'id_key');

// When used as part of a schema definition.
$schema = new Schema('table', function(Schema $schema): void{
    $schema->column('id')....
    $schema->index('id', 'id_key'); // Pass through to constructor
});
```

Multiple columns can be created against a single Index. Just create multiple Indexes with matching key names.
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

## get_key_name(): string
> @return string The index key_name  

Returns the key_name, either defualt or defined.

```php
$index_a = new Index('id', 'id_key'); // Defined key_name
$index_a = new Index('user');         // Default key_name (ix_{column_name})

$index_a->get_key_name(); // 'id_key;
$index_a->get_key_name(); // 'ix_user;
```

***

## primary( bool $primary = true ): self
> @param bool $primary  is primary key  

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

## is_primary(): bool
> @return bool  

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

## unique( bool $unique = true ): self
> @param bool $unique  is unique  

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

## is_unique(): bool
> @return bool  

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

## full_text( bool $unique = true ): self
> @param bool $unique  is unique  

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

## is_full_text(): bool
> @return bool  

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