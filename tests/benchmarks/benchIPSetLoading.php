<?php
// Benchmark for loading and initialising IPSet from PHP array vs JSON.
//
// This compares loading IPSet with and without cached state against
// a large XFF dataset (not hand-edited but auto-generated
// and thus can provide both plain input and cached state).

require_once dirname( __DIR__ ) . '/../src/IPSet.php';
require_once dirname( __DIR__ ) . '/../vendor/autoload.php';

use Wikimedia\IPSet;

// Warmup, simulating opcache (for PHP files) or apcu (for json state)
$hostInput = require __DIR__ . '/data/trusted-hosts.php';
$hostExpandedState = file_get_contents( __DIR__ . '/data/trusted-hosts.json' );

$iterations = 1_000;

$startTime = hrtime( true );
for ( $i = 0; $i < $iterations; $i++ ) {
	$ipsetPHP = new IPSet( $hostInput );
}
$timeMs = ( hrtime( true ) - $startTime ) / 1_000_000 / $iterations;
echo sprintf( "%-45s %.3f ms (iterations=%d)\n", "load-parse-XFF (IPSet::__construct):", $timeMs, $iterations );

$startTime = hrtime( true );
for ( $i = 0; $i < $iterations; $i++ ) {
	$ipsetJSON = IPSet::newFromJson( $hostExpandedState );
}
$timeMs = ( hrtime( true ) - $startTime ) / 1_000_000 / $iterations;
echo sprintf( "%-45s %.3f ms (iterations=%d)\n", "load-unserialize-XFF (IPSet::newFromJson):", $timeMs, $iterations );
