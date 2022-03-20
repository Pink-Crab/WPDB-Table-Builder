# Column

> **Related Pages**
>
> » [Schema](Schema.md)

The model of a tables column.

# SETTERS

***

## type( string $type ): Column
> @param string $type Defines the column type, only valid MYSQL types should be used  
> @return self  
 ```php
$schema = new Schema('table', function(Schema $schema): void{
    // Defined a column with the type of TEXT
    $schema->column('foo')->type('text');
]);
```
```sql
CREATE TABLE table(
    foo TEXT,
);
```
***

## length( string $length ): Column
> @param string $length Defines the column length, only valid MYSQL lengths should be used  
> @return self  
 ```php
$schema = new Schema('table', function(Schema $schema): void{
    // Defined a column with the type of LENGTH
    $schema->column('foo')->type('int')->length(12);
]);
```
```sql
CREATE TABLE table(
    foo INT(11)  NOT NULL,
);
```

***

## precision( string $precision ): Column
> @param string $precision Defines the precision of floating point columns. 
> @return self  
 ```php
$schema = new Schema('table', function(Schema $schema): void{
    // Defined a column with the type of LENGTH
    $schema->column('foo')->type('decimal')->length(12)->precision(2);
]);
```
```sql
CREATE TABLE table(
    foo DECIMAL(12, 2)  NOT NULL,
);
```

***

## nullable( string $nullable = true ): Column
> @param string $nullable Defines if the column can allow NULL as a value  
> @return self  
 ```php
$schema = new Schema('table', function(Schema $schema): void{
    // Defined a column with the type of nullable
    $schema->column('foo')->varchar(255)->nullable(true);
    $schema->column('bar')->text()->nullable(false);
]);
```
```sql
CREATE TABLE table(
    foo VARCHAR(255) NULL, 
    bar TEXT NOT NULL, 
);
```

***

## default( string $default ): Column
> @param string $default Defines the default value for a column
> @return self  
 ```php
$schema = new Schema('table', function(Schema $schema): void{
    // Defined a column with the type of nullable
    $schema->column('foo')->varchar(255)->default('HAPPY');
]);
```
```sql
CREATE TABLE table(
    foo VARCHAR(255) NULL, 
    bar TEXT NOT NULL, 
);
```

***

# Type Helpers

## json(): Column
> @return Column  

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
> @param int|null $length Sets the max length of the columns value, passing null ommits setting length.  
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
> @param int|null $length Sets the max length of the columns value, passing null ommits setting length.  
> @return Column  

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
> @param int|null $length Sets the max length of the columns value, passing null ommits setting length.  
> @return Column  

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
> @param int|null $length Sets the max length of the columns value, passing null ommits setting length.  
> @return Column  

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
> @param int|null $length Sets the max length of the columns value, passing null ommits setting length.  
> @return Column  

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
> @param int|null $length Sets the max length of the columns value, passing null ommits setting length.  
> @return Column  

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
> @param int|null $length Sets the max length of the columns value, passing null ommits setting length.  
> @return Column  

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
> @return Column  

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