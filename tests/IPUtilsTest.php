<?php

namespace Wikimedia\IPUtils\Test;

use Wikimedia\IPUtils;

/**
 * Tests for IP validity functions.
 *
 * @todo Test methods in this call should be split into a method and a
 * dataprovider.
 */
class IPUtilsTest extends \PHPUnit\Framework\TestCase {

	/**
	 * @covers \Wikimedia\IPUtils::isIPAddress
	 * @dataProvider provideInvalidIPs
	 */
	public function testIsNotIPAddress( $val, $desc ) {
		$this->assertFalse( IPUtils::isIPAddress( $val ), $desc );
	}

	/**
	 * Provide a list of things that aren't IP addresses
	 */
	public function provideInvalidIPs() {
		return [
			[ false, 'Boolean false is not an IP' ],
			[ true, 'Boolean true is not an IP' ],
			[ '', 'Empty string is not an IP' ],
			[ 'abc', 'Garbage IP string' ],
			[ ':', 'Single ":" is not an IP' ],
			[ '2001:0DB8::A:1::1', 'IPv6 with a double :: occurrence' ],
			[ '2001:0DB8::A:1::', 'IPv6 with a double :: occurrence, last at end' ],
			[ '::2001:0DB8::5:1', 'IPv6 with a double :: occurrence, firt at beginning' ],
			[ '124.24.52', 'IPv4 not enough quads' ],
			[ '24.324.52.13', 'IPv4 out of range' ],
			[ '.24.52.13', 'IPv4 starts with period' ],
			[ 'fc:100:300', 'IPv6 with only 3 words' ],
		];
	}

	/**
	 * @covers \Wikimedia\IPUtils::isIPAddress
	 */
	public function testisIPAddress() {
		$this->assertTrue( IPUtils::isIPAddress( '::' ), 'RFC 4291 IPv6 Unspecified Address' );
		$this->assertTrue( IPUtils::isIPAddress( '::1' ), 'RFC 4291 IPv6 Loopback Address' );
		$this->assertTrue( IPUtils::isIPAddress( '74.24.52.13/20' ), 'IPv4 range' );
		$this->assertTrue( IPUtils::isIPAddress( 'fc:100:a:d:1:e:ac:0/24' ), 'IPv6 range' );
		$this->assertTrue( IPUtils::isIPAddress( 'fc::100:a:d:1:e:ac/96' ), 'IPv6 range with "::"' );

		$validIPs = [ 'fc:100::', 'fc:100:a:d:1:e:ac::', 'fc::100', '::fc:100:a:d:1:e:ac',
			'::fc', 'fc::100:a:d:1:e:ac', 'fc:100:a:d:1:e:ac:0', '124.24.52.13', '1.24.52.13' ];
		foreach ( $validIPs as $ip ) {
			$this->assertTrue( IPUtils::isIPAddress( $ip ), "$ip is a valid IP address" );
		}
	}

	/**
	 * @covers \Wikimedia\IPUtils::isIPv6
	 */
	public function testisIPv6() {
		$this->assertFalse( IPUtils::isIPv6( ':fc:100::' ), 'IPv6 starting with lone ":"' );
		$this->assertFalse( IPUtils::isIPv6( 'fc:100:::' ), 'IPv6 ending with a ":::"' );
		$this->assertFalse( IPUtils::isIPv6( 'fc:300' ), 'IPv6 with only 2 words' );
		$this->assertFalse( IPUtils::isIPv6( 'fc:100:300' ), 'IPv6 with only 3 words' );

		$this->assertTrue( IPUtils::isIPv6( 'fc:100::' ) );
		$this->assertTrue( IPUtils::isIPv6( 'fc:100:a::' ) );
		$this->assertTrue( IPUtils::isIPv6( 'fc:100:a:d::' ) );
		$this->assertTrue( IPUtils::isIPv6( 'fc:100:a:d:1::' ) );
		$this->assertTrue( IPUtils::isIPv6( 'fc:100:a:d:1:e::' ) );
		$this->assertTrue( IPUtils::isIPv6( 'fc:100:a:d:1:e:ac::' ) );

		$this->assertFalse( IPUtils::isIPv6( 'fc:100:a:d:1:e:ac:0::' ), 'IPv6 with 8 words ending with "::"' );
		$this->assertFalse(
			IPUtils::isIPv6( 'fc:100:a:d:1:e:ac:0:1::' ),
			'IPv6 with 9 words ending with "::"'
		);

		$this->assertFalse( IPUtils::isIPv6( ':::' ) );
		$this->assertFalse( IPUtils::isIPv6( '::0:' ), 'IPv6 ending in a lone ":"' );

		$this->assertTrue( IPUtils::isIPv6( '::' ), 'IPv6 zero address' );
		$this->assertTrue( IPUtils::isIPv6( '::0' ) );
		$this->assertTrue( IPUtils::isIPv6( '::fc' ) );
		$this->assertTrue( IPUtils::isIPv6( '::fc:100' ) );
		$this->assertTrue( IPUtils::isIPv6( '::fc:100:a' ) );
		$this->assertTrue( IPUtils::isIPv6( '::fc:100:a:d' ) );
		$this->assertTrue( IPUtils::isIPv6( '::fc:100:a:d:1' ) );
		$this->assertTrue( IPUtils::isIPv6( '::fc:100:a:d:1:e' ) );
		$this->assertTrue( IPUtils::isIPv6( '::fc:100:a:d:1:e:ac' ) );

		$this->assertFalse( IPUtils::isIPv6( '::fc:100:a:d:1:e:ac:0' ), 'IPv6 with "::" and 8 words' );
		$this->assertFalse( IPUtils::isIPv6( '::fc:100:a:d:1:e:ac:0:1' ), 'IPv6 with 9 words' );

		$this->assertFalse( IPUtils::isIPv6( ':fc::100' ), 'IPv6 starting with lone ":"' );
		$this->assertFalse( IPUtils::isIPv6( 'fc::100:' ), 'IPv6 ending with lone ":"' );
		$this->assertFalse( IPUtils::isIPv6( 'fc:::100' ), 'IPv6 with ":::" in the middle' );

		$this->assertTrue( IPUtils::isIPv6( 'fc::100' ), 'IPv6 with "::" and 2 words' );
		$this->assertTrue( IPUtils::isIPv6( 'fc::100:a' ), 'IPv6 with "::" and 3 words' );
		$this->assertTrue( IPUtils::isIPv6( 'fc::100:a:d' ), 'IPv6 with "::" and 4 words' );
		$this->assertTrue( IPUtils::isIPv6( 'fc::100:a:d:1' ), 'IPv6 with "::" and 5 words' );
		$this->assertTrue( IPUtils::isIPv6( 'fc::100:a:d:1:e' ), 'IPv6 with "::" and 6 words' );
		$this->assertTrue( IPUtils::isIPv6( 'fc::100:a:d:1:e:ac' ), 'IPv6 with "::" and 7 words' );
		$this->assertTrue( IPUtils::isIPv6( '2001::df' ), 'IPv6 with "::" and 2 words' );
		$this->assertTrue( IPUtils::isIPv6( '2001:5c0:1400:a::df' ), 'IPv6 with "::" and 5 words' );
		$this->assertTrue( IPUtils::isIPv6( '2001:5c0:1400:a::df:2' ), 'IPv6 with "::" and 6 words' );

		$this->assertFalse( IPUtils::isIPv6( 'fc::100:a:d:1:e:ac:0' ), 'IPv6 with "::" and 8 words' );
		$this->assertFalse( IPUtils::isIPv6( 'fc::100:a:d:1:e:ac:0:1' ), 'IPv6 with 9 words' );

		$this->assertTrue( IPUtils::isIPv6( 'fc:100:a:d:1:e:ac:0' ) );
	}

	/**
	 * @covers \Wikimedia\IPUtils::isIPv4
	 * @dataProvider provideInvalidIPv4Addresses
	 */
	public function testisNotIPv4( $bogusIP, $desc ) {
		$this->assertFalse( IPUtils::isIPv4( $bogusIP ), $desc );
	}

	public function provideInvalidIPv4Addresses() {
		return [
			[ false, 'Boolean false is not an IP' ],
			[ true, 'Boolean true is not an IP' ],
			[ '', 'Empty string is not an IP' ],
			[ 'abc', 'Letters are not an IP' ],
			[ ':', 'A colon is not an IP' ],
			[ '124.24.52', 'IPv4 not enough quads' ],
			[ '24.324.52.13', 'IPv4 out of range' ],
			[ '.24.52.13', 'IPv4 starts with period' ],
		];
	}

	/**
	 * @covers \Wikimedia\IPUtils::isIPv4
	 * @dataProvider provideValidIPv4Address
	 */
	public function testIsIPv4( $ip, $desc ) {
		$this->assertTrue( IPUtils::isIPv4( $ip ), $desc );
	}

	/**
	 * Provide some IPv4 addresses and ranges
	 */
	public function provideValidIPv4Address() {
		return [
			[ '124.24.52.13', 'Valid IPv4 address' ],
			[ '1.24.52.13', 'Another valid IPv4 address' ],
			[ '74.24.52.13/20', 'An IPv4 range' ],
		];
	}

	/**
	 * @covers \Wikimedia\IPUtils::isValid
	 * @covers \Wikimedia\IPUtils::isValidIPv4
	 * @covers \Wikimedia\IPUtils::isValidIPv6
	 */
	public function testValidIPs() {
		foreach ( range( 0, 255 ) as $i ) {
			$a = sprintf( "%03d", $i );
			$b = sprintf( "%02d", $i );
			$c = sprintf( "%01d", $i );
			foreach ( array_unique( [ $a, $b, $c ] ) as $f ) {
				$ip = "$f.$f.$f.$f";
				$this->assertTrue( IPUtils::isValid( $ip ), "$ip is a valid IPv4 address" );
			}
		}
		foreach ( range( 0x0, 0xFFFF, 0xF ) as $i ) {
			$a = sprintf( "%04x", $i );
			$b = sprintf( "%03x", $i );
			$c = sprintf( "%02x", $i );
			foreach ( array_unique( [ $a, $b, $c ] ) as $f ) {
				$ip = "$f:$f:$f:$f:$f:$f:$f:$f";
				$this->assertTrue( IPUtils::isValid( $ip ), "$ip is a valid IPv6 address" );
			}
		}
		// test with some abbreviations
		$this->assertFalse( IPUtils::isValid( ':fc:100::' ), 'IPv6 starting with lone ":"' );
		$this->assertFalse( IPUtils::isValid( 'fc:100:::' ), 'IPv6 ending with a ":::"' );
		$this->assertFalse( IPUtils::isValid( 'fc:300' ), 'IPv6 with only 2 words' );
		$this->assertFalse( IPUtils::isValid( 'fc:100:300' ), 'IPv6 with only 3 words' );

		$this->assertTrue( IPUtils::isValid( 'fc:100::' ) );
		$this->assertTrue( IPUtils::isValid( 'fc:100:a:d:1:e::' ) );
		$this->assertTrue( IPUtils::isValid( 'fc:100:a:d:1:e:ac::' ) );

		$this->assertTrue( IPUtils::isValid( 'fc::100' ), 'IPv6 with "::" and 2 words' );
		$this->assertTrue( IPUtils::isValid( 'fc::100:a' ), 'IPv6 with "::" and 3 words' );
		$this->assertTrue( IPUtils::isValid( '2001::df' ), 'IPv6 with "::" and 2 words' );
		$this->assertTrue( IPUtils::isValid( '2001:5c0:1400:a::df' ), 'IPv6 with "::" and 5 words' );
		$this->assertTrue( IPUtils::isValid( '2001:5c0:1400:a::df:2' ), 'IPv6 with "::" and 6 words' );
		$this->assertTrue( IPUtils::isValid( 'fc::100:a:d:1' ), 'IPv6 with "::" and 5 words' );
		$this->assertTrue( IPUtils::isValid( 'fc::100:a:d:1:e:ac' ), 'IPv6 with "::" and 7 words' );

		$this->assertFalse(
			IPUtils::isValid( 'fc:100:a:d:1:e:ac:0::' ),
			'IPv6 with 8 words ending with "::"'
		);
		$this->assertFalse(
			IPUtils::isValid( 'fc:100:a:d:1:e:ac:0:1::' ),
			'IPv6 with 9 words ending with "::"'
		);
	}

	/**
	 * @covers \Wikimedia\IPUtils::isValid
	 */
	public function testInvalidIPs() {
		// Out of range...
		foreach ( range( 256, 999 ) as $i ) {
			$a = sprintf( "%03d", $i );
			$b = sprintf( "%02d", $i );
			$c = sprintf( "%01d", $i );
			foreach ( array_unique( [ $a, $b, $c ] ) as $f ) {
				$ip = "$f.$f.$f.$f";
				$this->assertFalse( IPUtils::isValid( $ip ), "$ip is not a valid IPv4 address" );
			}
		}
		foreach ( range( 'g', 'z' ) as $i ) {
			$a = sprintf( "%04s", $i );
			$b = sprintf( "%03s", $i );
			$c = sprintf( "%02s", $i );
			foreach ( array_unique( [ $a, $b, $c ] ) as $f ) {
				$ip = "$f:$f:$f:$f:$f:$f:$f:$f";
				$this->assertFalse( IPUtils::isValid( $ip ), "$ip is not a valid IPv6 address" );
			}
		}
		// Have CIDR
		$ipCIDRs = [
			'212.35.31.121/32',
			'212.35.31.121/18',
			'212.35.31.121/24',
			'::ff:d:321:5/96',
			'ff::d3:321:5/116',
			'c:ff:12:1:ea:d:321:5/120',
		];
		foreach ( $ipCIDRs as $i ) {
			$this->assertFalse( IPUtils::isValid( $i ),
				"$i is an invalid IP address because it is a range" );
		}
		// Incomplete/garbage
		$invalid = [
			'www.xn--var-xla.net',
			'216.17.184.G',
			'216.17.184.1.',
			'216.17.184',
			'216.17.184.',
			'256.17.184.1'
		];
		foreach ( $invalid as $i ) {
			$this->assertFalse( IPUtils::isValid( $i ), "$i is an invalid IP address" );
		}
	}

	/**
	 * Provide some valid IP ranges
	 */
	public function provideValidRanges() {
		return [
			[ '116.17.184.5/32' ],
			[ '116.17.184.5-116.17.184.5' ],
			[ '0.17.184.5/30' ],
			[ '0.17.184.4-0.17.184.7' ],
			[ '16.17.184.1/24' ],
			[ '16.17.184.0-16.17.184.255' ],
			[ '30.242.52.14/1' ],
			[ '0.0.0.0-127.255.255.255' ],
			[ '10.232.52.13/8' ],
			[ '10.0.0.0-10.255.255.255' ],
			[ '::e:f:2001/96' ],
			[ '0:0:0:0:0:e:0:0-0:0:0:0:0:e:ffff:ffff' ],
			[ '::c:f:2001/128' ],
			[ '0:0:0:0:0:c:f:2001-0:0:0:0:0:c:f:2001' ],
			[ '::10:f:2001/70' ],
			[ '0:0:0:0:0:0:0:0-0:0:0:0:3ff:ffff:ffff:ffff' ],
			[ '::fe:f:2001/1' ],
			[ '0:0:0:0:0:0:0:0-7fff:ffff:ffff:ffff:ffff:ffff:ffff:ffff' ],
			[ '::6d:f:2001/8' ],
			[ '0:0:0:0:0:0:0:0-ff:ffff:ffff:ffff:ffff:ffff:ffff:ffff' ],
		];
	}

	/**
	 * @covers \Wikimedia\IPUtils::isValidRange
	 * @covers \Wikimedia\IPUtils::isValidIPv4Range
	 * @covers \Wikimedia\IPUtils::isValidIPv6Range
	 * @dataProvider provideValidRanges
	 */
	public function testValidRanges( $range ) {
		$this->assertTrue( IPUtils::isValidRange( $range ), "$range is a valid IP range" );
	}

	/**
	 * @covers \Wikimedia\IPUtils::isValidRange
	 * @dataProvider provideInvalidRanges
	 */
	public function testInvalidRanges( $invalid ) {
		$this->assertFalse( IPUtils::isValidRange( $invalid ), "$invalid is not a valid IP range" );
	}

	public function provideInvalidRanges() {
		return [
			[ '116.17.184.5/33' ],
			[ '0.17.184.5/130' ],
			[ '16.17.184.1/-1' ],
			[ '10.232.52.13/*' ],
			[ '7.232.52.13/ab' ],
			[ '11.232.52.13/' ],
			[ '30.242.52.14/0' ],
			[ '::e:f:2001/129' ],
			[ '::c:f:2001/228' ],
			[ '::10:f:2001/-1' ],
			[ '::6d:f:2001/*' ],
			[ '::86:f:2001/ab' ],
			[ '::23:f:2001/' ],
			[ '::fe:f:2001/0' ],
		];
	}

	/**
	 * @covers \Wikimedia\IPUtils::sanitizeIP
	 * @dataProvider provideSanitizeIP
	 */
	public function testSanitizeIP( $expected, $input ) {
		$result = IPUtils::sanitizeIP( $input );
		$this->assertEquals( $expected, $result );
	}

	/**
	 * Provider for IPUtilsTest::testSanitizeIP()
	 */
	public static function provideSanitizeIP() {
		return [
			[ '0.0.0.0', '0.0.0.0' ],
			[ '0.0.0.0', '00.00.00.00' ],
			[ '0.0.0.0', '000.000.000.000' ],
			[ '0.0.0.0/24', '000.000.000.000/24' ],
			[ '141.0.11.253', '141.000.011.253' ],
			[ '1.2.4.5', '1.2.4.5' ],
			[ '1.2.4.5', '01.02.04.05' ],
			[ '1.2.4.5', '001.002.004.005' ],
			[ '10.0.0.1', '010.0.000.1' ],
			[ '80.72.250.4', '080.072.250.04' ],
			[ 'Foo.1000.00', 'Foo.1000.00' ],
			[ 'Bar.01', 'Bar.01' ],
			[ 'Bar.010', 'Bar.010' ],
			[ '0:0:0:0:0:10:F:2001', '::10:f:2001' ],
			[ '0:0:0:0:0:10:F:2001/70', '::10:f:2001/70' ],
			[ '2001:DB8:85A3:0:0:8A2E:370:7334', '2001:db8:85a3::8a2e:370:7334' ],
			[ '2001:DB8:85A3:8A2E:370:7334:0:0', '2001:db8:85a3:8a2e:370:7334::' ],
			[ null, '' ],
			[ null, ' ' ],
		];
	}

	/**
	 * @covers \Wikimedia\IPUtils::toHex
	 * @covers \Wikimedia\IPUtils::IPv6ToRawHex
	 * @dataProvider provideToHex
	 */
	public function testToHex( $expected, $input ) {
		$result = IPUtils::toHex( $input );
		$this->assertTrue( $result === false || is_string( $result ) );
		$this->assertEquals( $expected, $result );
	}

	/**
	 * Provider for IPUtils::testToHex()
	 */
	public static function provideToHex() {
		return [
			[ '00000001', '0.0.0.1' ],
			[ '01020304', '1.2.3.4' ],
			[ '7F000001', '127.0.0.1' ],
			[ '80000000', '128.0.0.0' ],
			[ 'DEADCAFE', '222.173.202.254' ],
			[ 'FFFFFFFF', '255.255.255.255' ],
			[ '8D000BFD', '141.000.11.253' ],
			[ false, 'IN.VA.LI.D' ],
			[ 'v6-00000000000000000000000000000001', '::1' ],
			[ 'v6-20010DB885A3000000008A2E03707334', '2001:0db8:85a3:0000:0000:8a2e:0370:7334' ],
			[ 'v6-20010DB885A3000000008A2E03707334', '2001:db8:85a3::8a2e:0370:7334' ],
			[ false, 'IN:VA::LI:D' ],
			[ false, ':::1' ],
		];
	}

	/**
	 * @covers \Wikimedia\IPUtils::isPublic
	 * @dataProvider provideIsPublic
	 */
	public function testIsPublic( $expected, $input ) {
		$result = IPUtils::isPublic( $input );
		$this->assertEquals( $expected, $result );
	}

	/**
	 * Provider for IPUtilsTest::testIsPublic()
	 */
	public static function provideIsPublic() {
		return [
			// RFC 4193 (local)
			[ false, 'fc00::3' ],
			// RFC 4193 (local)
			[ false, 'fc00::ff' ],
			// loopback
			[ false, '127.1.2.3' ],
			// loopback
			[ false, '::1' ],
			// link-local
			[ false, 'fe80::1' ],
			// link-local
			[ false, '169.254.1.1' ],
			// RFC 1918 (private)
			[ false, '10.0.0.1' ],
			// RFC 1918 (private)
			[ false, '172.16.0.1' ],
			// RFC 1918 (private)
			[ false, '192.168.0.1' ],
			// public
			[ true, '2001:5c0:1000:a::133' ],
			// public
			[ true, 'fc::3' ],
			// public
			[ true, '00FC::' ],
		];
	}

	// Private wrapper used to test CIDR Parsing.
	private function assertFalseCIDR( $CIDR, $msg = '' ) {
		$ff = [ false, false ];
		$this->assertEquals( $ff, IPUtils::parseCIDR( $CIDR ), $msg );
	}

	// Private wrapper to test network shifting using only dot notation
	private function assertNet( $expected, $CIDR ) {
		$parse = IPUtils::parseCIDR( $CIDR );
		$this->assertEquals( $expected, long2ip( $parse[0] ), "network shifting $CIDR" );
	}

	/**
	 * @covers \Wikimedia\IPUtils::formatHex
	 * @covers \Wikimedia\IPUtils::hexToOctet
	 * @covers \Wikimedia\IPUtils::hexToQuad
	 * @dataProvider provideOctetsAndHexes
	 */
	public function testHexToOctet( $octet, $hex ) {
		$this->assertEquals( $octet, IPUtils::formatHex( $hex ) );
	}

	/**
	 * Provide some hex and octet representations of the same IPs
	 */
	public function provideOctetsAndHexes() {
		return [
			// IPv4
			[ '0.0.0.1', '00000001' ],
			[ '255.0.0.0', 'FF000000' ],
			[ '255.255.255.255', 'FFFFFFFF' ],
			[ '10.188.222.255', '0ABCDEFF' ],
			// hex not left-padded...
			[ '0.0.0.0', '0' ],
			[ '0.0.0.1', '1' ],
			[ '0.0.0.255', 'FF' ],
			[ '0.0.255.0', 'FF00' ],

			// IPv6
			[ '0:0:0:0:0:0:0:1', 'v6-00000000000000000000000000000001' ],
			[ '0:0:0:0:0:0:FF:3', 'v6-00000000000000000000000000FF0003' ],
			[ '0:0:0:0:0:0:FF00:6', 'v6-000000000000000000000000FF000006' ],
			[ '0:0:0:0:0:0:FCCF:FAFF', 'v6-000000000000000000000000FCCFFAFF' ],
			[ 'FFFF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF', 'v6-FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF' ],
			// hex not left-padded...
			[ '0:0:0:0:0:0:0:0', 'v6-0' ],
			[ '0:0:0:0:0:0:0:1', 'v6-1' ],
			[ '0:0:0:0:0:0:0:FF', 'v6-FF' ],
			[ '0:0:0:0:0:0:0:FFD0', 'v6-FFD0' ],
			[ '0:0:0:0:0:0:FA00:0', 'v6-FA000000' ],
			[ '0:0:0:0:0:0:FCCF:FAFF', 'v6-FCCFFAFF' ],
		];
	}

	/**
	 * IPUtils::parseCIDR() returns an array containing a signed IP address
	 * representing the network mask and the bit mask.
	 * @covers \Wikimedia\IPUtils::parseCIDR
	 */
	public function testCIDRParsing() {
		$this->assertFalseCIDR( '192.0.2.0', "missing mask" );
		$this->assertFalseCIDR( '192.0.2.0/', "missing bitmask" );

		// Verify if statement
		$this->assertFalseCIDR( '256.0.0.0/32', "invalid net" );
		$this->assertFalseCIDR( '192.0.2.0/AA', "mask not numeric" );
		$this->assertFalseCIDR( '192.0.2.0/-1', "mask < 0" );
		$this->assertFalseCIDR( '192.0.2.0/33', "mask > 32" );

		// Check internal logic
		// 0 mask always result in [ 0, 0 ]
		$this->assertEquals( [ 0, 0 ], IPUtils::parseCIDR( '192.0.0.2/-' ) );
		$this->assertEquals( [ 0, 0 ], IPUtils::parseCIDR( '0.0.0.0/0' ) );
		$this->assertEquals( [ 0, 0 ], IPUtils::parseCIDR( '255.255.255.255/0' ) );

		// @todo FIXME: Add more tests.

		// This part test network shifting
		$this->assertNet( '192.0.0.0', '192.0.0.2/24' );
		$this->assertNet( '192.168.5.0', '192.168.5.13/24' );
		$this->assertNet( '10.0.0.160', '10.0.0.161/28' );
		$this->assertNet( '10.0.0.0', '10.0.0.3/28' );
		$this->assertNet( '10.0.0.0', '10.0.0.3/30' );
		$this->assertNet( '10.0.0.4', '10.0.0.4/30' );
		$this->assertNet( '172.17.32.0', '172.17.35.48/21' );
		$this->assertNet( '10.128.0.0', '10.135.0.0/9' );
		$this->assertNet( '134.0.0.0', '134.0.5.1/8' );

		// Test the IPv6 offloading to parseCIDR6()
		$this->assertEquals( [ "51540598785", 128 ], IPUtils::parseCIDR( '::c:f:2001/128' ) );
	}

	/**
	 * @covers \Wikimedia\IPUtils::canonicalize
	 */
	public function testCanonicalize() {
		$this->assertEquals(
			'192.0.2.152',
			IPUtils::canonicalize( '192.0.2.152' ),
			'Canonicalization of a valid IPv4 address returns it unchanged'
		);

		// Example IP from https://en.wikipedia.org/wiki/IPv6#IPv4-mapped_IPv6_addresses
		$this->assertEquals(
			'192.0.2.128',
			IPUtils::canonicalize( '::FFFF:192.0.2.128' ),
			'Canonicalization of IPv4-mapped addresses'
		);

		// Example IP from https://en.wikipedia.org/wiki/IPv6#IPv4-mapped_IPv6_addresses
		$this->assertEquals(
			'192.0.2.128',
			IPUtils::canonicalize( '::192.0.2.128' ),
			'Canonicalization of IPv4-compatible IPv6 addresses'
		);

		$this->assertEquals(
			'255.255.0.31',
			IPUtils::canonicalize( ':ffff:1F' )
		);

		$this->assertEquals(
			'2001:db8:85a3::8a2e:370:7334',
			IPUtils::canonicalize( '2001:db8:85a3::8a2e:370:7334' ),
			'Canonicalization of a valid IPv6 address returns it unchanged'
		);

		// Valid IPv6 address comes out again.
		// Confirm that ::1 isn't canonicalized into 127.0.0.1 as was done previously
		// https://phabricator.wikimedia.org/T248237
		$this->assertEquals(
			'::1',
			IPUtils::canonicalize( '::1' ),
			'IPv6 loopback address not converted to an IPv4 loopback address as occured in previous versions'
		);

		$this->assertNull(
			IPUtils::canonicalize( '' ),
			'Canonicalization of an invalid IP returns null'
		);
	}

	/**
	 * @covers \Wikimedia\IPUtils::canonicalize
	 */
	public function testIncorrectCanonicalize() {
		$this->markTestSkipped( 'Broken' );

		// This shouldn't be canonicalized as it is not a valid IP address, but is currently
		$this->assertNull(
			IPUtils::canonicalize( '::::!*@#&:127.0.0.1' ),
			'Invalid IPv4 mapped address that is incorrectly canonicalized'
		);
	}

	/**
	 * Issues there are most probably from IPUtils::toHex() or IPUtils::parseRange()
	 * @covers \Wikimedia\IPUtils::isInRange
	 * @covers \Wikimedia\IPUtils::isInRanges
	 * @dataProvider provideIPsAndRanges
	 */
	public function testIPIsInRanges( $expected, $addr, $ranges, $message = '' ) {
		$this->assertEquals(
			$expected,
			IPUtils::isInRanges( $addr, $ranges ),
			$message
		);
	}

	/**
	 * Provider for IPUtilsTest::testIPIsInRanges()
	 */
	public static function provideIPsAndRanges() {
		// Format: (expected boolean, address, ranges, optional message)
		return [
			// IPv4
			[ true, '192.0.2.0', [ '192.0.2.0/24' ], 'Network address' ],
			[ true, '192.0.2.77', [ '192.0.2.0/24' ], 'Simple address' ],
			[ true, '192.0.2.255', [ '192.0.2.0/24' ], 'Broadcast address' ],

			[ false, '0.0.0.0', [ '192.0.2.0/24' ] ],
			[ false, '255.255.255', [ '192.0.2.0/24' ] ],

			// IPv6
			[ false, '::1', [ '2001:DB8::/32' ] ],
			[ false, '::', [ '2001:DB8::/32' ] ],
			[ false, 'FE80::1', [ '2001:DB8::/32' ] ],

			[ true, '2001:DB8::', [ '2001:DB8::/32' ] ],
			[ true, '2001:0DB8::', [ '2001:DB8::/32' ] ],
			[ true, '2001:DB8::1', [ '2001:DB8::/32' ] ],
			[ true, '2001:0DB8::1', [ '2001:DB8::/32' ] ],
			[ true, '2001:0DB8:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF',
				[ '2001:DB8::/32' ] ],

			[ false, '2001:0DB8:F::', [ '2001:DB8::/96' ] ],
		];
	}

	/**
	 * @covers \Wikimedia\IPUtils::splitHostAndPort()
	 * @dataProvider provideSplitHostAndPort
	 */
	public function testSplitHostAndPort( $expected, $input, $description ) {
		$this->assertEquals( $expected, IPUtils::splitHostAndPort( $input ), $description );
	}

	/**
	 * Provider for IPUtilsTest::splitHostAndPort()
	 */
	public static function provideSplitHostAndPort() {
		return [
			[ false, '[', 'Unclosed square bracket' ],
			[ false, '[::', 'Unclosed square bracket 2' ],
			[ [ '::', false ], '::', 'Bare IPv6 0' ],
			[ [ '::1', false ], '::1', 'Bare IPv6 1' ],
			[ [ '::', false ], '[::]', 'Bracketed IPv6 0' ],
			[ [ '::1', false ], '[::1]', 'Bracketed IPv6 1' ],
			[ [ '::1', 80 ], '[::1]:80', 'Bracketed IPv6 with port' ],
			[ false, '::x', 'Double colon but no IPv6' ],
			[ [ 'x', 80 ], 'x:80', 'Hostname and port' ],
			[ false, 'x:x', 'Hostname and invalid port' ],
			[ [ 'x', false ], 'x', 'Plain hostname' ]
		];
	}

	/**
	 * @covers \Wikimedia\IPUtils::combineHostAndPort()
	 * @dataProvider provideCombineHostAndPort
	 */
	public function testCombineHostAndPort( $expected, $input, $description ) {
		list( $host, $port, $defaultPort ) = $input;
		$this->assertEquals(
			$expected,
			IPUtils::combineHostAndPort( $host, $port, $defaultPort ),
			$description );
	}

	/**
	 * Provider for IPUtilsTest::combineHostAndPort()
	 */
	public static function provideCombineHostAndPort() {
		return [
			[ '[::1]', [ '::1', 2, 2 ], 'IPv6 default port' ],
			[ '[::1]:2', [ '::1', 2, 3 ], 'IPv6 non-default port' ],
			[ 'x', [ 'x', 2, 2 ], 'Normal default port' ],
			[ 'x:2', [ 'x', 2, 3 ], 'Normal non-default port' ],
		];
	}

	/**
	 * @covers \Wikimedia\IPUtils::sanitizeRange()
	 * @dataProvider provideIPCIDRs
	 */
	public function testSanitizeRange( $input, $expected, $description ) {
		$this->assertEquals( $expected, IPUtils::sanitizeRange( $input ), $description );
	}

	/**
	 * Provider for IPUtilsTest::testSanitizeRange()
	 */
	public static function provideIPCIDRs() {
		return [
			[ '35.56.31.252/16', '35.56.0.0/16', 'IPv4 range' ],
			[ '135.16.21.252/24', '135.16.21.0/24', 'IPv4 range' ],
			[ '5.36.71.252/32', '5.36.71.252/32', 'IPv4 silly range' ],
			[ '5.36.71.252', '5.36.71.252', 'IPv4 non-range' ],
			[ '0:1:2:3:4:c5:f6:7/96', '0:1:2:3:4:C5:0:0/96', 'IPv6 range' ],
			[ '0:1:2:3:4:5:6:7/120', '0:1:2:3:4:5:6:0/120', 'IPv6 range' ],
			[ '0:e1:2:3:4:5:e6:7/128', '0:E1:2:3:4:5:E6:7/128', 'IPv6 silly range' ],
			[ '0:c1:A2:3:4:5:c6:7', '0:C1:A2:3:4:5:C6:7', 'IPv6 non range' ],
		];
	}

	/**
	 * @covers \Wikimedia\IPUtils::prettifyIP()
	 * @dataProvider provideIPsToPrettify
	 */
	public function testPrettifyIP( $ip, $prettified ) {
		$this->assertEquals( $prettified, IPUtils::prettifyIP( $ip ), "Prettify of $ip" );
	}

	/**
	 * Provider for IPUtilsTest::testPrettifyIP()
	 */
	public static function provideIPsToPrettify() {
		return [
			[ '0:0:0:0:0:0:0:0', '::' ],
			[ '0:0:0::0:0:0', '::' ],
			[ '0:0:0:1:0:0:0:0', '0:0:0:1::' ],
			[ '0:0::f', '::f' ],
			[ '0::0:0:0:33:fef:b', '::33:fef:b' ],
			[ '3f:535:0:0:0:0:e:fbb', '3f:535::e:fbb' ],
			[ '0:0:fef:0:0:0:e:fbb', '0:0:fef::e:fbb' ],
			[ 'abbc:2004::0:0:0:0', 'abbc:2004::' ],
			[ 'cebc:2004:f:0:0:0:0:0', 'cebc:2004:f::' ],
			[ '0:0:0:0:0:0:0:0/16', '::/16' ],
			[ '0:0:0::0:0:0/64', '::/64' ],
			[ '0:0::f/52', '::f/52' ],
			[ '::0:0:33:fef:b/52', '::33:fef:b/52' ],
			[ '3f:535:0:0:0:0:e:fbb/48', '3f:535::e:fbb/48' ],
			[ '0:0:fef:0:0:0:e:fbb/96', '0:0:fef::e:fbb/96' ],
			[ 'abbc:2004:0:0::0:0/40', 'abbc:2004::/40' ],
			[ 'aebc:2004:f:0:0:0:0:0/80', 'aebc:2004:f::/80' ],
			[ '', null ],
		];
	}

	/**
	 * @covers \Wikimedia\IPUtils::parseRange()
	 * @covers \Wikimedia\IPUtils::parseRange6()
	 * @covers \Wikimedia\IPUtils::parseCIDR()
	 * @covers \Wikimedia\IPUtils::parseCIDR6()
	 * @dataProvider provideIPsToConvertToRanges
	 */
	public function testParseRange( $range, $hexRange ) {
		$this->assertEquals( $hexRange, IPUtils::parseRange( $range ), "Range parsing" );
	}

	/**
	 * Provider for IPUtilsTest::testParseRange()
	 */
	public function provideIPsToConvertToRanges() {
		return [
			[ '116.17.184.5/32', [ '7411B805', '7411B805' ] ],
			[ '116.17.184.5-116.17.184.5', [ '7411B805', '7411B805' ] ],
			[ '0.17.184.5/30', [ '0011B804', '0011B807' ] ],
			[ '0.17.184.4-0.17.184.7', [ '0011B804', '0011B807' ] ],
			[ '16.17.184.1/24', [ '1011B800', '1011B8FF' ] ],
			[ '16.17.184.0-16.17.184.255', [ '1011B800', '1011B8FF' ] ],
			[ '30.242.52.14/0', [ '00000000', 'FFFFFFFF' ] ],
			[ '30.242.52.14/1', [ '00000000', '7FFFFFFF' ] ],
			[ '0.0.0.0-127.255.255.255', [ '00000000', '7FFFFFFF' ] ],
			[ '10.232.52.13/8', [ '0A000000', '0AFFFFFF' ] ],
			[ '10.0.0.0-10.255.255.255', [ '0A000000', '0AFFFFFF' ] ],
			[ '::e:f:2001/96', [ 'v6-00000000000000000000000E00000000', 'v6-00000000000000000000000EFFFFFFFF' ] ],
			[ '0:0:0:0:0:e:0:0-0:0:0:0:0:e:ffff:ffff', [ 'v6-00000000000000000000000E00000000', 'v6-00000000000000000000000EFFFFFFFF' ] ],
			[ '::c:f:2001/128', [ 'v6-00000000000000000000000C000F2001', 'v6-00000000000000000000000C000F2001' ] ],
			[ '0:0:0:0:0:c:f:2001-0:0:0:0:0:c:f:2001', [ 'v6-00000000000000000000000C000F2001', 'v6-00000000000000000000000C000F2001' ] ],
			[ '::10:f:2001/70', [ 'v6-00000000000000000000000000000000', 'v6-000000000000000003FFFFFFFFFFFFFF' ] ],
			[ '0:0:0:0:0:0:0:0-0:0:0:0:3ff:ffff:ffff:ffff', [ 'v6-00000000000000000000000000000000', 'v6-000000000000000003FFFFFFFFFFFFFF' ] ],
			[ '::fe:f:2001/1', [ 'v6-00000000000000000000000000000000', 'v6-7FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF' ] ],
			[ '0:0:0:0:0:0:0:0-7fff:ffff:ffff:ffff:ffff:ffff:ffff:ffff', [ 'v6-00000000000000000000000000000000', 'v6-7FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF' ] ],
			[ '::6d:f:2001/8', [ 'v6-00000000000000000000000000000000', 'v6-00FFFFFFFFFFFFFFFFFFFFFFFFFFFFFF' ] ],
			[ '0:0:0:0:0:0:0:0-ff:ffff:ffff:ffff:ffff:ffff:ffff:ffff', [ 'v6-00000000000000000000000000000000', 'v6-00FFFFFFFFFFFFFFFFFFFFFFFFFFFFFF' ] ],
			// Single IPs
			[ '10.0.0.1', [ '0A000001', '0A000001' ] ],
			[ '2001:0db8:85a3::7344', [ 'v6-20010DB885A300000000000000007344', 'v6-20010DB885A300000000000000007344' ] ],
			// We don't support /0 ranges
			// [ '::fe:f:2001/0', [ 'v6-00000000000000000000000000000000', 'v6-FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF' ] ],
			[ '0:0:0:0:0:0:0:0-ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff', [ 'v6-00000000000000000000000000000000', 'v6-FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF' ] ],
			// Invalid input
			[ '10.0.0.0/99', [ false, false ] ],
			[ false, [ false, false ] ],
		];
	}

	/**
	 * @covers \Wikimedia\IPUtils::getSubnet()
	 * @dataProvider provideSubnetsToGet
	 */
	public function testGetSubnet( $ip, $subnet ) {
		$this->assertEquals( $subnet, IPUtils::getSubnet( $ip ), "Subnet extraction" );
	}

	/**
	 * Provider for IPUtilsTest::testGetSubnet()
	 */
	public function provideSubnetsToGet() {
		return [
			[ '127.0.0.1', '127.0.0' ],
			[ '::6d:f:2001', 'v6-00000000000000000000000000000000' ],
		];
	}

	/**
	 * @covers \Wikimedia\IPUtils::getIPsInRange()
	 * @dataProvider provideIPsInRangeToGet
	 */
	public function testGetIPsInRange( $range, $expected ) {
		$this->assertEquals( $expected, IPUtils::getIPsInRange( $range ), "IPs from a range" );
	}

	/**
	 * @covers \Wikimedia\IPUtils::getIPsInRange()
	 * @dataProvider provideIPsInRangeToGet
	 */
	 public static function provideIPsInRangeToGet() {
		return [
			[ '212.35.31.121/28', [ '212.35.31.112', '212.35.31.113', '212.35.31.114', '212.35.31.115', '212.35.31.116',
			  '212.35.31.117', '212.35.31.118', '212.35.31.119', '212.35.31.120', '212.35.31.121', '212.35.31.122','212.35.31.123',
			  '212.35.31.124', '212.35.31.125', '212.35.31.126', '212.35.31.127' ]
			],
			[ '212.35.31.121/26', [ '212.35.31.64', '212.35.31.65', '212.35.31.66', '212.35.31.67', '212.35.31.68', '212.35.31.69',
			  '212.35.31.70', '212.35.31.71', '212.35.31.72', '212.35.31.73', '212.35.31.74', '212.35.31.75', '212.35.31.76',
			  '212.35.31.77', '212.35.31.78', '212.35.31.79', '212.35.31.80', '212.35.31.81', '212.35.31.82', '212.35.31.83',
			  '212.35.31.84', '212.35.31.85', '212.35.31.86', '212.35.31.87', '212.35.31.88', '212.35.31.89', '212.35.31.90',
			  '212.35.31.91', '212.35.31.92', '212.35.31.93', '212.35.31.94', '212.35.31.95', '212.35.31.96', '212.35.31.97',
			  '212.35.31.98', '212.35.31.99', '212.35.31.100', '212.35.31.101', '212.35.31.102', '212.35.31.103', '212.35.31.104',
			  '212.35.31.105', '212.35.31.106', '212.35.31.107', '212.35.31.108', '212.35.31.109', '212.35.31.110', '212.35.31.111',
			  '212.35.31.112', '212.35.31.113', '212.35.31.114', '212.35.31.115', '212.35.31.116', '212.35.31.117', '212.35.31.118',
			  '212.35.31.119', '212.35.31.120', '212.35.31.121', '212.35.31.122', '212.35.31.123', '212.35.31.124', '212.35.31.125',
			  '212.35.31.126', '212.35.31.127' ],
			],
			[ '212.35.31.112-212.35.31.127', [ '212.35.31.112', '212.35.31.113', '212.35.31.114', '212.35.31.115', '212.35.31.116',
			  '212.35.31.117', '212.35.31.118', '212.35.31.119', '212.35.31.120', '212.35.31.121', '212.35.31.122','212.35.31.123',
			  '212.35.31.124', '212.35.31.125', '212.35.31.126', '212.35.31.127' ]
			],
		];
	 }

	/**
	 * @covers \Wikimedia\IPUtils::getIPsInRange()
	 * @dataProvider provideIPsInForbiddenRangeToNotGet
	 */
	public function testGetForbiddenIPsInRange( $range, $expected ) {
		$this->expectException( \InvalidArgumentException::class );
		$this->expectExceptionMessage( $expected );
		IPUtils::getIPsInRange( $range );
	}

	/**
	 * Provider for IPUtilsTest::testGetForbiddenIPsInRange
	 */
	public static function provideIPsInForbiddenRangeToNotGet() {
		$string = 'is too large, it contains more than 65536 addresses';
		return [
			// By definition, a 'range' must contains more than one value
			[ '212.35.31.121/32', 'Invalid range given: ' . '212.35.31.121/32' ],
			[ '16.20.184.255-16.20.184.255', 'Invalid range given: ' . '16.20.184.255-16.20.184.255' ],

			// Other impossible/invalid ranges
			[ '212.35.31.121/33', 'Invalid range given: ' . '212.35.31.121/33' ],
			[ '212.35.31.121/999', 'Invalid range given: ' . '212.35.31.121/999' ],
			[ '16.17.184.0-16.27.184', 'Invalid range given: ' . '16.17.184.0-16.27.184' ],

			// IPv6 ranges
			[ '2001:5c0:1400:a::df:2', 'Cannot retrieve addresses for IPv6 range: ' . '2001:5c0:1400:a::df:2' ],
			[ 'fc::100:a:d:1:e:ac/96', 'Cannot retrieve addresses for IPv6 range: ' . 'fc::100:a:d:1:e:ac/96' ],
			[ '2607:fea8:bfa0:0bd0:0000:0000:0000:0000-2607:fea8:bfa0:0bd0:0000:0000:0000:0000', 'Cannot retrieve '
			. 'addresses for IPv6 range: 2607:fea8:bfa0:0bd0:0000:0000:0000:0000-2607:fea8:bfa0:0bd0:0000:0000:0000:0000' ],

			// Forbidden ranges
			[ '212.35.31.121/1', 'Range 212.35.31.121/1 ' . $string ],
			[ '212.35.31.121/15', 'Range 212.35.31.121/15 ' . $string ],
			[ '16.17.184.0-16.20.184.255', 'Range 16.17.184.0-16.20.184.255 ' . $string ],
			[ '16.02.000.0-16.20.184.255', 'Range 16.02.000.0-16.20.184.255 ' . $string ],
		];
	}
}
