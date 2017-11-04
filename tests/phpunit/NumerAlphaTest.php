<?php
/**
 * @group NumerAlpha
 * @covers NumerAlpha
 */
class NumerAlphaTest extends MediaWikiTestCase {

	protected function setUp() {
		parent::setUp();
	}

	protected function tearDown() {
		parent::tearDown();
	}

	public function testNumeralValues () {

		$parser = new Parser();
		$frame = new PPFrame_DOM( new Preprocessor_DOM( $parser ) );

		$this->assertEquals(
			'1',
			NumerAlpha::renderCounter( $parser, $frame, array( ' First list ' ) ),
			'First item of "First list" should equal 1'
		);
		$this->assertEquals(
			'2',
			NumerAlpha::renderCounter( $parser, $frame, array() ),
			'Implied "First list" second item should equal 2'
		);
		$this->assertEquals(
			'1',
			NumerAlpha::renderCounter( $parser, $frame, array( ' Second list ' ) ),
			'First it of "Second list" should equal 1'
		);
		$this->assertEquals(
			'2',
			NumerAlpha::renderCounter( $parser, $frame, array() ),
			'Implied "Second list" second item should equal 2'
		);
		$this->assertEquals(
			'3',
			NumerAlpha::renderCounter( $parser, $frame, array( '	First list ' ) ),
			'"First list" third item should equal 3'
		);


		$thirdListArgs = array(
			'Third list',
			' pad length = 2 ',
			' pad character = x ',
		);
		$this->assertEquals(
			'x1',
			NumerAlpha::renderCounter( $parser, $frame, $thirdListArgs ),
			'"Third list" first item should equal x1'
		);
		$this->assertEquals(
			'x2',
			NumerAlpha::renderCounter( $parser, $frame, array() ),
			'"Third list" second item should equal x2'
		);
		$this->assertEquals(
			'xxxx3',
			NumerAlpha::renderCounter( $parser, $frame, array( '   ', ' pad length = 5 ' ) ),
			'"Third list" third item should equal xxxx3'
		);
		$this->assertEquals(
			'00004',
			NumerAlpha::renderCounter( $parser, $frame, array( '   ', ' pad character = 0 ' ) ),
			'"Third list" fourth item should equal 00004'
		);

		$this->assertEquals(
			'(4)',
			NumerAlpha::renderCounter( $parser, $frame, array( '  First list ', ' prefix = ( ', 'suffix = )' ) ),
			'"Third list" third item should equal (4)'
		);

		/** list with standard format showing levels
			{{#counter:list-with-levels|level prefix=:}} Start lev 1
			{{#counter:}} Stay lev 1
			{{#counter:|level=2}} Jump to lev 2
			{{#counter:}} Stay lev 2
			{{#counter:|level=1}} Drop to lev 1
			{{#counter:|level=2}} back to lev 2
			{{#counter:|level=3}} jump to lev 3

			Results in:
			:1
			:2
			::1
			::2
			:3
			::1
			:::1

		*/
		$this->assertEquals(
			':1',
			NumerAlpha::renderCounter( $parser, $frame, array( 'list-with-levels', ' level prefix = : ' ) ),
			'"list-with-levels" first item should equal 1 at level 1'
		);
		$this->assertEquals(
			':2',
			NumerAlpha::renderCounter( $parser, $frame, array() ),
			'"list-with-levels" second item should equal 2, remembering last counter and maintains level'
		);
		$this->assertEquals(
			'::1',
			NumerAlpha::renderCounter( $parser, $frame, array('','level=2') ),
			'"list-with-levels" first item at level 2 should equal 1'
		);
		$this->assertEquals(
			'::2',
			NumerAlpha::renderCounter( $parser, $frame, array() ),
			'"list-with-levels" second item at level 2 should equal 2, remembering last counter and maintains level'
		);
		$this->assertEquals(
			':3',
			NumerAlpha::renderCounter( $parser, $frame, array('', ' level =1') ),
			'"list-with-levels" third item at level 1 should equal 3, remembers level'
		);
		$this->assertEquals(
			'::1',
			NumerAlpha::renderCounter( $parser, $frame, array('', ' level  = 2 ') ),
			'"list-with-levels" should equal 1, previous higher level increment resets lower level counters'
		);
		$this->assertEquals(
			':::1',
			NumerAlpha::renderCounter( $parser, $frame, array(' ','level=3') ),
			'"list-with-levels" should equal 1, increasing level resets counter'
		);

		/** outline-format list with levels and non-standard level prefix
			{{#counter:outline-list-with-levels|level prefix=_|format=outline}} Start lev 1
			{{#counter:}} Stay lev 1
			{{#counter:|level=2}} Jump to lev 2
			{{#counter:}} Stay lev 2
			{{#counter:|level=1}} Drop to lev 1
			{{#counter:|level=2}} back to lev 2
			{{#counter:|level=3}} jump to lev 3

			Results in:
			_1
			_2
			__2.1
			__2.2
			_3
			__3.1
			___3.1.1
		*/
		$this->assertEquals(
			'_1',
			NumerAlpha::renderCounter( $parser, $frame, array( 'outline-list-with-levels', ' level prefix = _ ' ) ),
			'"outline-list-with-levels" first item should equal 1 at level 1'
		);
		$this->assertEquals(
			'_2',
			NumerAlpha::renderCounter( $parser, $frame, array() ),
			'"outline-list-with-levels" second item should equal 2, remembering last counter and maintains level'
		);
		$this->assertEquals(
			'__1',
			NumerAlpha::renderCounter( $parser, $frame, array('','level=2') ),
			'"outline-list-with-levels" first item at level 2 should equal 1'
		);
		$this->assertEquals(
			'__2',
			NumerAlpha::renderCounter( $parser, $frame, array() ),
			'"outline-list-with-levels" second item at level 2 should equal 2, remembering last counter and maintains level'
		);
		$this->assertEquals(
			'_3',
			NumerAlpha::renderCounter( $parser, $frame, array('', ' level =1') ),
			'"outline-list-with-levels" third item at level 1 should equal 3, remembers level'
		);
		$this->assertEquals(
			'__1',
			NumerAlpha::renderCounter( $parser, $frame, array('', 'level=2') ),
			'"outline-list-with-levels" should equal 1, previous higher level increment resets lower level counters'
		);
		$this->assertEquals(
			'___1',
			NumerAlpha::renderCounter( $parser, $frame, array(' ','level=3') ),
			'"outline-list-with-levels" should equal 1, increasing level resets counter'
		);
	}

}