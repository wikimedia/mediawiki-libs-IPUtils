<?php
// Benchmark for loading and initialising IPSet from PHP array vs JSON.

require_once dirname( __DIR__ ) . '/src/IPSet.php';
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

use Wikimedia\IPSet;

// Warmup, simulating opcache (for PHP files) or apcu (for json state)
$hostInput = require __DIR__ . '/data/trusted-hosts.php';
$hostExpandedState = file_get_contents( __DIR__ . '/data/trusted-hosts.json' );

const ITERATIONS = 100;
echo "PHP " . PHP_VERSION . ", iterations=" . number_format( ITERATIONS ) . "\n\n";

$startTime = hrtime( true );
$i = ITERATIONS;
while ( $i-- ) {
	$ipsetPHP = new IPSet( $hostInput );
}
$timeMs = ( hrtime( true ) - $startTime ) / 1_000_000 / ITERATIONS;
echo sprintf( "%-35s %.3f ms\n", "parse (IPSet::__construct):", $timeMs );

$startTime = hrtime( true );
$i = ITERATIONS;
while ( $i-- ) {
	$ipsetJSON = IPSet::newFromJson( $hostExpandedState );
}
$timeMs = ( hrtime( true ) - $startTime ) / 1_000_000 / ITERATIONS;
echo sprintf( "%-35s %.3f ms\n", "unserialize (IPSet::newFromJson):", $timeMs );

echo "\n";
echo "Correctness for parse:\n";
checkIPs( $ipsetPHP );

echo "\n";
echo "Correctness for unserialize:\n";
checkIPs( $ipsetJSON );

function checkIPs( IPSet $ipset ): void {
	foreach ( [ '69.63.176.1' => true, '127.0.0.1' => false ] as $ip => $exists ) {
		if ( $ipset->match( $ip ) === $exists ) {
			echo "* IPSet returned correct result for $ip\n";
		}
	}
}
