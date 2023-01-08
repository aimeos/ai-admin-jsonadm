<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Admin;


class JsonAdmTest extends \PHPUnit\Framework\TestCase
{
	public function testCreateClient()
	{
		$context = \TestHelper::context();
		$aimeos = \TestHelper::getAimeos();

		$client = \Aimeos\Admin\JsonAdm::create( $context, $aimeos, 'order' );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Iface', $client );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Order\\Standard', $client );
	}


	public function testCreateSubClient()
	{
		$context = \TestHelper::context();
		$aimeos = \TestHelper::getAimeos();

		$client = \Aimeos\Admin\JsonAdm::create( $context, $aimeos, 'coupon/config' );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Iface', $client );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Coupon\\Config\\Standard', $client );
	}


	public function testCreateStandard()
	{
		$context = \TestHelper::context();
		$aimeos = \TestHelper::getAimeos();

		$client = \Aimeos\Admin\JsonAdm::create( $context, $aimeos, 'stock/type' );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Iface', $client );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Standard', $client );
	}


	public function testCreateClientEmpty()
	{
		$context = \TestHelper::context();
		$aimeos = \TestHelper::getAimeos();

		$client = \Aimeos\Admin\JsonAdm::create( $context, $aimeos, '/' );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Iface', $client );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Standard', $client );
	}


	public function testCreateClientInvalidPath()
	{
		$context = \TestHelper::context();
		$aimeos = \TestHelper::getAimeos();

		$this->expectException( '\\Aimeos\\Admin\\JsonAdm\\Exception' );
		\Aimeos\Admin\JsonAdm::create( $context, $aimeos, '%^' );
	}


	public function testCreateClientInvalidName()
	{
		$context = \TestHelper::context();
		$aimeos = \TestHelper::getAimeos();

		$this->expectException( '\\Aimeos\\Admin\\JsonAdm\\Exception' );
		\Aimeos\Admin\JsonAdm::create( $context, $aimeos, '', '%^' );
	}
}
