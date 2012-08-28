<?php

/**
 * This is an example of the structure of a database test.
 * Copy this file into a subfolder of php-dbal-bench as
 * direct.php and edit it to create a new test.
 */

require '../lib/Bench.php';

Bench::mark ('start');

///// Require your database libraries here /////

require 'vendor/autoload.php';

////////////////////////////////////////////////

function fake_name () {
	$list = 'abcdefghijklmnopqrstuvwxyz';
	$name = '';
	$length = mt_rand (4, 8);
	while (strlen ($name) < $length) {
		$name .= substr ($list, mt_rand (0, strlen ($list)), 1);
	}
	return $name;
}

Bench::mark ('loaded classes');

///// Connect to your database here /////

$config = new \Doctrine\DBAL\Configuration ();

$db = \Doctrine\DBAL\DriverManager::getConnection (array (
	'driver' => 'pdo_sqlite',
	'memory' => true
), $config);

////////////////////////////////////////////////

Bench::mark ('connected');

///// Create a basic database schema /////

$db->executeUpdate ('create table people (id integer primary key, name char(32), email char(48), score int)');
$db->executeUpdate ('create index people_name on people (name)');
$db->executeUpdate ('create index people_score on people (score)');

//////////////////////////////////////////

Bench::mark ('created tables');

///// Insert 1,000 people with the following loop /////

$db->beginTransaction ();

try {
	$stmt = $db->prepare ('insert into people (name, email, score) values (?, ?, ?)');

	for ($i = 0; $i < 1000; $i++) {
		$name = fake_name () . ' ' . fake_name ();
		$email = str_replace (' ', '.', $name) . '@example.com';
		$score = mt_rand (1000, 999999);
	
		$stmt->bindValue (1, $name);
		$stmt->bindValue (2, $email);
		$stmt->bindValue (3, $score);
	
		$stmt->execute ();
	}
	
	$db->commit ();
} catch (Exception $e) {
	$db->rollback ();
	die ($e->getMessage ());
}

///////////////////////////////////////////////////////

Bench::mark ('inserted 1000 people');

///// Fetch total people over 10 queries /////

$total = 0;
$queries = 0;
for ($i = 0; $i <= 900000; $i += 100000) {
	$count = $db->fetchColumn (
		'select count(*) from people where score >= ? and score < ?',
		array ($i, $i + 100000),
		0
	);
	
	$total += $count;
	$queries++;
}

///////////////////////////////////////////////////

Bench::mark ('counted ' . $total . ' people in ' . $queries . ' queries');

///// Fetch top fifty people /////

$list = $db->fetchAll ('select * from people order by score desc limit 50');

//////////////////////////////////

Bench::mark ('fetched top ' . count ($list) . ' people in 1 query');

///// Perform 50 updates /////

foreach ($list as $person) {
	$db->executeUpdate (
		'update people set score = ? where id = ?',
		array ($person['score'] + 1, $person['id'])
	);
}

//////////////////////////////

Bench::mark ('performed ' . count ($list) . ' updates');

///// Perform 50 deletes /////

foreach ($list as $person) {
	$db->executeUpdate (
		'delete from people where id = ?',
		array ($person['id'])
	);
}

//////////////////////////////

Bench::mark ('performed ' . count ($list) . ' deletes');

Bench::mark (true);

?>