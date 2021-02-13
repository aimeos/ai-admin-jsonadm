<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Admin;


class JsonAdmTest extends \PHPUnit\Framework\TestCase
{
	public function testCreateClient()
	{
		$context = \TestHelperJadm::getContext();
		$aimeos = \TestHelperJadm::getAimeos();

		$client = \Aimeos\Admin\JsonAdm::create( $context, $aimeos, 'order' );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Iface', $client );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Order\\Standard', $client );
	}


	public function testCreateSubClient()
	{
		$context = \TestHelperJadm::getContext();
		$aimeos = \TestHelperJadm::getAimeos();

		$client = \Aimeos\Admin\JsonAdm::create( $context, $aimeos, 'order/base' );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Iface', $client );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Order\\Base\\Standard', $client );
	}


	public function testCreateStandard()
	{
		$context = \TestHelperJadm::getContext();
		$aimeos = \TestHelperJadm::getAimeos();

		$client = \Aimeos\Admin\JsonAdm::create( $context, $aimeos, 'stock/type' );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Iface', $client );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Standard', $client );
	}


	public function testCreateClientEmpty()
	{
		$context = \TestHelperJadm::getContext();
		$aimeos = \TestHelperJadm::getAimeos();

		$client = \Aimeos\Admin\JsonAdm::create( $context, $aimeos, '/' );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Iface', $client );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Standard', $client );
	}


	public function testCreateClientInvalidPath()
	{
		$context = \TestHelperJadm::getContext();
		$aimeos = \TestHelperJadm::getAimeos();

		$this->expectException( '\\Aimeos\\Admin\\JsonAdm\\Exception' );
		\Aimeos\Admin\JsonAdm::create( $context, $aimeos, '%^' );
	}


	public function testCreateClientInvalidName()
	{
		$context = \TestHelperJadm::getContext();
		$aimeos = \TestHelperJadm::getAimeos();

		$this->expectException( '\\Aimeos\\Admin\\JsonAdm\\Exception' );
		\Aimeos\Admin\JsonAdm::create( $context, $aimeos, '', '%^' );
	}
}
