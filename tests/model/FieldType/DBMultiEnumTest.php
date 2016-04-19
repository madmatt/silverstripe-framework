<?php
use \SilverStripe\Model\FieldType\DBMultiEnum;

class DBMultiEnumTest extends SapphireTest {
	public function testConstructor() {
		$multiEnum = new DBMultiEnum('TestField', 'test1,test2,test3');
		$this->assertSame(null, $multiEnum->getDefault());
		$this->assertSame(
			[
				'test1' => 'test1',
				'test2' => 'test2',
				'test3' => 'test3'
			],
			$multiEnum->enumValues()
		);

		$multiEnum = new DBMultiEnum('TestField', 'test1,test2,test3', 'test1, test3');
		$this->assertSame('test1,test3', $multiEnum->getDefault());
	}

	/**
	 * @expectedException InvalidArgumentException
	 * @expectedExceptionMessage DBMultiEnum::__construct() The default value 'test4' does not match any item in the enumeration
	 */
	public function testConstructorFailingDefaults() {
		$failingMultiEnum = new DBMultiEnum('TestField', 'test1,test2', 'test4');
	}
}
