<?php

namespace SMW\Tests\SQLStore\QueryEngine;

use SMW\SQLStore\QueryEngine\QuerySegmentListItemResolver;

/**
 * @covers \SMW\SQLStore\QueryEngine\QuerySegmentListItemResolver
 * @group semantic-mediawiki
 *
 * @license GNU GPL v2+
 * @since 2.2
 *
 * @author mwjames
 */
class QuerySegmentListItemResolverTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$connection = $this->getMockBuilder( '\SMW\MediaWiki\Database' )
			->disableOriginalConstructor()
			->getMock();

		$temporaryIdTableCreator = $this->getMockBuilder( '\SMW\SQLStore\TemporaryIdTableCreator' )
			->disableOriginalConstructor()
			->getMock();

		$resolverOptions = $this->getMockBuilder( '\SMW\SQLStore\QueryEngine\ResolverOptions' )
			->disableOriginalConstructor()
			->getMock();

		$this->assertInstanceOf(
			'\SMW\SQLStore\QueryEngine\QuerySegmentListItemResolver',
			new QuerySegmentListItemResolver( $connection, $temporaryIdTableCreator, $resolverOptions )
		);
	}

	public function testTryResolveSegmentForInvalidIdThrowsException() {

		$connection = $this->getMockBuilder( '\SMW\MediaWiki\Database' )
			->disableOriginalConstructor()
			->getMock();

		$temporaryIdTableCreator = $this->getMockBuilder( '\SMW\SQLStore\TemporaryIdTableCreator' )
			->disableOriginalConstructor()
			->getMock();

		$resolverOptions = $this->getMockBuilder( '\SMW\SQLStore\QueryEngine\ResolverOptions' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new QuerySegmentListItemResolver(
			$connection,
			$temporaryIdTableCreator,
			$resolverOptions
		);

		$this->setExpectedException( 'RuntimeException' );
		$instance->resolveForSegmentItem( 42 );
	}

}
