<?php

/**
 * This is an example of the structure of a database test.
 * Copy this file into a subfolder of php-dbal-bench as
 * direct.php and edit it to create a new test.
 */

require '../lib/Bench.php';

Bench::mark ('start');

///// Require your database libraries here /////

require 'lib/rb.php';

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

// PDO::connect ();
R::setup ('sqlite::memory:');
$dba = R::$adapter->getDatabase ();
$db = $dba->getPDO ();

////////////////////////////////////////////////

Bench::mark ('connected');

///// Create a basic database schema /////

try {
	$stmt = $db->prepare ('create table people (id integer primary key, name char(32), email char(48), score int)');
	$stmt->execute ();
} catch (PDOException $e) {
	die ($e->getMessage ());
}

try {
	$stmt = $db->prepare ('create index people_name on people (name)');
	$stmt->execute ();
} catch (PDOException $e) {
	die ($e->getMessage ());
}

try {
	$stmt = $db->prepare ('create index people_score on people (score)');
	$stmt->execute ();
} catch (PDOException $e) {
	die ($e->getMessage ());
}

R::freeze (true);

//////////////////////////////////////////

Bench::mark ('created tables');

///// Insert 1,000 people with the following loop /////

R::begin ();

try {
	for ($i = 0; $i < 1000; $i++) {
		$name = fake_name () . ' ' . fake_name ();
		$email = str_replace (' ', '.', $name) . '@example.com';
		$score = mt_rand (1000, 999999);
	
		// insert into people (name, email, score) values (?, ?, ?);
		$p = R::dispense ('people');
		$p->name = $name;
		$p->email = $email;
		$p->score = $score;
		R::store ($p);
	}
	R::commit ();
} catch (Exception $e) {
	R::rollback ();
	die ($e->getMessage ());
}

///////////////////////////////////////////////////////

Bench::mark ('inserted 1000 people');

///// Fetch total people over 10 queries /////

$total = 0;
$queries = 0;
for ($i = 0; $i <= 900000; $i += 100000) {
	$count = R::getCell (
		'select count(*) from people where score >= ? and score < ?',
		array ($i, $i + 100000)
	);
	
	$total += $count;
	$queries++;
}

///////////////////////////////////////////////////

Bench::mark ('counted ' . $total . ' people in ' . $queries . ' queries');

///// Fetch top fifty people /////

$list = R::findAll ('people', ' order by score desc limit 50 ');

//////////////////////////////////

Bench::mark ('fetched top ' . count ($list) . ' people in 1 query');

///// Perform 50 updates /////

foreach ($list as $person) {
	$person->score++;
	R::store ($person);
}

//////////////////////////////

Bench::mark ('performed ' . count ($list) . ' updates');

///// Perform 50 deletes /////

foreach ($list as $person) {
	R::trash ($person);
}

//////////////////////////////

Bench::mark ('performed ' . count ($list) . ' deletes');

Bench::mark (true);

?>