<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Admin\JsonAdm;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp() : void
	{
		\Aimeos\MShop::cache( true );

		$this->context = \TestHelperJadm::getContext();
		$this->view = $this->context->getView();

		$this->object = new \Aimeos\Admin\JsonAdm\Standard( $this->context, 'product' );
		$this->object->setAimeos( \TestHelperJadm::getAimeos() );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		\Aimeos\MShop::cache( false );
	}


	public function testDelete()
	{
		$this->getProductMock( array( 'delete' ) )->expects( $this->once() )->method( 'delete' );

		$params = array( 'id' => $this->getProductItem()->getId() );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->delete( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 1, $result['meta']['total'] );

		$this->assertArrayNotHasKey( 'included', $result );
		$this->assertArrayNotHasKey( 'data', $result );
		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testDeleteBulk()
	{
		$this->getProductMock( array( 'delete' ) )->expects( $this->once() )->method( 'delete' );

		$body = '{"data":[{"type": "product", "id": "-1"},{"type": "product", "id": "-2"}]}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->delete( $request, $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 2, $result['meta']['total'] );
		$this->assertArrayNotHasKey( 'included', $result );
		$this->assertArrayNotHasKey( 'data', $result );
		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testDeleteInvalid()
	{
		$body = '{"data":null}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->delete( $request, $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 400, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 0, $result['meta']['total'] );
		$this->assertArrayHasKey( 'errors', $result );
		$this->assertArrayNotHasKey( 'included', $result );
		$this->assertArrayNotHasKey( 'data', $result );
	}


	public function testDeleteException()
	{
		$this->getProductMock( array( 'delete' ) )->expects( $this->once() )->method( 'delete' )
			->will( $this->throwException( new \RuntimeException( 'test exception' ) ) );

		$params = array( 'id' => $this->getProductItem()->getId() );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->delete( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 500, $response->getStatusCode() );
		$this->assertArrayHasKey( 'errors', $result );
	}


	public function testDeleteMShopException()
	{
		$this->getProductMock( array( 'delete' ) )->expects( $this->once() )->method( 'delete' )
			->will( $this->throwException( new \Aimeos\MShop\Exception( 'test exception' ) ) );

		$params = array( 'id' => $this->getProductItem()->getId() );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->delete( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 404, $response->getStatusCode() );
		$this->assertArrayHasKey( 'errors', $result );
	}


	public function testGet()
	{
		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 28, $result['meta']['total'] );
		$this->assertEquals( 25, count( $result['data'] ) );
		$this->assertEquals( 'product', $result['data'][0]['type'] );
		$this->assertEquals( 0, count( $result['included'] ) );
		$this->assertArrayHasKey( 'next', $result['links'] );
		$this->assertArrayHasKey( 'last', $result['links'] );
		$this->assertArrayHasKey( 'self', $result['links'] );
		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testGetType()
	{
		$object = new \Aimeos\Admin\JsonAdm\Standard( $this->context, 'product/property/type' );
		$object->setView( $this->view );

		$response = $object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 4, $result['meta']['total'] );
		$this->assertEquals( 4, count( $result['data'] ) );
		$this->assertEquals( 'product/property/type', $result['data'][0]['type'] );
		$this->assertEquals( 0, count( $result['included'] ) );

		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testGetInvalid()
	{
		$object = new \Aimeos\Admin\JsonAdm\Standard( $this->context, 'invalid' );
		$object->setView( $this->view );

		$response = $object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 404, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 1, count( $result['errors'] ) );
		$this->assertArrayHasKey( 'title', $result['errors'][0] );
		$this->assertArrayHasKey( 'detail', $result['errors'][0] );
		$this->assertArrayNotHasKey( 'data', $result );
		$this->assertArrayNotHasKey( 'indluded', $result );
	}


	public function testGetException()
	{
		$this->getProductMock( ['get'] )->expects( $this->once() )->method( 'get' )
			->will( $this->throwException( new \RuntimeException( 'test exception' ) ) );

		$params = array( 'id' => -1 );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 500, $response->getStatusCode() );
		$this->assertArrayHasKey( 'errors', $result );
	}


	public function testGetMShopException()
	{
		$this->getProductMock( ['get'] )->expects( $this->once() )->method( 'get' )
			->will( $this->throwException( new \Aimeos\MShop\Exception( 'test exception' ) ) );

		$params = array( 'id' => -1 );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 404, $response->getStatusCode() );
		$this->assertArrayHasKey( 'errors', $result );
	}


	public function testGetFilter()
	{
		$params = array(
			'filter' => array(
				'==' => array( 'product.type' => 'select' )
			)
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 3, $result['meta']['total'] );
		$this->assertEquals( 3, count( $result['data'] ) );
		$this->assertEquals( 'product', $result['data'][0]['type'] );
		$this->assertEquals( 0, count( $result['included'] ) );

		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testGetFilterCombine()
	{
		$params = array(
			'filter' => array(
				'&&' => array(
					array( '=~' => array( 'product.label' => 'Unittest: Test' ) ),
					array( '==' => array( 'product.type' => 'select' ) ),
				)
			)
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 2, $result['meta']['total'] );
		$this->assertEquals( 2, count( $result['data'] ) );
		$this->assertEquals( 'product', $result['data'][0]['type'] );
		$this->assertEquals( 0, count( $result['included'] ) );

		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testGetPage()
	{
		$params = array(
			'page' => array(
				'offset' => 25,
				'limit' => 25
			)
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 28, $result['meta']['total'] );
		$this->assertEquals( 3, count( $result['data'] ) );
		$this->assertEquals( 'product', $result['data'][0]['type'] );
		$this->assertEquals( 0, count( $result['included'] ) );
		$this->assertArrayHasKey( 'first', $result['links'] );
		$this->assertArrayHasKey( 'prev', $result['links'] );
		$this->assertArrayHasKey( 'self', $result['links'] );

		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testGetSort()
	{
		$params = array(
			'sort' => 'product.label'
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 28, $result['meta']['total'] );
		$this->assertEquals( 25, count( $result['data'] ) );
		$this->assertEquals( 'product', $result['data'][0]['type'] );
		$this->assertEquals( 'ABCD', $result['data'][0]['attributes']['product.code'] );
		$this->assertEquals( 'ABCD/16 discs', $result['data'][0]['attributes']['product.label'] );
		$this->assertEquals( 0, count( $result['included'] ) );

		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testGetFields()
	{
		$params = array(
			'fields' => array(
				'product' => 'product.id,product.label'
			),
			'sort' => 'product.id',
			'include' => 'product'
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 28, $result['meta']['total'] );
		$this->assertEquals( 25, count( $result['data'] ) );
		$this->assertEquals( 'product', $result['data'][0]['type'] );
		$this->assertEquals( 2, count( $result['data'][0]['attributes'] ) );

		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testPatch()
	{
		$productManagerStub = $this->getProductMock( array( 'get', 'save' ) );

		$item = $productManagerStub->create();
		$item->setLabel( 'test' );
		$item->setId( '-1' );

		$productManagerStub->expects( $this->once() )->method( 'save' )
			->will( $this->returnValue( $item ) );
		$productManagerStub->expects( $this->atLeastOnce() )->method( 'get' )
			->will( $this->returnValue( $item ) );


		$params = array( 'id' => '-1' );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$body = '{"data": {"type": "product", "attributes": {"product.label": "test"}}}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->patch( $request, $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertArrayHasKey( 'data', $result );
		$this->assertEquals( '-1', $result['data']['id'] );
		$this->assertEquals( 'product', $result['data']['type'] );
		$this->assertEquals( 'test', $result['data']['attributes']['product.label'] );

		$this->assertArrayNotHasKey( 'included', $result );
		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testPatchBulk()
	{
		$productManagerStub = $this->getProductMock( array( 'get', 'save' ) );

		$item = $productManagerStub->create();
		$item->setLabel( 'test' );
		$item->setId( '-1' );

		$productManagerStub->expects( $this->exactly( 2 ) )->method( 'save' )
			->will( $this->returnValue( $item ) );
		$productManagerStub->expects( $this->atLeastOnce() )->method( 'get' )
			->will( $this->returnValue( $item ) );


		$body = '{"data": [{"id": "-1", "type": "product", "attributes": {"product.label": "test"}}, {"id": "-1", "type": "product", "attributes": {"product.label": "test"}}]}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->patch( $request, $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 2, $result['meta']['total'] );
		$this->assertArrayHasKey( 'data', $result );
		$this->assertEquals( 2, count( $result['data'] ) );
		$this->assertEquals( '-1', $result['data'][0]['id'] );
		$this->assertEquals( 'product', $result['data'][0]['type'] );
		$this->assertEquals( 'test', $result['data'][0]['attributes']['product.label'] );

		$this->assertArrayNotHasKey( 'included', $result );
		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testPatchInvalid()
	{
		$body = '{"data":null}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->patch( $request, $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 400, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 0, $result['meta']['total'] );
		$this->assertArrayHasKey( 'errors', $result );
		$this->assertArrayNotHasKey( 'included', $result );
		$this->assertArrayNotHasKey( 'data', $result );
	}


	public function testPatchInvalidId()
	{
		$body = '{"data":{"id":-1}}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->patch( $request, $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 400, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 0, $result['meta']['total'] );
		$this->assertArrayHasKey( 'errors', $result );
	}


	public function testPatchException()
	{
		$this->getProductMock( ['get'] )->expects( $this->once() )->method( 'get' )
			->will( $this->throwException( new \RuntimeException( 'test exception' ) ) );

		$body = '{"data":[{"id":-1}]}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->patch( $request, $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 500, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );
		$this->assertArrayHasKey( 'errors', $result );
	}


	public function testPatchMShopException()
	{
		$this->getProductMock( ['get'] )->expects( $this->once() )->method( 'get' )
			->will( $this->throwException( new \Aimeos\MShop\Exception( 'test exception' ) ) );

		$body = '{"data":[{"id":-1}]}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->patch( $request, $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 404, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );
		$this->assertArrayHasKey( 'errors', $result );
	}


	public function testPost()
	{
		$productManagerStub = $this->getProductMock( array( 'create', 'get', 'save' ) );

		$item = new \Aimeos\MShop\Product\Item\Standard();
		$item->setId( '-1' );

		$productManagerStub->expects( $this->once() )->method( 'create' )
			->will( $this->returnValue( $item ) );
		$productManagerStub->expects( $this->any() )->method( 'get' )
			->will( $this->returnValue( $item ) );
		$productManagerStub->expects( $this->once() )->method( 'save' )
			->will( $this->returnValue( $item ) );


		$body = '{"data": {"type": "product", "attributes": {"product.type": "default", "product.label": "test"}}}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->post( $request, $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 201, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertArrayHasKey( 'data', $result );
		$this->assertEquals( '-1', $result['data']['id'] );
		$this->assertEquals( 'product', $result['data']['type'] );
		$this->assertEquals( 'default', $result['data']['attributes']['product.type'] );
		$this->assertEquals( 'test', $result['data']['attributes']['product.label'] );

		$this->assertArrayNotHasKey( 'included', $result );
		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testPostBulk()
	{
		$productManagerStub = $this->getProductMock( array( 'get', 'save' ) );

		$item = $productManagerStub->create();
		$item->setLabel( 'test' );
		$item->setId( '-1' );

		$productManagerStub->expects( $this->exactly( 2 ) )->method( 'save' )
			->will( $this->returnValue( $item ) );
		$productManagerStub->expects( $this->exactly( 2 ) )->method( 'get' )
			->will( $this->returnValue( $item ) );


		$body = '{"data": [{"type": "product", "attributes": {"product.label": "test"}}, {"type": "product", "attributes": {"product.label": "test"}}]}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->post( $request, $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 201, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 2, $result['meta']['total'] );
		$this->assertArrayHasKey( 'data', $result );
		$this->assertEquals( 2, count( $result['data'] ) );
		$this->assertEquals( '-1', $result['data'][0]['id'] );
		$this->assertEquals( 'product', $result['data'][0]['type'] );
		$this->assertEquals( 'test', $result['data'][0]['attributes']['product.label'] );

		$this->assertArrayNotHasKey( 'included', $result );
		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testPostRelationships()
	{
		$productManagerStub = $this->getProductMock( array( 'getSubManager', 'create', 'get', 'save' ) );
		$productManagerListsStub = $this->getProductListsMock( array( 'save' ) );

		$product = $productManagerStub->find( 'CNE' );
		$item = new \Aimeos\MShop\Product\Item\Standard();
		$item->setId( '-1' );

		$productManagerStub->expects( $this->once() )->method( 'create' )
			->will( $this->returnValue( $item ) );
		$productManagerStub->expects( $this->any() )->method( 'get' )
			->will( $this->returnValue( $item ) );
		$productManagerStub->expects( $this->once() )->method( 'getSubManager' )
			->will( $this->returnValue( $productManagerListsStub ) );
		$productManagerStub->expects( $this->once() )->method( 'save' )
			->will( $this->returnValue( $item ) );

		$productManagerListsStub->expects( $this->exactly( 2 ) )->method( 'save' );

		$body = '{"data": {"type": "product",
			"attributes": {"product.label": "test"},
			"relationships": {"text": {"data": [
				{"type": "text", "id": "-2", "attributes": {"product.lists.type": "default"}},
				{"type": "product", "id": "' . $product->getId() . '", "attributes": {"product.lists.type": "default"}}
			]}}
		}}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->post( $request, $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 201, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertArrayHasKey( 'data', $result );
		$this->assertEquals( '-1', $result['data']['id'] );
		$this->assertEquals( 'product', $result['data']['type'] );
		$this->assertEquals( 'test', $result['data']['attributes']['product.label'] );

		$this->assertArrayNotHasKey( 'included', $result );
		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testPostInvalid()
	{
		$body = '{"data":null}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->post( $request, $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 400, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 0, $result['meta']['total'] );
		$this->assertArrayHasKey( 'errors', $result );
		$this->assertArrayNotHasKey( 'included', $result );
		$this->assertArrayNotHasKey( 'data', $result );
	}


	public function testPostInvalidId()
	{
		$body = '{"data":{"id":-1}}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->post( $request, $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 403, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 0, $result['meta']['total'] );
		$this->assertArrayHasKey( 'errors', $result );
	}


	public function testPostException()
	{
		$this->getProductMock( array( 'save' ) )->expects( $this->once() )->method( 'save' )
			->will( $this->throwException( new \RuntimeException( 'test exception' ) ) );

		$body = '{"data":{}}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->post( $request, $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 500, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );
		$this->assertArrayHasKey( 'errors', $result );
	}


	public function testPostMShopException()
	{
		$this->getProductMock( array( 'save' ) )->expects( $this->once() )->method( 'save' )
			->will( $this->throwException( new \Aimeos\MShop\Exception( 'test exception' ) ) );

		$body = '{"data":{}}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->post( $request, $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 404, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );
		$this->assertArrayHasKey( 'errors', $result );
	}


	public function testPut()
	{
		$response = $this->object->put( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 501, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );
		$this->assertArrayHasKey( 'errors', $result );
	}


	public function testOptions()
	{
		$response = $this->object->options( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Allow' ) ) );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertNull( $result['meta']['prefix'] );
		$this->assertGreaterThan( 65, count( $result['meta']['resources'] ) );
		$this->assertGreaterThan( 0, count( $result['meta']['attributes'] ) );

		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testOptionsWithPrefix()
	{
		$this->view->prefix = 'prefix';
		$response = $this->object->options( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Allow' ) ) );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 'prefix', $result['meta']['prefix'] );
		$this->assertGreaterThan( 65, count( $result['meta']['resources'] ) );
		$this->assertGreaterThan( 0, count( $result['meta']['attributes'] ) );

		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testOptionsException()
	{
		$this->getProductMock( array( 'getResourceType' ) )->expects( $this->once() )->method( 'getResourceType' )
			->will( $this->throwException( new \RuntimeException( 'test exception' ) ) );

		$response = $this->object->options( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 500, $response->getStatusCode() );
		$this->assertArrayHasKey( 'errors', $result );
	}


	public function testOptionsMShopException()
	{
		$this->getProductMock( array( 'getResourceType' ) )->expects( $this->once() )->method( 'getResourceType' )
			->will( $this->throwException( new \Aimeos\MShop\Exception( 'test exception' ) ) );

		$response = $this->object->options( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 404, $response->getStatusCode() );
		$this->assertArrayHasKey( 'errors', $result );
	}


	protected function getProductMock( array $methods )
	{
		$name = 'ClientJsonAdmStandard';
		$this->context->getConfig()->set( 'mshop/product/manager/name', $name );

		$stub = $this->getMockBuilder( '\\Aimeos\\MShop\\Product\\Manager\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( $methods )
			->getMock();

		\Aimeos\MShop\Product\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Product\\Manager\\' . $name, $stub );

		return $stub;
	}


	protected function getProductListsMock( array $methods )
	{
		$name = 'ClientJsonAdmStandard';
		$this->context->getConfig()->set( 'mshop/product/manager/lists/name', $name );

		$stub = $this->getMockBuilder( '\\Aimeos\\MShop\\Product\\Manager\\Lists\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( $methods )
			->getMock();

		\Aimeos\MShop\Product\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Product\\Manager\\Lists\\' . $name, $stub );

		return $stub;
	}


	protected function getProductItem( $code = 'CNC' )
	{
		$manager = \Aimeos\MShop::create( $this->context, 'product' );
		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );

		if( ( $item = $manager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}
