<?php
use SilverStripe\Model\FieldType\DBVarchar;

class DBVarcharTest extends SapphireTest {
	/**
	 * @covers SilverStripe\Model\FieldType\DBVarchar::Initial()
	 */
	public function testInitial() {
		$varchar = new DBVarchar();
		$this->assertSame(null, $varchar->Initial());

		$varchar->setValue('test string');
		$this->assertSame('t.', $varchar->Initial());
	}

	/**
	 * @covers SilverStripe\Model\FieldType\DBVarchar::URL()
	 */
	public function testURL() {
		$varchar = new DBVarchar();
		$this->assertSame('http://', $varchar->URL());

		$varchar->setValue('example.com');
		$this->assertSame('http://example.com', $varchar->URL());

		$varchar->setValue('example.com/test');
		$this->assertSame('http://example.com/test', $varchar->URL());

		$varchar->setValue('ftp://example.com');
		$this->assertSame('ftp://example.com', $varchar->URL());

		// Malformed URLs add 'http://' before them, even if that's not always correct
		$varchar->setValue('ftp//example.com:');
		$this->assertSame('http://ftp//example.com:', $varchar->URL());
	}

	/**
	 * @covers SilverStripe\Model\FieldType\DBVarchar::RTF()
	 */
	public function testRTF() {
		$varchar = new DBVarchar();
		$this->assertSame('', $varchar->RTF());

		$varchar->setValue('test string');
		$this->assertSame('test string', $varchar->RTF());

		$varchar->setValue("test\nstring");
		$this->assertSame('test\par string', $varchar->RTF());

		$varchar->setValue("test\nstring\n");
		$this->assertSame('test\par string\par ', $varchar->RTF());

		// Ensure a literal \ followed by n is not replaced by "\par "
		$varchar->setValue('test\nstring');
		$this->assertSame('test\nstring', $varchar->RTF());
	}

	/**
	 * @covers SilverStripe\Model\FieldType\DBVarchar::scaffoldFormField()
	 */
	public function testScaffoldFormField() {
		$varchar = new DBVarchar();
		$this->assertInstanceOf('TextField', $field = $varchar->scaffoldFormField('Field Title'));
		$this->assertSame('Field Title', $field->Title());

		$varchar->setNullifyEmpty(false);
		$this->assertInstanceOf('NullableField', $field = $varchar->scaffoldFormField('Field Title'));
		$this->assertSame('Field Title', $field->Title());
	}
}
