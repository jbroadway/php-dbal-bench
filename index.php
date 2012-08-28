<?php

/**
 * This generates the launch screen for the tests.
 */

echo '<h1>php-dbal-bench</h1>';

echo '<ul>';

$files = glob ('*/*.php');

foreach ($files as $file) {
	if (strpos ($file, 'lib/') === 0) {
		continue;
	}

	printf (
		'<li><a href="%s" target="_blank">%s - %s</a> (<a href="src.php?lib=%s&file=%s" target="_blank">src</a>)</li>',
		$file,
		dirname ($file),
		basename ($file, '.php'),
		dirname ($file),
		basename ($file, '.php')
	);
}

echo '</ul>';

echo '<p><a href="http://github.com/jbroadway/php-dbal-bench" target="_blank">http://github.com/jbroadway/php-dbal-bench</a></p>';

?>