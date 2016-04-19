<?php
use \SilverStripe\Model\FieldType\DBBoolean;

class DBBooleanTest extends SapphireTest {
	protected $usesDatabase = true;

	protected $extraDataObjects = array(
		'DataObjectTest_Player'
	);

	public function testNice() {
		$test = new DBBoolean('x');
		$this->assertSame('No', $test->Nice());

		$test->setValue(true);
		$this->assertSame('Yes', $test->Nice());

		$test->setValue(0);
		$this->assertSame('No', $test->Nice());
	}

	public function testNiceAsBoolean() {
		$test = new DBBoolean('x');
		$this->assertSame('false', $test->NiceAsBoolean());

		$test->setValue(true);
		$this->assertSame('true', $test->NiceAsBoolean());

		$test->setValue(0);
		$this->assertSame('false', $test->NiceAsBoolean());
	}

	public function testSaveInto() {
		$test = new DBBoolean('IsRetired');
		$obj = new DataObjectTest_Player();

		$test->saveInto($obj);
		$this->assertSame(0, $obj->IsRetired);

		$test->setValue(true);
		$test->saveInto($obj);
		$this->assertSame(1, $obj->IsRetired);
	}
}
