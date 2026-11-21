<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 */


namespace Aimeos\Admin\JsonAdm\Order\Base\Service\Attribute;


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

		$this->object = new \Aimeos\Admin\JsonAdm\Order\Base\Service\Attribute\Standard( $this->context, 'order/base/service/attribute' );
		$this->object->setAimeos( \TestHelper::getAimeos() );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		\Aimeos\MShop::cache( false );
		unset( $this->object, $this->view, $this->context );
	}


	public function testPatch()
	{
		$item = $this->getAttribute();
		$saved = null;

		$stub = $this->getManagerMock( ['save'] );
		$stub->expects( $this->once() )->method( 'save' )
			->willReturnCallback( function( $item ) use ( &$saved ) { return $saved = $item; } );

		$params = ['id' => $item->getId()];
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$body = '{"data": {"type": "order/base/service/attribute", "attributes": {
			"order.base.service.attribute.value": "test", "order.base.service.attribute.type": "tx",
			"order.base.service.attribute.code": "TRANSACTIONID", "order.base.service.attribute.parentid": "-1"
		}}}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->patch( $request, $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertArrayNotHasKey( 'errors', $result );

		$this->assertEquals( 'test', $saved->getValue() );
		$this->assertEquals( $item->getType(), $saved->getType() );
		$this->assertEquals( $item->getCode(), $saved->getCode() );
		$this->assertEquals( $item->getParentId(), $saved->getParentId() );
	}


	public function testPost()
	{
		$item = $this->getAttribute();
		$service = \Aimeos\MShop::create( $this->context, 'order/base/service' )->get( $item->getParentId() );
		$saved = null;

		$stub = $this->getManagerMock( ['save'] );
		$stub->expects( $this->once() )->method( 'save' )
			->willReturnCallback( function( $new ) use ( &$saved, $item ) {
				$saved = $new;
				return $item; // existing item so the client can fetch it afterwards
			} );

		$body = '{"data": {"type": "order/base/service/attribute", "attributes": {
			"order.base.service.attribute.parentid": "' . $service->getId() . '", "order.base.service.attribute.type": "tx",
			"order.base.service.attribute.code": "TRANSACTIONID", "order.base.service.attribute.value": "test"
		}}}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->post( $request, $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 201, $response->getStatusCode() );
		$this->assertArrayNotHasKey( 'errors', $result );

		$this->assertNull( $saved->getId() );
		$this->assertEquals( $service->getId(), $saved->getParentId() );
		$this->assertEquals( $service->getType(), $saved->getType() );
		$this->assertEquals( 'TRANSACTIONID', $saved->getCode() );
		$this->assertEquals( 'test', $saved->getValue() );
	}


	public function testPostNoService()
	{
		$stub = $this->getManagerMock( ['save'] );
		$stub->expects( $this->never() )->method( 'save' );

		$body = '{"data": {"type": "order/base/service/attribute", "attributes": {
			"order.base.service.attribute.parentid": "-1", "order.base.service.attribute.code": "test"
		}}}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->post( $request, $this->view->response() );

		$this->assertEquals( 404, $response->getStatusCode() );
	}


	public function testDeleteEditor()
	{
		$stub = $this->getManagerMock( ['delete'] );
		$stub->expects( $this->once() )->method( 'delete' );

		$this->view->addHelper( 'access', new \Aimeos\Base\View\Helper\Access\Standard( $this->view, ['editor'] ) );
		$this->context->setView( $this->view );

		$params = ['id' => '-1'];
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->delete( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertArrayNotHasKey( 'errors', $result );
	}


	protected function getManagerMock( array $methods )
	{
		$this->context->config()->set( 'mshop/order/manager/base/service/attribute/name', 'Standard' );

		$stub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Base\\Service\\Attribute\\Standard' )
			->setConstructorArgs( [$this->context] )
			->onlyMethods( $methods )
			->getMock();

		\Aimeos\MShop::inject( '\\Aimeos\\MShop\\Order\\Manager\\Base\\Service\\Attribute\\Standard', $stub );

		return $stub;
	}


	protected function getAttribute() : \Aimeos\MShop\Order\Item\Base\Service\Attribute\Iface
	{
		$manager = \Aimeos\MShop::create( $this->context, 'order/base/service/attribute' );
		$filter = $manager->filter()->add( 'order.base.service.attribute.code', '==', 'ACOWNER' )->slice( 0, 1 );

		return $manager->search( $filter )->first( new \RuntimeException( 'No order service attribute found' ) );
	}
}
