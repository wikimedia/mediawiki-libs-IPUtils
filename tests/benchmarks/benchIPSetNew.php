<?php
// Benchmark for IPSet::__construct.

require_once dirname( __DIR__ ) . '/../src/IPSet.php';
require_once dirname( __DIR__ ) . '/../vendor/autoload.php';

use Wikimedia\IPSet;

$cdnServers = require __DIR__ . '/data/cdnServers.php';

$iterations = 1_000;

// Small dataset, benchmark parse-only
//
// This resembles a dynamically configured dataset in MediaWiki that is regulary
// hand-edited without pregenerated JSON.
$startTime = hrtime( true );
for ( $i = 0; $i < $iterations; $i++ ) {
	new IPSet( $cdnServers );
}
$timeMs = ( hrtime( true ) - $startTime ) / 1_000_000 / $iterations;
echo sprintf( "%-45s %.3f ms (iterations=%d)\n", "load-parse-CDN (IPSet::__construct):", $timeMs, $iterations );
