<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Admin\JsonAdm\Common\Factory;


class BaseTest extends \PHPUnit\Framework\TestCase
{
	private $client;
	private $context;
	private $object;
	private $view;


	protected function setUp() : void
	{
		$this->context = \TestHelperJadm::getContext();
		$this->view = $this->context->view();

		$this->client = new \Aimeos\Admin\JsonAdm\Product\Standard( $this->context, '' );

		$this->object = $this->getMockBuilder( \Aimeos\Admin\JsonAdm\Common\Factory\Base::class )
			->getMockForAbstractClass();
	}


	public function testInjectClient()
	{
		$this->object->injectClient( 'test', $this->client );
	}


	public function testClientDecorator()
	{
		$this->context->getConfig()->set( 'admin/jsonadm/common/decorators/default', ['Example'] );

		$result = \Aimeos\Admin\JsonAdm::create( $this->context, \TestHelperJadm::getAimeos(), 'product' );

		$this->assertInstanceOf( '\Aimeos\Admin\JsonAdm\Iface', $result );
		$this->assertInstanceOf( '\Aimeos\Admin\JsonAdm\Common\Decorator\Iface', $result );
		$this->assertEquals( 'Aimeos\Admin\JsonAdm\Common\Decorator\Example', get_class( $result ) );
	}


	public function testAddClientDecorators()
	{
		$config = $this->context->getConfig();
		$config->set( 'admin/jsonadm/common/decorators/default', ['Test'] );
		$config->set( 'admin/jsonadm/product/decorators/excludes', ['Test'] );

		$params = [$this->client, $this->context, 'product'];
		$result = $this->access( 'addClientDecorators' )->invokeArgs( $this->object, $params );

		$this->assertInstanceOf( '\Aimeos\\Admin\\JsonAdm\\Iface', $result );
	}


	public function testAddDecorators()
	{
		$prefix = '\Aimeos\\Admin\\JsonAdm\\Common\\Decorator\\';
		$params = [$this->client, ['Example'], $prefix, $this->context, ''];

		$result = $this->access( 'addDecorators' )->invokeArgs( $this->object, $params );

		$this->assertInstanceOf( '\Aimeos\\Admin\\JsonAdm\\Iface', $result );
	}


	public function testAddDecoratorsInvalidClass()
	{
		$prefix = '\Aimeos\\Admin\\JsonAdm\\Common\\Decorator\\';
		$params = [$this->client, ['Test'], $prefix, $this->context, ''];

		$this->expectException( \Aimeos\Admin\JsonAdm\Exception::class );
		$this->access( 'addDecorators' )->invokeArgs( $this->object, $params );
	}


	public function testAddDecoratorsInvalidName()
	{
		$prefix = '\Aimeos\\Admin\\JsonAdm\\Common\\Decorator\\';
		$params = [$this->client, [''], $prefix, $this->context, ''];

		$this->expectException( \Aimeos\Admin\JsonAdm\Exception::class );
		$this->access( 'addDecorators' )->invokeArgs( $this->object, $params );
	}


	public function testCreateClientBase()
	{
		$iface = '\Aimeos\\Admin\\JsonAdm\\Iface';
		$class = '\Aimeos\\Admin\\JsonAdm\\Product\\Standard';
		$params = [$class, $iface, $this->context, ''];

		$result = $this->access( 'createAdmin' )->invokeArgs( $this->object, $params );

		$this->assertInstanceOf( '\Aimeos\\Admin\\JsonAdm\\Iface', $result );
	}


	public function testCreateClientBaseCache()
	{
		$iface = '\Aimeos\\Admin\\JsonAdm\\Iface';
		$params = ['test', $iface, $this->context, ''];

		$this->object->injectClient( 'test', $this->client );
		$result = $this->access( 'createAdmin' )->invokeArgs( $this->object, $params );

		$this->assertSame( $this->client, $result );
	}


	public function testCreateClientBaseInvalidClass()
	{
		$iface = '\Aimeos\\Admin\\JsonAdm\\Iface';
		$params = ['invalid', $iface, $this->context, ''];

		$this->expectException( \Aimeos\Admin\JsonAdm\Exception::class );
		$this->access( 'createAdmin' )->invokeArgs( $this->object, $params );
	}


	public function testCreateClientBaseInvalidIface()
	{
		$iface = '\Aimeos\\Admin\\JsonAdm\\Common\\Decorator\\Iface';
		$class = '\Aimeos\\Admin\\JsonAdm\\Product\\Standard';
		$params = [$class, $iface, $this->context, ''];

		$this->expectException( \Aimeos\MW\Common\Exception::class );
		$this->access( 'createAdmin' )->invokeArgs( $this->object, $params );
	}


	protected function access( $name )
	{
		$class = new \ReflectionClass( \Aimeos\Admin\JsonAdm\Common\Factory\Base::class );
		$method = $class->getMethod( $name );
		$method->setAccessible( true );

		return $method;
	}
}
