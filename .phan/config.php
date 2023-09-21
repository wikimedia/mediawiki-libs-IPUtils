<?php
$cfg = require __DIR__ . '/../vendor/mediawiki/mediawiki-phan-config/src/config-library.php';

// https://phabricator.wikimedia.org/T325321
$cfg['plugins'] = [];

$cfg['directory_list'] = [
	'vendor/wikimedia/',
	'src/',
];

return $cfg;
