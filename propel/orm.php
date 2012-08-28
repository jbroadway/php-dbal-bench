<?php

/**
 * This is an example of the structure of a database test.
 * Copy this file into a subfolder of php-dbal-bench as
 * direct.php and edit it to create a new test.
 */

require '../lib/Bench.php';

Bench::mark ('start');

///// Require your database libraries here /////

require 'propel/runtime/lib/Propel.php';
Propel::init (__DIR__ . '/people/build/conf/people-conf.php');
set_include_path (__DIR__ . '/people/build/classes' . PATH_SEPARATOR . get_include_path ());

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

$db = Propel::getConnection (PersonPeer::DATABASE_NAME);

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

//////////////////////////////////////////

Bench::mark ('created tables');

///// Insert 1,000 people with the following loop /////

$db->beginTransaction ();

try {
	for ($i = 0; $i < 1000; $i++) {
		$name = fake_name () . ' ' . fake_name ();
		$email = str_replace (' ', '.', $name) . '@example.com';
		$score = mt_rand (1000, 999999);

		$p = new Person ();
		$p->setName ($name);
		$p->setEmail ($email);
		$p->setScore ($score);
		$p->save ();
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
	$count = PersonQuery::create ()
		->filterByScore (array ('min' => $i, 'max' => $i + 99999))
		->count ();
	
	$total += $count;
	$queries++;
}

///////////////////////////////////////////////////

Bench::mark ('counted ' . $total . ' people in ' . $queries . ' queries');

///// Fetch top fifty people /////

$list = PersonQuery::create ()
	->orderByScore ('desc')
	->limit (50)
	->find ();

//////////////////////////////////

Bench::mark ('fetched top ' . count ($list) . ' people in 1 query');

///// Perform 50 updates /////

foreach ($list as $person) {
	$person->setScore ($person->getScore () + 1);
	$person->save ();
}

//////////////////////////////

Bench::mark ('performed ' . count ($list) . ' updates');

///// Perform 50 deletes /////

foreach ($list as $person) {
	$person->delete ();
}

//////////////////////////////

Bench::mark ('performed ' . count ($list) . ' deletes');

Bench::mark (true);

?>