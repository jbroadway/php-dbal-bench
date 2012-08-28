<?php

require '../lib/Bench.php';

Bench::mark ('start');

///// Require your database libraries here /////

//require '.php';

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

try {
	$db = new PDO ('sqlite::memory:');
} catch (PDOException $e) {
	die ($e->getMessage ());
}

/////////////////////////////////////////

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

// begin;
try {
	$db->beginTransaction ();
	$stmt = $db->prepare ('insert into people (name, email, score) values (?, ?, ?)');

	for ($i = 0; $i < 1000; $i++) {
		$name = fake_name () . ' ' . fake_name ();
		$email = str_replace (' ', '.', $name) . '@example.com';
		$score = mt_rand (1000, 999999);

		$res = $stmt->execute (array ($name, $email, $score));

		if (! $res) {
			$db->rollback ();
		}
	}

	$db->commit ();
} catch (PDOException $e) {
	die ($e->getLine () . '. ' . $e->getMessage ());
}

///////////////////////////////////////////////////////

Bench::mark ('inserted 1000 people');

///// Fetch total people over 10 queries /////

$total = 0;
$queries = 0;
for ($i = 0; $i <= 900000; $i += 100000) {
	$stmt = $db->prepare ('select count(*) from people where score >= ? and score < ?');
	$stmt->execute (array ($i, $i + 100000));
	$res = $stmt->fetch (PDO::FETCH_NUM);
	$count = $res[0];

	$total += $count;
	$queries++;
}

//////////////////////////////////////////////

Bench::mark ('counted ' . $total . ' people in ' . $queries . ' queries');

///// Fetch top fifty people /////

$stmt = $db->prepare ('select * from people order by score desc limit 50');
$stmt->execute ();
$list = $stmt->fetchAll ();

//////////////////////////////////

Bench::mark ('fetched ' . count ($list) . ' people in 1 query');

///// Perform 50 updates /////

foreach ($list as $person) {
	$stmt = $db->prepare ('update people set score = ? where id = ?');
	$stmt->execute (array (
		$person['score'] + 1,
		$person['id']
	));
}

//////////////////////////////

Bench::mark ('performed ' . count ($list) . ' updates');

///// Perform 50 deletes /////

foreach ($list as $person) {
	$stmt = $db->prepare ('delete from people where id = ?');
	$stmt->execute (array ($person['id']));
}

//////////////////////////////

Bench::mark ('performed ' . count ($list) . ' deletes');

Bench::mark (true);

?>