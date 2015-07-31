<?php
/**
 * NumerAlpha MediaWiki extension - Provides an incremental tag
 * with zero padded numbers, roman and alpha numbers
 *
 * @link http://www.mediawiki.org/wiki/Extension:NumerAlpha Documentation
 * @file NumerAlpha.php
 * @author Thierry G. Veilleux (Kronoxt), James Montalvo (jamesmontalvo3)
 * @copyright (C) 2009 Thierry G. Veilleux (Kronoxt)
 * @license http://www.publicdomainmanifesto.org/ Public Domain
 */

// Check if we are being called directly
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is an extension to MediaWiki and thus not a valid entry point.' );
}

class NumerAlpha {

	static $numer = array(
		0 => 1, // alpha
		1 => 1, // arabic
		2 => 1, // roman
	);
	static $frame = null; // set with each use of parser function...
	static $prevName = 'default'; // default counter name
	static $lists = array();
	static $listTypes = null;

	// Hook our callback function into the parser
	static public function onParserFirstCallInit ( Parser &$parser ) {

		// When the parser sees the <sample> tag, it executes
		// the wfSampleRender function (see below)
		$parser->setHook( 'in', 'NumerAlpha::renderNumeralTag' );
		$parser->setHook( 'ia', 'NumerAlpha::renderAlphaTag' );
		$parser->setHook( 'ir', 'NumerAlpha::renderRomanTag' );

		// Always return true from this function. The return value does not denote
		// success or otherwise have meaning - it just must always be true.

		self::$listTypes = array(
			wfMessage( 'ext-numeralpha-list-type-numeral' )->text() => "numeral",
			wfMessage( 'ext-numeralpha-list-type-alpha' )->text() => "alpha",
			wfMessage( 'ext-numeralpha-list-type-roman' )->text() => "roman",
		);

		$parser->setFunctionHook(
			'counter', // parser function name, == $magicWords value i18n
			array(
				'NumerAlpha',  // class to call function from
				'renderCounter' // function to call within that class
			),
			SFH_OBJECT_ARGS
		);

		return true;
	}

	static function extractListOptions ( $rawArgs ) {

		$name = trim( array_shift( $rawArgs ) ); // remove first element, since this is the counter name
		if ( $name === "" ) {
			$name = self::$prevName; // not set? get previous
		}
		else {
			self::$prevName = $name; // is set? set as previous for next usage
		}

		if ( ! isset( self::$lists[ $name ] ) ) {
			self::$lists[ $name ] = array(
				'levels' => array(),
			);
		}

		foreach( $rawArgs as $rawArg ) {

			//Convert args with "=" into an array of options
			$pair = explode( '=', self::$frame->expand($rawArg) , 2 );
			if ( count( $pair ) === 2 ) {
				$param  = trim( $pair[0] ); //Convert to lower case so it is case-insensitive
				$value = trim( $pair[1] );

				switch ( $param ) {
					case wfMessage( 'ext-numeralpha-list-type-label' )->text():
						if ( isset( self::$listTypes[ $value ] ) ) {
							self::$lists[ $name ][ 'type' ] = self::$listTypes[ $value ];
						}
						break;
					case wfMessage( 'ext-numeralpha-list-set-label' )->text():
						$newCountValue = intVal( $value );
						if ( $newCountValue < 1 ) {
							$newCountValue = 1;
						}
						break;
					case wfMessage( 'ext-numeralpha-list-pad-length' )->text():
						self::$lists[ $name ][ 'padlength' ] = intVal( $value );
						break;
					case wfMessage( 'ext-numeralpha-list-pad-char' )->text():
						self::$lists[ $name ][ 'padchar' ] = $value;
						break;
					case wfMessage( 'ext-numeralpha-list-prefix' )->text():
						self::$lists[ $name ][ 'prefix' ] = $value;
						break;
					case wfMessage( 'ext-numeralpha-list-suffix' )->text():
						self::$lists[ $name ][ 'suffix' ] = $value;
						break;
					case wfMessage( 'ext-numeralpha-list-level-label' )->text():
						self::$lists[ $name ][ 'level' ] = intVal($value);
						break;
					case wfMessage( 'ext-numeralpha-list-level-prefix-label' )->text():
						self::$lists[ $name ][ 'level prefix' ] = $value;
						break;
					case wfMessage( 'ext-numeralpha-list-format-label' )->text():
						if ( $value === wfMessage( 'ext-numeralpha-list-format-outline' )->text() ) {
							self::$lists[ $name ][ 'format' ] = 'outline';
						}
						else {
							self::$lists[ $name ][ 'format' ] = 'standard';
						}
						break;
				}

			}

		}

		// if level not specifed, use the previous level if there is one
		if ( isset( self::$lists[ $name ][ 'level' ] ) ) {
			$farts = true;
		}
		elseif ( isset( self::$lists[ $name ][ 'prev-level' ] ) ) {
			self::$lists[ $name ][ 'level' ] = self::$lists[ $name ][ 'prev-level' ];
		}
		// otherwise set level to 1
		else {
			self::$lists[ $name ][ 'level' ] = 1;
		}
		self::$lists[ $name ][ 'prev-level' ] = self::$lists[ $name ][ 'level' ];

		// if user inputs level 1, equates to array index zero
		$levelIndex = self::$lists[ $name ][ 'level' ] - 1;

		// if level has been used before (and hasn't been reset due to lower
		// level being used) then increase the count on this level
		if ( isset( self::$lists[ $name ][ 'levels' ][ $levelIndex ] ) ) {
			self::$lists[ $name ][ 'levels' ][ $levelIndex ]++;
		}

		// @todo FIXME this shoudln't be necessary
		else {
			self::$lists[ $name ][ 'levels' ][ $levelIndex ] = 1;
		}

		// Make sure all levels are set. Fill in blanks with 1.
		// Is this excessive?
		for ( $i = 0; $i <= $levelIndex; $i++ ) {
			if ( ! isset( self::$lists[ $name ][ 'levels' ][ $i ] ) ) {
				self::$lists[ $name ][ 'levels' ][ $i ] = 1;
			}
		}

		// if indices exist beyond the desired level, cut the array of levels
		// down just to the zeroth index through the desired index
		if ( isset( self::$lists[ $name ][ 'levels' ][ $levelIndex + 1 ] ) ) {
			self::$lists[ $name ][ 'levels' ] = array_slice(
				self::$lists[ $name ][ 'levels' ],
				0,
				self::$lists[ $name ][ 'level' ] // AKA $levelIndex + 1
			);
		}

		// despite all the level-working above, if user has specified a new
		// count value, override:
		if ( isset( $newCountValue ) ) {
			self::$lists[ $name ][ 'levels' ][ $levelIndex ] = $newCountValue;
		}


		// set default list type if not already set
		if ( ! isset( self::$lists[ $name ][ 'type' ] ) ) {
			self::$lists[ $name ][ 'type' ] = 'numeral';
		}

		// set pad length and character for numeral type only
		if ( self::$lists[ $name ][ 'type' ] === 'numeral' ) {

			// set default pad length if not already set
			if ( ! isset( self::$lists[ $name ][ 'padlength' ] ) ) {
				self::$lists[ $name ][ 'padlength' ] = 1;
			}

			// set default pad character if not already set
			if ( ! isset( self::$lists[ $name ][ 'padchar' ] ) ) {
				self::$lists[ $name ][ 'padchar' ] = '0';
			}
		}

		// set list counter prefix and suffix character(s)
		if ( ! isset( self::$lists[ $name ][ 'prefix' ] ) ) {
			self::$lists[ $name ][ 'prefix' ] = '';
		}
		if ( ! isset( self::$lists[ $name ][ 'suffix' ] ) ) {
			self::$lists[ $name ][ 'suffix' ] = '';
		}

		// level prefix = string applied before counter, x times per level
		if ( ! isset( self::$lists[ $name ][ 'level prefix' ] ) ) {
			self::$lists[ $name ][ 'level prefix' ] = '';
		}
		self::$lists[ $name ][ 'full level prefix' ] = str_repeat(
			self::$lists[ $name ][ 'level prefix' ],
			self::$lists[ $name ][ 'level' ]
		);


		// This if-statement determines if we're using standard or outline format
		// self::$lists[ $name ][ 'count' ] is the display for the counter.
		// The name sucks, but is here for now due to historical naming.
		if ( ! isset( self::$lists[ $name ][ 'format' ] ) || self::$lists[ $name ][ 'format' ] == 'standard' ) {
			self::$lists[ $name ][ 'count' ] = self::$lists[ $name ][ 'levels' ][ $levelIndex ];
		}
		else {
			self::$lists[ $name ][ 'count' ] = implode(
				wfMessage( 'ext-numeralpha-list-format-outline-glue' ),
				self::$lists[ $name ][ 'levels' ]
			);
		}

		return self::$lists[ $name ];

	}

	/**
	 *	Call parser function like {{#counter: Counter name | type = Counter type | set = 5 }}
	 *
	 **/
	static public function renderCounter ( &$parser, $frame, $args ) {

		self::$frame = $frame;

		$list = self::extractListOptions( $args );

		if ( $list[ 'type' ] === 'numeral' ) {
			return self::getNumeralValue( $list );
		}

		if ( $list[ 'type' ] === 'alpha' ) {
			return self::getAlphaValue( $list );
		}

		if ( $list[ 'type' ] === 'roman' ) {
			return self::getRomanValue( $list );
		}

	}

	static protected function getNumeralValue ( $list ) {
		$counterValue = str_pad(
			$list[ 'count' ],
			$list[ 'padlength' ],
			$list[ 'padchar' ],
			STR_PAD_LEFT // this may require internationalization...
		);

		return htmlspecialchars( $list['full level prefix'] . $list[ 'prefix' ] . $counterValue . $list[ 'suffix' ] );
	}


	static protected function getAlphaValue ( $list ) {

		$num = $list[ 'count' ];

		$alpha = "";
		while ($num >= 1) {
			$num = $num - 1;
			$alpha = chr( ($num % 26) + 97 ) . $alpha; //we use the ascii table
			$num = $num / 26;
		}

		return htmlspecialchars( $list['full level prefix'] . $list[ 'prefix' ] . $alpha . $list[ 'suffix' ] );

	}

	static protected function getRomanValue ( $list ) {

		$num = $list[ 'count' ];

		$result = '';
		$equival = array(
			'm'  => 1000,
			'cm' => 900,
			'd'  => 500,
			'cd' => 400,
			'c'  => 100,
			'xc' => 90,
			'l'  => 50,
			'xl' => 40,
			'x'  => 10,
			'ix' => 9,
			'v'  => 5,
			'iv' => 4,
			'i'  => 1
		);
		foreach ($equival as $roma => $val) {
			$concordances = intval($num / $val);
			$result .= str_repeat($roma, $concordances);
			$num = $num % $val;
		}

		return htmlspecialchars( $list['full level prefix'] . $list[ 'prefix' ] . $result . $list[ 'suffix' ] );

	}

	static public function renderNumeralTag ( $input, array $argv, Parser $parser, PPFrame $frame ) {
		if ( isset( $argv['reset'] ) && $argv['reset'] == "yes" OR isset( $argv['reset'] ) && $argv['reset'] == "1" ) {
			self::$numer[1] = 1;
		}
		if ( isset( $argv['begin'] ) && $argv['begin'] != "" ) {
			self::$numer[1] = $argv['begin'];
		}
		$num = self::$numer[1]++;
		$num = intval($num);

		return self::getNumeralValue( array(
			'count' => $num,
			'padlength' => 1,
			'padchar' => 0,
			'prefix' => '',
			'suffix' => '. ' . $input
		) ) . '<br />';
	}

	static public function renderAlphaTag ( $input, array $argv, Parser $parser, PPFrame $frame ) {

		if (isset($argv['reset']) && $argv['reset'] == "yes" OR isset($argv['reset']) && $argv['reset'] == "1") {self::$numer[0] = 1;}
		if (isset($argv['begin']) && $argv['begin'] != "") {self::$numer[0] = $argv['begin'];}
		$num = self::$numer[0]++;
		$num = intval($num);

		return self::getAlphaValue( array(
			'count' => $num,
			'prefix' => '',
			'suffix' => '. ' . $input
		) ) . '<br />';

	}

	// ...render unto Caesar?
	static public function renderRomanTag ( $input, array $argv, Parser $parser, PPFrame $frame ) {

		if (isset($argv['reset']) && $argv['reset'] == "yes" OR isset($argv['reset']) && $argv['reset'] == "1") {self::$numer[2] = 1;}
		if (isset($argv['begin']) && $argv['begin'] != "") {self::$numer[2] = $argv['begin'];}
		$num = self::$numer[2]++;
		$num = intval($num);


		return self::getRomanValue( array(
			'count' => $num,
			'prefix' => '',
			'suffix' => '. ' . $input
		) ) . '<br />';

	}

}