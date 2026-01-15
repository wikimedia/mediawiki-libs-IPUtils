<?php
// Benchmark for IPUtils::isInRanges().

require_once dirname( __DIR__ ) . '/../src/IPUtils.php';
require_once dirname( __DIR__ ) . '/../vendor/autoload.php';

use Wikimedia\IPUtils;

$softBlockRanges = require __DIR__ . '/data/softBlockRanges.php';

// Randomly-chosen IPs. To simulate the common case, they are not
// in any of the ranges.
$ipv6 = '2600:4041:42ad:b800:3ca0:8106:2c2d:e290';
$ipv4 = '71.172.245.55';

$iterations = 1_000;

$startTime = hrtime( true );
for ( $i = 0; $i < $iterations; $i += 2 ) {
	IPUtils::isInRanges( $ipv4, $softBlockRanges );
	IPUtils::isInRanges( $ipv6, $softBlockRanges );
}
$timeMs = ( hrtime( true ) - $startTime ) / 1_000_000 / $iterations;
echo sprintf( "%-45s %.3f ms (iterations=%d)\n", "IPUtils::isInRanges:", $timeMs, $iterations );
