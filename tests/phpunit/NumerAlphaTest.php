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
            NumerAlpha::renderCounter( $parser, $frame, array( '    First list ' ) ),
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


    }

}