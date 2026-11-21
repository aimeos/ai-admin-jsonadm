<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 */


namespace Aimeos\Admin\JsonAdm\Order\Base\Service;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp() : void
	{
		\Aimeos\MShop::cache( true );

		$this->context = \TestHelper::context();
		$this->view = $this->context->view();

		$this->object = new \Aimeos\Admin\JsonAdm\Order\Base\Service\Standard( $this->context, 'order/base/service' );
		$this->object->setAimeos( \TestHelper::getAimeos() );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		\Aimeos\MShop::cache( false );
		unset( $this->object, $this->view, $this->context );
	}


	public function testDelete()
	{
		$stub = $this->getManagerMock( ['delete'] );
		$stub->expects( $this->once() )->method( 'delete' );

		$params = ['id' => '-1'];
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->delete( $this->view->request(), $this->view->response() );

		$this->assertEquals( 200, $response->getStatusCode() );
	}


	public function testDeleteEditor()
	{
		$stub = $this->getManagerMock( ['delete'] );
		$stub->expects( $this->never() )->method( 'delete' );

		$this->setAccess( ['admin', 'editor'] );

		$params = ['id' => '-1'];
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->delete( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 403, $response->getStatusCode() );
		$this->assertArrayHasKey( 'errors', $result );
	}


	public function testPatchEditor()
	{
		$stub = $this->getManagerMock( ['save'] );
		$stub->expects( $this->never() )->method( 'save' );

		$this->setAccess( ['admin', 'editor'] );

		$params = ['id' => '-1'];
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$body = '{"data": {"type": "order/base/service", "attributes": {"order.base.service.price": "0.01"}}}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->patch( $request, $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 403, $response->getStatusCode() );
		$this->assertArrayHasKey( 'errors', $result );
	}


	public function testPostEditor()
	{
		$stub = $this->getManagerMock( ['save'] );
		$stub->expects( $this->never() )->method( 'save' );

		$this->setAccess( ['admin', 'editor'] );

		$body = '{"data": [{"type": "order/base/service", "attributes": {"order.base.service.price": "-100.00"}}]}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->post( $request, $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 403, $response->getStatusCode() );
		$this->assertArrayHasKey( 'errors', $result );
	}


	protected function getManagerMock( array $methods )
	{
		$this->context->config()->set( 'mshop/order/manager/base/service/name', 'Standard' );
		\Aimeos\MShop::create( $this->context, 'order/base/service' ); // clears cached managers on context change

		$stub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Base\\Service\\Standard' )
			->setConstructorArgs( [$this->context] )
			->onlyMethods( $methods )
			->getMock();

		\Aimeos\MShop::inject( '\\Aimeos\\MShop\\Order\\Manager\\Base\\Service\\Standard', $stub );

		return $stub;
	}


	protected function setAccess( array $groups )
	{
		$this->view->addHelper( 'access', new \Aimeos\Base\View\Helper\Access\Standard( $this->view, $groups ) );
		$this->context->setView( $this->view );
	}
}
