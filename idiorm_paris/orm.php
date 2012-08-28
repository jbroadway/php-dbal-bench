<?php

require '../lib/Bench.php';

Bench::mark ('start');

///// Require your database libraries here /////

require 'lib/idiorm.php';
require 'lib/paris.php';

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

class Person extends Model {
	public static $_table = 'people';
}

Bench::mark ('loaded classes');

///// Connect to your database here /////

ORM::configure ('sqlite::memory:');
$db = ORM::get_db ();

////////////////////////////////////////////////

Bench::mark ('connected');

///// Execute the following SQL queries /////

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

/////////////////////////////////////////////

Bench::mark ('created tables');

///// Insert 1,000 people with the following loop /////

// begin;
$db->beginTransaction ();

for ($i = 0; $i < 1000; $i++) {
	$name = fake_name () . ' ' . fake_name ();
	$email = str_replace (' ', '.', $name) . '@example.com';
	$score = mt_rand (1000, 999999);

	// insert into people (name, email, score) values (?, ?, ?);
	$person = Model::factory ('Person')->create ();
	$person->name = $name;
	$person->email = $email;
	$person->score = $score;

	if (! $person->save ()) {
		$db->rollback ();
		die ('error');
	}
}

$db->commit ();

///////////////////////////////////////////////////////

Bench::mark ('inserted 1000 people');

///// Fetch total people over 10 queries /////

$total = 0;
$queries = 0;
for ($i = 0; $i <= 900000; $i += 100000) {
	$count = Model::factory ('Person')
		->where_raw ('score >= ' . $i)
		->where_raw ('score < ' . ($i + 100000))
		->count ();
	
	$total += $count;
	$queries++;
}

//////////////////////////////////////////////

Bench::mark ('counted ' . $total . ' people in ' . $queries . ' queries');

///// Fetch top fifty people /////

$list = Model::factory ('Person')->order_by_desc ('score')->limit (50)->find_many ();

//////////////////////////////////

Bench::mark ('fetched ' . count ($list) . ' people in 1 query');

///// Perform 50 updates /////

foreach ($list as $person) {
	$person->score++;
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