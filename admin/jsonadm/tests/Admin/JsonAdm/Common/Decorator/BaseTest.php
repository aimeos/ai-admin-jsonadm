<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\Admin\JsonAdm\Common\Decorator;


class BaseTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $stub;
	private $view;


	protected function setUp()
	{
		$context = \TestHelperJadm::getContext();
		$this->view = $context->getView();

		$this->stub = $this->getMockBuilder( '\\Aimeos\\Admin\\JsonAdm\\Standard' )
			->setConstructorArgs( array( $context, $this->view, array(), 'attribute' ) )
			->getMock();

		$this->object = new TestBase( $this->stub, $context, $this->view, array(), 'attribute' );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->stub );
	}


	public function testDelete()
	{
		$this->stub->expects( $this->once() )->method( 'delete' )->will( $this->returnArgument( 1 ) );
		$response = $this->view->response();

		$this->assertSame( $response, $this->object->delete( $this->view->request(), $response ) );
	}


	public function testGet()
	{
		$this->stub->expects( $this->once() )->method( 'get' )->will( $this->returnArgument( 1 ) );
		$response = $this->view->response();

		$this->assertSame( $response, $this->object->get( $this->view->request(), $response ) );
	}


	public function testPatch()
	{
		$this->stub->expects( $this->once() )->method( 'patch' )->will( $this->returnArgument( 1 ) );
		$response = $this->view->response();

		$this->assertSame( $response, $this->object->patch( $this->view->request(), $response ) );
	}


	public function testPost()
	{
		$this->stub->expects( $this->once() )->method( 'post' )->will( $this->returnArgument( 1 ) );
		$response = $this->view->response();

		$this->assertSame( $response, $this->object->post( $this->view->request(), $response ) );
	}


	public function testPut()
	{
		$this->stub->expects( $this->once() )->method( 'put' )->will( $this->returnArgument( 1 ) );
		$response = $this->view->response();

		$this->assertSame( $response, $this->object->put( $this->view->request(), $response ) );
	}


	public function testOptions()
	{
		$this->stub->expects( $this->once() )->method( 'options' )->will( $this->returnArgument( 1 ) );
		$response = $this->view->response();

		$this->assertSame( $response, $this->object->options( $this->view->request(), $response ) );
	}


	public function testGetContext()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Context\\Item\\Iface', $this->object->getContextPublic() );
	}


	public function testGetTemplatePaths()
	{
		$this->assertEquals( array(), $this->object->getTemplatePathsPublic() );
	}


	public function testGetPath()
	{
		$this->assertEquals( 'attribute', $this->object->getPathPublic() );
	}


	public function testCall()
	{
		$this->markTestIncomplete( 'PHP warning is triggered instead of exception' );
	}

}


class TestBase
	extends \Aimeos\Admin\JsonAdm\Common\Decorator\Base
{
	public function getContextPublic()
	{
		return $this->getContext();
	}

	public function getTemplatePathsPublic()
	{
		return $this->getTemplatePaths();
	}

	public function getPathPublic()
	{
		return $this->getPath();
	}
}
