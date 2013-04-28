<?php

namespace SMW\Test;

use SMW\Subobject;
use SMWDIProperty;

use Title;

/**
 * Tests for the SMW\Subobject class
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @since 1.9
 *
 * @file
 * @ingroup SMW
 * @ingroup Test
 *
 * @group SMW
 * @group SMWExtension
 *
 * @licence GNU GPL v2+
 * @author mwjames
 */

/**
 * Tests for the SMW\Subobject class
 *
 * @ingroup SMW
 * @ingroup Test
 */
class SubobjectTest extends ParserTestCase {

	/**
	 * Helper method
	 *
	 * @return string
	 */
	public function getClass() {
		return '\SMW\Subobject';
	}

	/**
	 * DataProvider
	 *
	 * @return array
	 */
	public function getDataProvider() {
		$diPropertyError = new SMWDIProperty( SMWDIProperty::TYPE_ERROR );
		return array(
			// #0
			array(
				array(
					'identifier' => 'Bar',
					'property' => array( 'Foo' => 'bar' )
				),
				array(
					'name' => 'Bar',
					'errors' => 0,
					'propertyCount' => 1,
					'propertyLabel' => 'Foo',
					'propertyValue' => 'Bar'
				)
			),

			// #1
			array(
				array(
					'identifier' => 'bar',
					'property' => array( 'FooBar' => 'bar Foo' )
				),
				array(
					'name' => 'bar',
					'errors' => 0,
					'propertyCount' => 1,
					'propertyLabel' => 'FooBar',
					'propertyValue' => 'Bar Foo',
				)
			),

			// #2
			array(
				array(
					'identifier' => 'foo',
					'property' => array( 9001 => 1001 )
				),
				array( // Expected results
					'name' => 'foo',
					'errors' => 0,
					'propertyCount' => 1,
					'propertyLabel' => array( 9001 ),
					'propertyValue' => array( 1001 ),
				)
			),

			// #3
			array(
				array(
					'identifier' => 'foo bar',
					'property' => array( 1001 => 9001, 'Foo' => 'Bar' )
				),
				array( // Expected results
					'name' => 'foo bar',
					'errors' => 0,
					'propertyCount' => 2,
					'propertyLabel' => array( 1001, 'Foo' ),
					'propertyValue' => array( 9001, 'Bar' ),
				)
			),

			// #4 Property with leading underscore will raise error
			array(
				array(
					'identifier' => 'bar',
					'property' => array( '_FooBar' => 'bar Foo' )
				),
				array(
					'name' => 'bar',
					'errors' => 1,
					'propertyCount' => 0,
					'propertyLabel' => '',
					'propertyValue' => '',
				)
			),

			// #5 Inverse property will raise error
			array(
				array(
					'identifier' => 'bar',
					'property' => array( '-FooBar' => 'bar Foo' )
				),
				array(
					'name' => 'bar',
					'errors' => 1,
					'propertyCount' => 0,
					'propertyLabel' => '',
					'propertyValue' => '',
				)
			),

			// #6 Improper value for wikipage property will add an 'Has improper value for'
			array(
				array(
					'identifier' => 'bar',
					'property' => array( 'Foo' => '' )
				),
				array(
					'name' => 'bar',
					'errors' => 1,
					'propertyCount' => 2,
					'propertyLabel' => array( $diPropertyError->getLabel(), 'Foo' ),
					'propertyValue' => 'Foo',
				)
			),
		);
	}

	/**
	 * Helper method to get Subobject instance
	 *
	 * @return Subobject
	 */
	private function getInstance( Title $title, $name = '') {
		return new Subobject( $title, $name );
	}

	/**
	 * @test Subobject::__construct
	 *
	 * @since 1.9
	 */
	public function testConstructor() {
		$instance = $this->getInstance( $this->getTitle() );
		$this->assertInstanceOf( $this->getClass(), $instance );
	}

	/**
	 * Test setSemanticData()
	 *
	 * @dataProvider getDataProvider
	 */
	public function testSetSemanticData( array $setup ) {
		$subobject = $this->getInstance( $this->getTitle() );

		$instance = $subobject->setSemanticData( $setup['identifier'] );
		$this->assertInstanceOf( '\SMWContainerSemanticData', $instance );
	}

	/**
	 * Test getName()
	 *
	 * @dataProvider getDataProvider
	 */
	public function testGetName( array $setup, array $expected ) {
		$subobject = $this->getInstance( $this->getTitle(), $setup['identifier'] );
		$this->assertEquals( $expected['name'], $subobject->getName() );
	}

	/**
	 * Test getProperty()
	 *
	 * @dataProvider getDataProvider
	 */
	public function testGetProperty( array $setup ) {
		$subobject = $this->getInstance( $this->getTitle(), $setup['identifier'] );
		$this->assertInstanceOf( '\SMWDIProperty', $subobject->getProperty() );
	}

	/**
	 * Test addPropertyValueString() exception
	 *
	 * @dataProvider getDataProvider
	 */
	public function testAddPropertyValueStringException( array $setup ) {
		$this->setExpectedException( 'MWException' );
		$subobject = $this->getInstance( $this->getTitle() );

		foreach ( $setup['property'] as $property => $value ){
			$subobject->addPropertyValue( $property, $value );
		}
	}

	/**
	 * Test addPropertyValue()
	 *
	 * @dataProvider getDataProvider
	 */
	public function testAddPropertyValue( array $setup, array $expected ) {
		$subobject = $this->getInstance( $this->getTitle(), $setup['identifier'] );

		foreach ( $setup['property'] as $property => $value ){
			$subobject->addPropertyValue( $property, $value );
		}

		// Check errors
		$this->assertCount( $expected['errors'], $subobject->getErrors() );

		$this->assertInstanceOf( 'SMWSemanticData', $subobject->getSemanticData() );
		$this->assertSemanticData( $subobject->getSemanticData(), $expected );
	}

	/**
	 * Test getAnonymousIdentifier()
	 *
	 * @dataProvider getDataProvider
	 */
	public function testGetAnonymousIdentifier( array $setup ) {
		$subobject = $this->getInstance( $this->getTitle() );
		// Looking for the _ instead of comparing the hash key as it
		// can change with the method applied (md4, sha1 etc.)
		$this->assertContains( '_', $subobject->getAnonymousIdentifier( $setup['identifier'] ) );
	}

	/**
	 * Test getContainer()
	 *
	 * @dataProvider getDataProvider
	 */
	public function testGetContainer( array $setup ) {
		$subobject = $this->getInstance( $this->getTitle(), $setup['identifier'] );
		$this->assertInstanceOf( '\SMWDIContainer', $subobject->getContainer() );
	}
}