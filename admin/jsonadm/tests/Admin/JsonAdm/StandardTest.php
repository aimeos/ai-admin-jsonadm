<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\Admin\JsonAdm;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp()
	{
		$this->context = \TestHelperJadm::getContext();
		$templatePaths = \TestHelperJadm::getJsonadmPaths();
		$this->view = $this->context->getView();

		$this->object = new \Aimeos\Admin\JsonAdm\Standard( $this->context, $this->view, $templatePaths, 'product' );
	}


	protected function tearDown()
	{
		\Aimeos\MShop\Factory::clear();
	}


	public function testDelete()
	{
		$this->getProductMock( array( 'deleteItem' ) )->expects( $this->once() )->method( 'deleteItem' );

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
		$this->getProductMock( array( 'deleteItems' ) )->expects( $this->once() )->method( 'deleteItems' );

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
		$this->getProductMock( array( 'deleteItem' ) )->expects( $this->once() )->method( 'deleteItem' )
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
		$this->getProductMock( array( 'deleteItem' ) )->expects( $this->once() )->method( 'deleteItem' )
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
		$templatePaths = \TestHelperJadm::getJsonadmPaths();
		$object = new \Aimeos\Admin\JsonAdm\Standard( $this->context, $this->view, $templatePaths, 'product/property/type' );

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
		$templatePaths = \TestHelperJadm::getJsonadmPaths();
		$object = new \Aimeos\Admin\JsonAdm\Standard( $this->context, $this->view, $templatePaths, 'invalid' );

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
		$this->getProductMock( array( 'getItem' ) )->expects( $this->once() )->method( 'getItem' )
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
		$this->getProductMock( array( 'getItem' ) )->expects( $this->once() )->method( 'getItem' )
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
				'==' => array( 'product.type.code' => 'select' )
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
					array( '==' => array( 'product.type.code' => 'select' ) ),
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
			'sort' => 'product.label,-product.code'
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
		$this->assertEquals( 'QRST', $result['data'][0]['attributes']['product.code'] );
		$this->assertEquals( '16 discs', $result['data'][0]['attributes']['product.label'] );
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
		$productManagerStub = $this->getProductMock( array( 'getItem', 'saveItem' ) );

		$item = $productManagerStub->createItem();
		$item->setLabel( 'test' );
		$item->setId( '-1' );

		$productManagerStub->expects( $this->once() )->method( 'saveItem' );
		$productManagerStub->expects( $this->atLeastOnce() )->method( 'getItem' )
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
		$productManagerStub = $this->getProductMock( array( 'getItem', 'saveItem' ) );

		$item = $productManagerStub->createItem();
		$item->setLabel( 'test' );
		$item->setId( '-1' );

		$productManagerStub->expects( $this->exactly( 2 ) )->method( 'saveItem' );
		$productManagerStub->expects( $this->atLeastOnce() )->method( 'getItem' )
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
		$this->getProductMock( array( 'getItem' ) )->expects( $this->once() )->method( 'getItem' )
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
		$this->getProductMock( array( 'getItem' ) )->expects( $this->once() )->method( 'getItem' )
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
		$productManagerStub = $this->getProductMock( array( 'createItem', 'getItem', 'saveItem' ) );

		$item = new \Aimeos\MShop\Product\Item\Standard();
		$item->setId( '-1' );

		$productManagerStub->expects( $this->once() )->method( 'createItem' )
			->will( $this->returnValue( $item ) );
		$productManagerStub->expects( $this->any() )->method( 'getItem' )
			->will( $this->returnValue( $item ) );
		$productManagerStub->expects( $this->once() )->method( 'saveItem' );


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
		$this->assertGreaterThan( 0, $result['data']['attributes']['product.typeid'] );
		$this->assertEquals( 'test', $result['data']['attributes']['product.label'] );

		$this->assertArrayNotHasKey( 'included', $result );
		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testPostBulk()
	{
		$productManagerStub = $this->getProductMock( array( 'getItem', 'saveItem' ) );

		$item = $productManagerStub->createItem();
		$item->setLabel( 'test' );
		$item->setId( '-1' );

		$productManagerStub->expects( $this->exactly( 2 ) )->method( 'saveItem' );
		$productManagerStub->expects( $this->exactly( 2 ) )->method( 'getItem' )
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
		$productManagerStub = $this->getProductMock( array( 'getSubManager', 'createItem', 'getItem', 'saveItem' ) );
		$productManagerListsStub = $this->getProductListsMock( array( 'saveItem' ) );

		$item = new \Aimeos\MShop\Product\Item\Standard();
		$item->setId( '-1' );

		$productManagerStub->expects( $this->once() )->method( 'createItem' )
			->will( $this->returnValue( $item ) );
		$productManagerStub->expects( $this->any() )->method( 'getItem' )
			->will( $this->returnValue( $item ) );
		$productManagerStub->expects( $this->once() )->method( 'getSubManager' )
			->will( $this->returnValue( $productManagerListsStub ) );
		$productManagerStub->expects( $this->once() )->method( 'saveItem' );

		$productManagerListsStub->expects( $this->once() )->method( 'saveItem' );

		$body = '{"data": {"type": "product",
			"attributes": {"product.label": "test"},
			"relationships": {"text": {"data": [
				{"type": "text", "id": "-2", "attributes": {"product.lists.type": "default"}}
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
		$this->getProductMock( array( 'saveItem' ) )->expects( $this->once() )->method( 'saveItem' )
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
		$this->getProductMock( array( 'saveItem' ) )->expects( $this->once() )->method( 'saveItem' )
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
		$this->assertEquals( 57, count( $result['meta']['resources'] ) );
		$this->assertGreaterThan( 0, count( $result['meta']['attributes'] ) );

		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testOptionsWithPrefix()
	{
		$response = $this->object->options( $this->view->request(), $this->view->response(), 'prefix' );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Allow' ) ) );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 'prefix', $result['meta']['prefix'] );
		$this->assertEquals( 57, count( $result['meta']['resources'] ) );
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
		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );
		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No product item with code "%1$s" found', $code ) );
		}

		return $item;
	}
}
