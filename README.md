# php-dbal-bench

This is an attempt to benchmark several PHP database abstraction layers.
Where one offers ORM, there will be an orm.php in addition to a `direct.php`
file with the tests for that library.

To run, drop the `php-dbal-bench` folder into your site root and load
`/php-dbal-bench/` in your web browser.

## The tests

The tests run through the following steps:

1. Require your database libraries
2. Connect to your database
3. Create a basic database schema
4. Insert 1,000 records
5. Count records over 10 queries
6. Fetch top 50 records
7. Perform 50 updates
8. Perform 50 deletes

## Creating new tests

The tests should be run on an in-memory SQLite database to have a common
baseline. To create a new test, create a folder for it and copy `example.php`
into that folder. Edit and name either `direct.php` or `orm.php`, depending
on the type of database abstraction layer used.
