<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\Admin\JsonAdm;


class FactoryTest extends \PHPUnit\Framework\TestCase
{
	public function testCreateClient()
	{
		$context = \TestHelperJadm::getContext();
		$templatePaths = \TestHelperJadm::getJsonadmPaths();

		$client = \Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, 'order' );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Common\\Iface', $client );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Order\\Standard', $client );
	}


	public function testCreateSubClient()
	{
		$context = \TestHelperJadm::getContext();
		$templatePaths = \TestHelperJadm::getJsonadmPaths();

		$client = \Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, 'order/base' );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Common\\Iface', $client );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Order\\Base\\Standard', $client );
	}


	public function testCreateStandard()
	{
		$context = \TestHelperJadm::getContext();
		$templatePaths = \TestHelperJadm::getJsonadmPaths();

		$client = \Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, 'stock/type' );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Common\\Iface', $client );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Standard', $client );
	}


	public function testCreateClientEmpty()
	{
		$context = \TestHelperJadm::getContext();
		$templatePaths = \TestHelperJadm::getJsonadmPaths();

		$client = \Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, '' );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Common\\Iface', $client );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Standard', $client );
	}


	public function testCreateClientInvalidPath()
	{
		$context = \TestHelperJadm::getContext();
		$templatePaths = \TestHelperJadm::getJsonadmPaths();

		$this->setExpectedException( '\\Aimeos\\Admin\\JsonAdm\\Exception' );
		\Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, '%^' );
	}


	public function testCreateClientInvalidName()
	{
		$context = \TestHelperJadm::getContext();
		$templatePaths = \TestHelperJadm::getJsonadmPaths();

		$this->setExpectedException( '\\Aimeos\\Admin\\JsonAdm\\Exception' );
		\Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, '', '%^' );
	}


	public function testClear()
	{
		$cache = \Aimeos\Admin\JsonAdm\Factory::setCache( true );

		$context = \TestHelperJadm::getContext();
		$templatePaths = \TestHelperJadm::getJsonadmPaths();

		$client1 = \Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, 'order' );
		\Aimeos\Admin\JsonAdm\Factory::clear();
		$client2 = \Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, 'order' );

		\Aimeos\Admin\JsonAdm\Factory::setCache( $cache );

		$this->assertNotSame( $client1, $client2 );
	}


	public function testClearSite()
	{
		$cache = \Aimeos\Admin\JsonAdm\Factory::setCache( true );

		$context = \TestHelperJadm::getContext();
		$templatePaths = \TestHelperJadm::getJsonadmPaths();

		$cntlA1 = \Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, 'order' );
		$cntlB1 = \Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, 'order/base' );
		\Aimeos\Admin\JsonAdm\Factory::clear( (string) $context );

		$cntlA2 = \Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, 'order' );
		$cntlB2 = \Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, 'order/base' );

		\Aimeos\Admin\JsonAdm\Factory::setCache( $cache );

		$this->assertNotSame( $cntlA1, $cntlA2 );
		$this->assertNotSame( $cntlB1, $cntlB2 );
	}


	public function testClearSpecific()
	{
		$cache = \Aimeos\Admin\JsonAdm\Factory::setCache( true );

		$context = \TestHelperJadm::getContext();
		$templatePaths = \TestHelperJadm::getJsonadmPaths();

		$cntlA1 = \Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, 'order' );
		$cntlB1 = \Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, 'order/base' );

		\Aimeos\Admin\JsonAdm\Factory::clear( (string) $context, 'order' );

		$cntlA2 = \Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, 'order' );
		$cntlB2 = \Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, 'order/base' );

		\Aimeos\Admin\JsonAdm\Factory::setCache( $cache );

		$this->assertNotSame( $cntlA1, $cntlA2 );
		$this->assertSame( $cntlB1, $cntlB2 );
	}

}