<?php
// Benchmark various ways to check if two IPs are equal.
// See <https://gerrit.wikimedia.org/r/c/mediawiki/libs/IPUtils/+/1208413>

require_once dirname( __DIR__ ) . '/src/IPUtils.php';
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

use Wikimedia\IPUtils;

$dataset = [
	'141.0.11.253' => '141.000.011.253',
	'141.000.011.253' => '141.0.11.253',
	'cebc:2004:f:0:0:0:0:0' => 'cebc:2004:f::',
	'cebc:2004:f::' => 'cebc:2004:f:0:0:0:0:0',
];
$cases = [
	[ 'prettifyIP', static fn ( $a, $b ) => IPUtils::prettifyIP( $a ) === IPUtils::prettifyIP( $b ) ],
	[ 'sanitizeIP', static fn ( $a, $b ) => IPUtils::sanitizeIP( $a ) === IPUtils::sanitizeIP( $b ) ],
	[ 'toHex', static fn ( $a, $b ) => IPUtils::toHex( $a ) === IPUtils::toHex( $b ) ],
	[ 'isInRange', static fn ( $a, $b ) => IPUtils::isInRange( $a, $b ) ],
];
shuffle( $cases );
$results = [];

foreach ( $cases as [ $label, $fn ] ) {
	$iterations = 10_000;
	$startTime = hrtime( true );
	for ( $i = 0; $i < $iterations; $i++ ) {
		foreach ( $dataset as $a => $b ) {
			$fn( $a, $b ) || die( "Error: Expected $label('$a', '$b') to return true\n" );
		}
	}
	$avgTimeMs = ( hrtime( true ) - $startTime ) / 1_000_000 / $iterations;
	$results[$label] = $avgTimeMs;
}
asort( $results );
foreach ( $results as $label => $avgTimeMs ) {
	print sprintf( '%-15s: %.3f', $label, $avgTimeMs ) . " ms\n";
}
