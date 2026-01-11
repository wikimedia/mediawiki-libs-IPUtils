<?php
// Benchmark for IPUtils::isInRanges().

require_once dirname( __DIR__ ) . '/src/IPUtils.php';
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

use Wikimedia\IPUtils;

$softBlockRanges = require __DIR__ . '/data/softBlockRanges.php';

$iterations = 1000;

$startTime = hrtime( true );

// Randomly-chosen IPs. To simulate the common case, they are not
// in any of the ranges.
$ipv6 = '2600:4041:42ad:b800:3ca0:8106:2c2d:e290';
$ipv4 = '71.172.245.55';

for ( $i = 0; $i < $iterations; $i += 2 ) {
	IPUtils::isInRanges( $ipv4, $softBlockRanges );
	IPUtils::isInRanges( $ipv6, $softBlockRanges );
}

$endTime = hrtime( true );
$totalTimeMs = ( $endTime - $startTime ) / 1_000_000;
$avgTimeMs = $totalTimeMs / $iterations;

echo "IPUtils::isInRanges(): " . sprintf( '%.3f', $avgTimeMs ) . " ms\n";
