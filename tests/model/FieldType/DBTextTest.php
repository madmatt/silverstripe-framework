<?php

use SilverStripe\Model\FieldType\DBField;
use SilverStripe\Model\FieldType\DBText;

/**
 * @package framework
 * @subpackage tests
 */
class DBTextTest extends SapphireTest {

	/**
	 * @covers SilverStripe\Model\FieldType\DBText::LimitSentences()
	 */
	public function testLimitSentences() {
		$cases = array(
			'' => '',
			'First sentence.' => 'First sentence.',
			'First sentence. Second sentence' => 'First sentence. Second sentence.',
			'<p>First sentence.</p>' => 'First sentence.',
			'<p>First sentence. Second sentence. Third sentence</p>' => 'First sentence. Second sentence.',
			'<p>First sentence. <em>Second sentence</em>. Third sentence</p>' => 'First sentence. Second sentence.',
			'<p>First sentence. <em class="dummyClass">Second sentence</em>. Third sentence</p>'
				=> 'First sentence. Second sentence.'
		);

		foreach($cases as $originalValue => $expectedValue) {
			$textObj = new DBText('Test');
			$textObj->setValue($originalValue);
			$this->assertEquals($expectedValue, $textObj->LimitSentences(2));
		}
	}

	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage DBText::LimitSentences() expects one numeric argument
	 * @covers SilverStripe\Model\FieldType\DBText::LimitSentences()
	 */
	public function testLimitSentencesException() {
		$failingObj = new DBText('Test');
		$failingObj->LimitSentences('non-numeric value');
	}

	/**
	 * @covers SilverStripe\Model\FieldType\DBText::FirstSentence()
	 */
	public function testFirstSentence() {
		$cases = array(
			'' => '',
			'First sentence.' => 'First sentence.',
			'First sentence. Second sentence' => 'First sentence.',
			'First sentence? Second sentence' => 'First sentence?',
			'First sentence! Second sentence' => 'First sentence!',
			'<p>First sentence.</p>' => 'First sentence.',
			'<p>First sentence. Second sentence. Third sentence</p>' => 'First sentence.',
			'<p>First sentence. <em>Second sentence</em>. Third sentence</p>' => 'First sentence.',
			'<p>First sentence. <em class="dummyClass">Second sentence</em>. Third sentence</p>' => 'First sentence.',
			'Dr. John Sir. Hello there' => 'Dr. John Sir.',

			// Test the default fallback if a break isn't found at all
			'>20 words x x x x x x x x x x x x x x x x x y z a' => '>20 words x x x x x x x x x x x x x x x x x y...'
		);

		foreach($cases as $originalValue => $expectedValue) {
			$textObj = new DBText('Test');
			$textObj->setValue($originalValue);
			$this->assertEquals($expectedValue, $textObj->FirstSentence());
		}
	}

	/**
	 * @covers SilverStripe\Model\FieldType\DBText::Summary()
	 */
	public function testSummary() {
		$cases = array(
			'' => '',
			"First paragraph<br><br>Second paragraph" => "First paragraph.",
			"First paragraph\n\nSecond paragraph" => "First paragraph.",
			"First paragraph<br>Second paragraph" => "First paragraph\nSecond paragraph.",
			"word1 word2 word3 word4 word5" => "word1 word2 word3 word4...",
			"sentence1. sentence2. sentence3. sentence4. sentence5." => "sentence1.sentence2.",
			'<a href="test.html">test</a>' => 'test[test.html].'
		);

		foreach($cases as $original => $expected) {
			$textObj = DBField::create_field('Text', $original);
			$this->assertEquals($expected, $textObj->Summary(4));
		}
	}

	/**
	 * @covers SilverStripe\Model\FieldType\DBText::BigSummary()
	 */
	public function testBigSummaryPlain() {
		$cases = array(
			'<p>This text has multiple sentences. Big Summary uses this to split sentences up.</p>'
				=> 'This text has multiple...',
			'This text does not have multiple sentences' => 'This text does not...',
			'Very short' => 'Very short',
			'' => ''
		);

		foreach($cases as $originalValue => $expectedValue) {
			$textObj = DBField::create_field('Text', $originalValue);
			$this->assertEquals($expectedValue, $textObj->BigSummary(4, true));
		}
	}

	/**
	 * @covers SilverStripe\Model\FieldType\DBText::BigSummary()
	 */
	public function testBigSummary() {
		$cases = array(
			'<strong>This</strong> text has multiple sentences. Big Summary uses this to split sentences up.</p>'
				=> '<strong>This</strong> text has multiple...',
			'This text does not have multiple sentences' => 'This text does not...',
			'Very short' => 'Very short',
			'sentence1. sentence2. A longer sentence goes here.' => 'sentence1. sentence2. ',
			// If a string doesn't include a </a>, it will ignore the $maxWords arg and insert </a> at the end
			'<a href="test">test. really. long. string.' => '<a href="test">test. really. long. string. </a>',
			'' => ''
		);

		foreach($cases as $originalValue => $expectedValue) {
			$textObj = DBField::create_field('Text', $originalValue);
			$this->assertEquals($expectedValue, $textObj->BigSummary(4, false));
		}
	}

	/**
	 * @covers SilverStripe\Model\FieldType\DBText::FirstParagraph()
	 */
	public function testFirstParagraph() {
		$cases = array(
			'' => array(
				'plain' => '',
				'html' => ''
			),

			'<p>Para1</p><p>Para2</p>' => array(
				'plain' => 'Para1',
				'html' => '<p>Para1</p><p>Para2</p>'
			),

			// Paragraph is defined as \n\n, so a single \n shouldn't do anything
			"Para1\nPara2" => array(
				'plain' => "Para1\nPara2",
				'html' => "Para1\nPara2"
			),

			// If parsed as HTML, then \n\n won't do anything, it's expecting a </p>
			"Para1\n\nPara2" => array(
				'plain' => 'Para1',
				'html' => "Para1\n\nPara2"
			)
		);

		foreach($cases as $originalValue => $expectedValue) {
			$textObj = new DBText('test');
			$textObj->setValue($originalValue);
			$this->assertEquals($expectedValue['plain'], $textObj->FirstParagraph());
			$this->assertEquals($expectedValue['html'], $textObj->FirstParagraph('html'));
		}
	}

	/**
	 * @covers SilverStripe\Model\FieldType\DBText::ContextSummary()
	 */
	public function testContextSummary() {
		$testString1 = '<p>This is some text. It is a test</p>';
		$testKeywords1 = 'test';

		$testString2 = '<p>This is some test text. Test test what if you have multiple keywords.</p>';
		$testKeywords2 = 'some test';

		$testString3 = '<p>A dog ate a cat while looking at a Foobar</p>';
		$testKeyword3 = 'a';
		$testKeyword3a = 'ate';

		$textObj = DBField::create_field('Text', $testString1, 'Text');

		$this->assertEquals(
			'... text. It is a <span class="highlight">test</span>...',
			$textObj->ContextSummary(20, $testKeywords1)
		);

		// Test to ensure it pulls from _REQUEST['Search'] if no $string is set
		$_REQUEST['Search'] = $testKeywords1;

		$this->assertEquals(
			'... text. It is a <span class="highlight">test</span>...',
			$textObj->ContextSummary(20)
		);

		unset($_REQUEST['Search']);

		$textObj->setValue($testString2);

		$this->assertEquals(
			'This is <span class="highlight">some</span> <span class="highlight">test</span> text.'
				. ' <span class="highlight">test</span> <span class="highlight">test</span> what if you have...',
			$textObj->ContextSummary(50, $testKeywords2)
		);

		$textObj->setValue($testString3);

		// test that it does not highlight too much (eg every a)
		$this->assertEquals(
			'A dog ate a cat while looking at a Foobar',
			$textObj->ContextSummary(100, $testKeyword3)
		);

		// it should highlight 3 letters or more.
		$this->assertEquals(
			'A dog <span class="highlight">ate</span> a cat while looking at a Foobar',
			$textObj->ContextSummary(100, $testKeyword3a)
		);
	}
}
