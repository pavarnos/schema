Simple PHP and MySQL schema sync / migration tool.

This is an ancient library i wrote in 2007 to allow me to have a database schema definition all in PHP
and (at the click of a button) generate alter table statements to synch the mysql server with the PHP.
This was handy for a number of sites that had a very limited number of users. It allowed 

- a build process to update the schema on demand. 
- custom reports to introspect the schema and generate a reasonably rich description of the tables and fields. Users
  could pick which a table to start with and the report writer knows what tables can join with it because of the 
  foreign key relationships.
- a database diagram to be generated with graphviz
- and automated schema documentation, both supporting the report builder.
- cross checking of foreign key relationships so we could do different kinds of data integrity checks 
  (without relying on the DBMS to maintain referential integrity, which didn't make sense in all cases)
- checking that dependant models are correctly wired up to handle onChange and onDelete events when data in the parent
  model changes. (This is done in a pre-release build step).

Yeah, there is better ways to do this now with full ORMs and other goodness. But this is simple, battle tested over 
many years, fast / light, and I still use it in a few places. 

So here it is for me to pull in via composer and for you to maybe use if you feel like it. Recently updated to

- use PHP 5.5 features
- PSR-4 autoloading
- high level of unit test coverage
- better separation of concerns (though I needed to maintain some compatibility with my old code so I didn't have to rewrite everything that used it).

### Declare your schema programatically

```php
$schema = new LSS\Schema();
$userTable = new LSS\Schema\Table('user', 'users who can log in');
$userTable->addColumn(new Column\PrimaryKey('id', 'unique user id'));
$userTable->addColumn(new Column\String('first_name', 'user first name', false, 40));
$userTable->addColumn(new Column\String('family_name', 'user family name', false, 40));
$userTable->addColumn(new Column\String('email', 'email address', false, 200));
$userTable->addColumn(new Column\String('password', 'bcrypt encoded password', false, 60));
$schema->add($userTable);

$groupTable = new LSS\Schema\Table('group', 'groups of users');
$groupTable->addColumn(new Column\PrimaryKey('id', 'unique group id'));
$groupTable->addColumn(new Column\String('name', 'name of the group', false, 40));
$groupTable->addColumn(new Column\Text('name', 'description of the group'));
$schema->add($groupTable);

$userGroupTable->addColumn(new Column\ForeignKey($userTable->getName(), 'which user'));
$userGroupTable->addColumn(new Column\ForeignKey($groupTable->getName(), 'which group'));
$schema->add($userGroupTable);

// get the DDL for the database (if needed)
$ddlGenerator = new LSS\Schema\Renderer\AlterTableSQL();
$ddl = $ddlGenerator->render($schema);
```

### Parse a mysqldump

```php
$sql = 'create table....'; // mysql ddl from mysqldump 
$parsedSchema = new LSS\Schema();
$parser = new LSS\Schema\Parser();
$parser->parse($parsedSchema,$sql);

```

### Compare the two

```php
$comparator = new LSS\Schema\Renderer\AlterTableSQL();
$alterTableSQL = $comparator->render($schema, $parsedSchema);
// contains the alter table statements to update the mysql server and make it look like $schema

```

### known limitations

- cannot have a ; in a table or field comment
- does not handle the meta stuff at the end of the table eg type, encoding etc
- works better with a single integer primary key index field first in the table
- the sync / comparator can cope with one or more columns added / deleted renamed but gets easily confused
  if you do a lot of big changes at once. Having unique comments on each field helps it resync itself. You can 
  change one of (column name, data type, comment) for the field to be altered. Change two and it will
  think it is a new field, deleting the old one and adding a new one

### todo

- document example convenience methods for quickly building schemas
