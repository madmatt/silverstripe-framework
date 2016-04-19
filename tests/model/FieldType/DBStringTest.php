<?php

use SilverStripe\Model\FieldType\DBField;
use SilverStripe\Model\FieldType\DBString;

/**
 * @package framework
 * @subpackage tests
 */

class DBStringTest extends SapphireTest {
	/**
	 * @covers SilverStripe\Model\FieldType\DBString::LimitCharacters()
	 */
	public function testLimitCharacters() {
		$cases = array(
			'The little brown fox jumped over the lazy cow.' => 'The little brown fox...',
			'<p>This is some text in a paragraph.</p>' => '<p>This is some text...'
		);

		foreach($cases as $originalValue => $expectedValue) {
			$textObj = new StringFieldTest_MyStringField('Test');
			$textObj->setValue($originalValue);
			$this->assertEquals($expectedValue, $textObj->LimitCharacters());
		}
	}

	/**
	 * @covers SilverStripe\Model\FieldType\DBString::LimitCharactersToClosestWord()
	 */
	public function testLimitCharactersToClosestWord() {
		$cases = array(
			/* Standard words limited, ellipsis added if truncated */
			'Lorem ipsum dolor sit amet' => 'Lorem ipsum dolor sit...',

			/* Complete words less than the character limit don't get truncated, ellipsis not added */
			'Lorem ipsum' => 'Lorem ipsum',
			'Lorem' => 'Lorem',
			'' => '',	// No words produces nothing!

			/* HTML tags get stripped out, leaving the raw text */
			'<p>Lorem ipsum dolor sit amet</p>' => 'Lorem ipsum dolor sit...',
			'<p><span>Lorem ipsum dolor sit amet</span></p>' => 'Lorem ipsum dolor sit...',
			'<p>Lorem ipsum</p>' => 'Lorem ipsum',

			/* HTML entities are treated as a single character */
			'Lorem &amp; ipsum dolor sit amet' => 'Lorem &amp; ipsum dolor...'
		);

		foreach($cases as $originalValue => $expectedValue) {
			$textObj = new StringFieldTest_MyStringField('Test');
			$textObj->setValue($originalValue);
			$this->assertEquals($expectedValue, $textObj->LimitCharactersToClosestWord(24));
		}
	}

	/**
	 * @covers SilverStripe\Model\FieldType\DBString::LimitWordCount()
	 */
	public function testLimitWordCount() {
		$cases = array(
			/* Standard words limited, ellipsis added if truncated */
			'The little brown fox jumped over the lazy cow.' => 'The little brown...',
			' This text has white space around the ends ' => 'This text has...',

			/* Words less than the limt word count don't get truncated, ellipsis not added */
			'Two words' => 'Two words',	// Two words shouldn't have an ellipsis
			'One' => 'One',	// Neither should one word
			'' => '',	// No words produces nothing!

			/* HTML tags get stripped out, leaving the raw text */
			'<p>Text inside a paragraph tag should also work</p>' => 'Text inside a...',
			'<p><span>Text nested inside another tag should also work</span></p>' => 'Text nested inside...',
			'<p>Two words</p>' => 'Two words'
		);

		foreach($cases as $originalValue => $expectedValue) {
			$textObj = new StringFieldTest_MyStringField('Test');
			$textObj->setValue($originalValue);
			$this->assertEquals($expectedValue, $textObj->LimitWordCount(3));
		}
	}

	/**
	 * @covers SilverStripe\Model\FieldType\DBString::LimitWordCountXML()
	 */
	public function testLimitWordCountXML() {
		$cases = array(
			'<p>Stuff & stuff</p>' => 'Stuff &amp;...',
			"Stuff\nBlah Blah Blah" => "Stuff\nBlah Blah...",
			"Stuff<Blah Blah" => "Stuff&lt;Blah Blah",
			"Stuff>Blah Blah" => "Stuff&gt;Blah Blah"
		);

		foreach($cases as $originalValue => $expectedValue) {
			$textObj = new StringFieldTest_MyStringField('Test');
			$textObj->setValue($originalValue);
			$this->assertEquals($expectedValue, $textObj->LimitWordCountXML(3));
		}
	}

	/**
	 * @covers SilverStripe\Model\FieldType\DBString::forTemplate()
	 */
	public function testForTemplate() {
		$this->assertEquals(
			"this is<br />\na test!",
			DBField::create_field('StringFieldTest_MyStringField', "this is\na test!")->forTemplate()
		);
	}

	/**
	 * @covers SilverStripe\Model\FieldType\DBString::LowerCase()
	 */
	public function testLowerCase() {
		$this->assertEquals(
			'this is a test!',
			DBField::create_field('StringFieldTest_MyStringField', 'This is a TEST!')->LowerCase()
		);
	}

	/**
	 * @covers SilverStripe\Model\FieldType\DBString::UpperCase()
	 */
	public function testUpperCase() {
		$this->assertEquals(
			'THIS IS A TEST!',
			DBField::create_field('StringFieldTest_MyStringField', 'This is a TEST!')->UpperCase()
		);
	}

	/**
	 * @covers SilverStripe\Model\FieldType\DBString::exists()
	 */
	public function testExists() {
		// True exists
		$this->assertTrue(DBField::create_field('StringFieldTest_MyStringField', true)->exists());
		$this->assertTrue(DBField::create_field('StringFieldTest_MyStringField', '0')->exists());
		$this->assertTrue(DBField::create_field('StringFieldTest_MyStringField', '1')->exists());
		$this->assertTrue(DBField::create_field('StringFieldTest_MyStringField', 1)->exists());
		$this->assertTrue(DBField::create_field('StringFieldTest_MyStringField', 1.1)->exists());

		// false exists
		$this->assertFalse(DBField::create_field('StringFieldTest_MyStringField', false)->exists());
		$this->assertFalse(DBField::create_field('StringFieldTest_MyStringField', '')->exists());
		$this->assertFalse(DBField::create_field('StringFieldTest_MyStringField', null)->exists());
		$this->assertFalse(DBField::create_field('StringFieldTest_MyStringField', 0)->exists());
		$this->assertFalse(DBField::create_field('StringFieldTest_MyStringField', 0.0)->exists());
	}

}

class StringFieldTest_MyStringField extends DBString implements TestOnly {
	public function requireField() {}
}
