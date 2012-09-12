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
4. Insert 1,000 records with transactions
5. Count all records broken into 10 queries
6. Fetch top 50 records
7. Perform 50 updates
8. Perform 50 deletes

Any test is flawed and this is no exception, but this does show all of the
basic uses and gives a sense of how well each library performs at them.

## Frameworks tested

* [PDO](http://www.php.net/pdo)
* [Doctrine 2](http://www.doctrine-project.org/) DBAL and ORM
* [Elefant CMS](http://www.elefantcms.com/) DB and Model
* [Idiorm and Paris](http://j4mie.github.com/idiormandparis/)
* [Propel ORM](http://www.propelorm.org/)
* [RedBean](http://redbeanphp.com/)
* [Zend_Db](http://framework.zend.com/manual/en/zend.db.html)

## Creating new tests

The tests should be run on an in-memory SQLite database to have a common
baseline. To create a new test, create a folder for it and copy `example.php`
into that folder. Edit and name either `direct.php` or `orm.php`, depending
on the type of database abstraction layer used.

## Results

I ran the tests on an iMac with 3.06 GHz Intel Core 2 Duo with 8 GB RAM
running PHP 5.3.10 with APC enabled and Apache 2.2.21. Here are the numbers
and charts of the execution time and memory usage breakdown:

![PHP Database Library Benchmarks - Numbers](https://github.com/jbroadway/php-dbal-bench/blob/master/results/php-dbal-benchmarks-numbers.png)

![PHP Database Library Benchmarks - Execution Time](https://github.com/jbroadway/php-dbal-bench/blob/master/results/php-dbal-benchmarks-execution-time.png)

![PHP Database Library Benchmarks - Memory Usage](https://github.com/jbroadway/php-dbal-bench/blob/master/results/php-dbal-benchmarks-memory-usage.png)
