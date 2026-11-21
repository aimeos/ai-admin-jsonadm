<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 */


namespace Aimeos\Admin\JsonAdm\Order\Product;


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

		$this->object = new \Aimeos\Admin\JsonAdm\Order\Product\Standard( $this->context, 'order/product' );
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
		$item = $this->getOrderProduct();
		$saved = null;

		$stub = $this->getManagerMock( ['save'] );
		$stub->expects( $this->once() )->method( 'save' )
			->willReturnCallback( function( $item ) use ( &$saved ) { return $saved = $item; } );

		$params = ['id' => $item->getId()];
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$body = '{"data": {"type": "order/product", "attributes": {
			"order.product.statusdelivery": 5, "order.product.notes": "test",
			"order.product.price": "0.01", "order.product.costs": "-10.00", "order.product.quantity": 100,
			"order.product.name": "test", "order.product.prodcode": "test", "order.product.parentid": "-1"
		}}}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->patch( $request, $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertArrayNotHasKey( 'errors', $result );

		$this->assertEquals( 5, $saved->getStatusDelivery() );
		$this->assertEquals( 'test', $saved->getNotes() );
		$this->assertEquals( $item->getPrice()->getValue(), $saved->getPrice()->getValue() );
		$this->assertEquals( $item->getPrice()->getCosts(), $saved->getPrice()->getCosts() );
		$this->assertEquals( $item->getQuantity(), $saved->getQuantity() );
		$this->assertEquals( $item->getName(), $saved->getName() );
		$this->assertEquals( $item->getProductCode(), $saved->getProductCode() );
		$this->assertEquals( $item->getParentId(), $saved->getParentId() );
	}


	public function testPost()
	{
		$stub = $this->getManagerMock( ['save'] );
		$stub->expects( $this->never() )->method( 'save' );

		$body = '{"data": {"type": "order/product", "attributes": {
			"order.product.parentid": "' . $this->getOrderProduct()->getParentId() . '",
			"order.product.prodcode": "test", "order.product.price": "-100.00", "order.product.quantity": 1
		}}}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->post( $request, $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 403, $response->getStatusCode() );
		$this->assertArrayHasKey( 'errors', $result );
	}


	public function testPostBulk()
	{
		$stub = $this->getManagerMock( ['save'] );
		$stub->expects( $this->never() )->method( 'save' );

		$body = '{"data": [{"type": "order/product", "attributes": {
			"order.product.parentid": "' . $this->getOrderProduct()->getParentId() . '", "order.product.prodcode": "test"
		}}]}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->post( $request, $this->view->response() );

		$this->assertEquals( 403, $response->getStatusCode() );
	}


	public function testDelete()
	{
		$stub = $this->getManagerMock( ['delete'] );
		$stub->expects( $this->never() )->method( 'delete' );

		$this->view->addHelper( 'access', new \Aimeos\Base\View\Helper\Access\Standard( $this->view, ['admin'] ) );
		$this->context->setView( $this->view );

		$params = ['id' => $this->getOrderProduct()->getId()];
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->delete( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 403, $response->getStatusCode() );
		$this->assertArrayHasKey( 'errors', $result );
	}


	protected function getManagerMock( array $methods )
	{
		$this->context->config()->set( 'mshop/order/manager/product/name', 'Standard' );

		$stub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Product\\Standard' )
			->setConstructorArgs( [$this->context] )
			->onlyMethods( $methods )
			->getMock();

		\Aimeos\MShop::inject( '\\Aimeos\\MShop\\Order\\Manager\\Product\\Standard', $stub );

		return $stub;
	}


	protected function getOrderProduct() : \Aimeos\MShop\Order\Item\Product\Iface
	{
		$manager = \Aimeos\MShop::create( $this->context, 'order/product' );
		$filter = $manager->filter()->add( 'order.product.prodcode', '==', 'CNE' )->slice( 0, 1 );

		return $manager->search( $filter )->first( new \RuntimeException( 'No ordered product found' ) );
	}
}
