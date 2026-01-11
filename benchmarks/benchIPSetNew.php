<?php
// Benchmark for IPSet::new().

require_once dirname( __DIR__ ) . '/src/IPSet.php';
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

use Wikimedia\IPSet;

$cdnServers = require __DIR__ . '/data/cdnServers.php';

$iterations = 10_000;

$startTime = hrtime( true );

for ( $i = 0; $i < $iterations; $i += 2 ) {
	new IPSet( $cdnServers );
}

$endTime = hrtime( true );
$totalTimeMs = ( $endTime - $startTime ) / 1_000_000;
$avgTimeMs = $totalTimeMs / $iterations;

echo "IPSet::new(): " . sprintf( '%.3f', $avgTimeMs ) . " ms\n";
