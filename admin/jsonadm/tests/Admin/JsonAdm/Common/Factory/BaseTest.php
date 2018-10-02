<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\Admin\JsonAdm\Common\Factory;


class BaseTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp()
	{
		$this->context = \TestHelperJadm::getContext();
		$this->view = $this->context->getView();

		$this->client = new \Aimeos\Admin\JsonAdm\Product\Standard( $this->context, $this->view, [], '' );

		$this->object = $this->getMockBuilder( '\Aimeos\Admin\JsonAdm\Common\Factory\Base' )
			->getMockForAbstractClass();
	}


	public function testinjectClient()
	{
		$this->object->injectClient( 'test', $this->client );
	}


	public function testAddClientDecorators()
	{
		$config = $this->context->getConfig();
		$config->set( 'client/jsonapi/common/decorators/default', ['Test'] );
		$config->set( 'client/jsonapi/product/decorators/excludes', ['Test'] );

		$params = [$this->client, $this->context, $this->view, [], 'product'];
		$result = $this->access( 'addClientDecorators' )->invokeArgs( $this->object, $params );

		$this->assertInstanceOf( '\Aimeos\\Admin\\JsonAdm\\Iface', $result );
	}


	public function testAddDecorators()
	{
		$prefix = '\Aimeos\\Admin\\JsonAdm\\Common\\Decorator\\';
		$params = [$this->client, ['Example'], $prefix, $this->context, $this->view, [], ''];

		$result = $this->access( 'addDecorators' )->invokeArgs( $this->object, $params );

		$this->assertInstanceOf( '\Aimeos\\Admin\\JsonAdm\\Iface', $result );
	}


	public function testAddDecoratorsInvalidClass()
	{
		$prefix = '\Aimeos\\Admin\\JsonAdm\\Common\\Decorator\\';
		$params = [$this->client, ['Test'], $prefix, $this->context, $this->view, [], ''];

		$this->setExpectedException( '\Aimeos\Admin\JsonAdm\Exception' );
		$this->access( 'addDecorators' )->invokeArgs( $this->object, $params );
	}


	public function testAddDecoratorsInvalidName()
	{
		$prefix = '\Aimeos\\Admin\\JsonAdm\\Common\\Decorator\\';
		$params = [$this->client, [''], $prefix, $this->context, $this->view, [], ''];

		$this->setExpectedException( '\Aimeos\Admin\JsonAdm\Exception' );
		$this->access( 'addDecorators' )->invokeArgs( $this->object, $params );
	}


	public function testCreateClientBase()
	{
		$iface = '\Aimeos\\Admin\\JsonAdm\\Iface';
		$class = '\Aimeos\\Admin\\JsonAdm\\Product\\Standard';
		$params = [$class, $iface, $this->context, $this->view, [], ''];

		$result = $this->access( 'createClientBase' )->invokeArgs( $this->object, $params );

		$this->assertInstanceOf( '\Aimeos\\Admin\\JsonAdm\\Iface', $result );
	}


	public function testCreateClientBaseCache()
	{
		$iface = '\Aimeos\\Admin\\JsonAdm\\Iface';
		$params = ['test', $iface, $this->context, $this->view, [], ''];

		$this->object->injectClient( 'test', $this->client );
		$result = $this->access( 'createClientBase' )->invokeArgs( $this->object, $params );

		$this->assertSame( $this->client, $result );
	}


	public function testCreateClientBaseInvalidClass()
	{
		$iface = '\Aimeos\\Admin\\JsonAdm\\Iface';
		$params = ['invalid', $iface, $this->context, $this->view, [], ''];

		$this->setExpectedException( '\Aimeos\Admin\JsonAdm\Exception' );
		$this->access( 'createClientBase' )->invokeArgs( $this->object, $params );
	}


	public function testCreateClientBaseInvalidIface()
	{
		$iface = '\Aimeos\\Admin\\JsonAdm\\Common\\Decorator\\Iface';
		$class = '\Aimeos\\Admin\\JsonAdm\\Product\\Standard';
		$params = [$class, $iface, $this->context, $this->view, [], ''];

		$this->setExpectedException( '\Aimeos\MW\Common\Exception' );
		$this->access( 'createClientBase' )->invokeArgs( $this->object, $params );
	}


	protected function access( $name )
	{
		$class = new \ReflectionClass( '\Aimeos\Admin\JsonAdm\Common\Factory\Base' );
		$method = $class->getMethod( $name );
		$method->setAccessible( true );

		return $method;
	}
}