<?php

require '../lib/Bench.php';

Bench::mark ('start');

///// Require your database libraries here /////

require 'lib/DB.php';

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

DB::open (array (
	'master' => true,
	'driver' => 'sqlite',
	'file' => ':memory:'
));

/////////////////////////////////////////

Bench::mark ('connected');

///// Create a basic database schema /////

DB::execute ('create table people (id integer primary key, name char(32), email char(48), score int)');
DB::execute ('create index people_name on people (name)');
DB::execute ('create index people_score on people (score)');

//////////////////////////////////////////

Bench::mark ('created tables');

///// Insert 1,000 people with the following loop /////

DB::execute ('begin');
for ($i = 0; $i < 1000; $i++) {
	$name = fake_name () . ' ' . fake_name ();
	$email = str_replace (' ', '.', $name) . '@example.com';
	$score = mt_rand (1000, 999999);

	if (! DB::execute (
		'insert into people (name, email, score) values (?, ?, ?)',
		$name,
		$email,
		$score
	)) {
		DB::execute ('rollback');
		die (DB::error ());
	}
}
DB::execute ('commit');

///////////////////////////////////////////////////////

Bench::mark ('inserted 1000 people');

///// Fetch total people over 10 queries /////

$total = 0;
$queries = 0;
for ($i = 0; $i <= 900000; $i += 100000) {
	$count = DB::shift ('select count(*) from people where score >= ? and score < ?', $i, ($i + 100000));
	
	$total += $count;
	$queries++;
}

//////////////////////////////////////////////

Bench::mark ('counted ' . $total . ' people in ' . $queries . ' queries');

///// Fetch top fifty people /////

$list = DB::fetch ('select * from people order by score desc limit 50');

//////////////////////////////////

Bench::mark ('fetched ' . count ($list) . ' people in 1 query');

///// Perform 50 updates /////

foreach ($list as $person) {
	DB::execute ('update people set score = ? where id = ?', $person->score + 1, $person->id);
}

//////////////////////////////

Bench::mark ('performed ' . count ($list) . ' updates');

///// Perform 50 deletes /////

foreach ($list as $person) {
	DB::execute ('delete from people where id = ?', $person->id);
}

//////////////////////////////

Bench::mark ('performed ' . count ($list) . ' deletes');

Bench::mark (true);

?>