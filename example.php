<?php

/**
 * This is an example of the structure of a database test.
 * Copy this file into a subfolder of php-dbal-bench as
 * direct.php and edit it to create a new test.
 */

require '../lib/Bench.php';

Bench::mark ('start');

///// Require your database libraries here /////

require '.php';

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

////////////////////////////////////////////////

Bench::mark ('connected');

///// Create a basic database schema /////

// create table people (id integer primary key, name char(32), email char(48), score int);
// create index people_name on people (name);
// create index people_score on people (score);

//////////////////////////////////////////

Bench::mark ('created tables');

///// Insert 1,000 people with the following loop /////

// begin;

for ($i = 0; $i < 1000; $i++) {
	$name = fake_name () . ' ' . fake_name ();
	$email = str_replace (' ', '.', $name) . '@example.com';
	$score = mt_rand (1000, 999999);

	// insert into people (name, email, score) values (?, ?, ?);

	// if (/*error*/) {
	//     rollback;
	//     die (DB::error ());
	// }
}

// commit;

///////////////////////////////////////////////////////

Bench::mark ('inserted 1000 people');

///// Fetch total people over 10 queries /////

$total = 0;
$queries = 0;
for ($i = 0; $i <= 900000; $i += 100000) {
	// $count = select count(*) from people where score >= ? and score < ?;
	// $i
	// $i + 100000
	
	$total += $count;
	$queries++;
}

///////////////////////////////////////////////////

Bench::mark ('counted ' . $total . ' people in ' . $queries . ' queries');

///// Fetch top fifty people /////

// $list = select * from people order by score desc limit 50;

//////////////////////////////////

Bench::mark ('fetched top ' . count ($list) . ' people in 1 query');

///// Perform 50 updates /////

foreach ($list as $person) {
	// update people set name = ?, email = ?, score = ? where id = ?;
	// $person['name']
	// $person['email']
	// $person['score']
	// $person['id']
}

//////////////////////////////

Bench::mark ('performed ' . count ($list) . ' updates');

///// Perform 50 deletes /////

foreach ($list as $person) {
	// delete from people where id = ?;
	// $person['id']
}

//////////////////////////////

Bench::mark ('performed ' . count ($list) . ' deletes');

Bench::mark (true);

?>