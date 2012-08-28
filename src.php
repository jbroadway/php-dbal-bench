<?php

/**
 * Outputs the highlighted source code for a test.
 */

if (! isset ($_GET['lib']) || ! preg_match ('/^[a-z0-9_-]+$/i', $_GET['lib'])) {
	die ('Invalid lib');
}

if (! isset ($_GET['file']) || ! preg_match ('/^[a-z0-9_-]+$/i', $_GET['file'])) {
	die ('Invalid file');
}

highlight_file ($_GET['lib'] . '/' . $_GET['file'] . '.php');

?>