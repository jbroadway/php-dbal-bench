<?php

/**
 * This is an example of the structure of a database test.
 * Copy this file into a subfolder of php-dbal-bench as
 * direct.php and edit it to create a new test.
 */

require '../lib/Bench.php';

Bench::mark ('start');

///// Require your database libraries here /////

ini_set ('include_path', ini_get ('include_path') . ':library');
require 'library/Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance ();

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

$db = Zend_Db::factory ('Pdo_Sqlite', array (
	'dbname' => ':memory:'
));

////////////////////////////////////////////////

Bench::mark ('connected');

///// Create a basic database schema /////

$conn = $db->getConnection ();
$conn->exec ('create table people (id integer primary key, name char(32), email char(48), score int)');
$conn->exec ('create index people_name on people (name)');
$conn->exec ('create index people_score on people (score)');

//////////////////////////////////////////

Bench::mark ('created tables');

///// Insert 1,000 people with the following loop /////

$db->beginTransaction ();

try {
	for ($i = 0; $i < 1000; $i++) {
		$name = fake_name () . ' ' . fake_name ();
		$email = str_replace (' ', '.', $name) . '@example.com';
		$score = mt_rand (1000, 999999);
	
		$db->insert ('people', array (
			'name' => $name,
			'email' => $email,
			'score' => $score
		));
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
	$count = $db->fetchOne (
		'select count(*) as count from people where score >= ? and score < ?',
		array ($i, $i + 100000)
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
	$db->update (
		'people',
		array (
			'score' => $person['score'] + 1
		),
		array (
			'id = ?' => $person['id']
		)
	);
}

//////////////////////////////

Bench::mark ('performed ' . count ($list) . ' updates');

///// Perform 50 deletes /////

foreach ($list as $person) {
	$db->delete ('people', array ('id = ?' => $person['id']));
}

//////////////////////////////

Bench::mark ('performed ' . count ($list) . ' deletes');

Bench::mark (true);

?>