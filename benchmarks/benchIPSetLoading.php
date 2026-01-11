<?php
// Benchmark for loading and initialising IPSet from PHP array vs JSON.

require_once dirname( __DIR__ ) . '/src/IPSet.php';
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

use Wikimedia\IPSet;

$startTime = hrtime( true );
$ipsetPHP = new IPSet( require __DIR__ . '/data/trusted-hosts.php' );
$endTime = hrtime( true );
$timeMs = ( $endTime - $startTime ) / 1_000_000;
echo "Load from PHP array and initialise: " . sprintf( '%.3f', $timeMs ) . " ms\n";

checkIPs( $ipsetPHP );

echo "\n";

$startTime = hrtime( true );
$ipsetJSON = IPSet::newFromJson( file_get_contents( __DIR__ . '/data/trusted-hosts.json' ) );
$endTime = hrtime( true );
$timeMs = ( $endTime - $startTime ) / 1_000_000;
echo "Load from serialized json and initialise: " . sprintf( '%.3f', $timeMs ) . " ms\n";

checkIPs( $ipsetJSON );

function checkIPs( IPSet $ipset ): void {
	foreach ( [ '69.63.176.1' => true, '127.0.0.1' => false ] as $ip => $exists ) {
		if ( $ipset->match( $ip ) === $exists ) {
			echo "IPSet returned correct result for $ip\n";
		}
	}
}
