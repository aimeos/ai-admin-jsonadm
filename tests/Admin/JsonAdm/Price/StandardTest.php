<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 */


namespace Aimeos\Admin\JsonAdm\Price;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $view;
	private $productIds = [];
	private $priceIds = [];


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();
		$this->view = $this->context->view();

		$this->object = new \Aimeos\Admin\JsonAdm\Price\Standard( $this->context, 'price' );
		$this->object->setAimeos( \TestHelper::getAimeos() );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		\Aimeos\MShop::create( $this->context, 'product' )->delete( $this->productIds );
		\Aimeos\MShop::create( $this->context, 'price' )->delete( $this->priceIds );
	}


	public function testGetIncluded()
	{
		$params = array(
			'filter' => array(
				'==' => array( 'price.value' => '12.95' )
			),
			'include' => 'attribute'
		);
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertGreaterThan( 1, $result['meta']['total'] );
		$this->assertGreaterThan( 1, count( $result['data'] ) );
		$this->assertEquals( 'price', $result['data'][0]['type'] );
		$this->assertEquals( 0, count( $result['data'][0]['relationships'] ) );
		$this->assertEquals( 0, count( $result['included'] ) );

		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testPatchService()
	{
		$item = $this->price( 'service' );

		$response = $this->patch( $item->getId(), ['price.value' => '13.37'] );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( '13.37', $this->get( $item->getId() )->getValue() );
	}


	public function testPatchEditor()
	{
		$item = $this->price( 'product' );
		$this->editor();

		$response = $this->patch( $item->getId(), ['price.value' => '13.37'] );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( '13.37', $this->get( $item->getId() )->getValue() );
	}


	public function testPatchEditorServiceOwned()
	{
		$item = $this->price( 'service' );
		$this->editor();

		$response = $this->patch( $item->getId(), ['price.value' => '13.37'] );

		$this->assertEquals( 403, $response->getStatusCode() );
		$this->assertEquals( '10.00', $this->get( $item->getId() )->getValue() );
	}


	public function testPatchEditorMoveToService()
	{
		$item = $this->price( 'product' );
		$this->editor();

		$response = $this->patch( $item->getId(), ['price.domain' => 'service'] );

		$this->assertEquals( 403, $response->getStatusCode() );
		$this->assertEquals( 'product', $this->get( $item->getId() )->getDomain() );
	}


	public function testPatchEditorRepoint()
	{
		$item = $this->price( 'product' );
		$service = $this->price( 'service' );
		$this->editor();

		// The ID in the attributes must not re-point the write to the service price
		$response = $this->patch( $item->getId(), ['price.id' => $service->getId(), 'price.value' => '13.37'] );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( '13.37', $this->get( $item->getId() )->getValue() );
		$this->assertEquals( '10.00', $this->get( $service->getId() )->getValue() );
	}


	public function testPatchBulkEditorServiceOwned()
	{
		$item = $this->price( 'service' );
		$this->editor();

		$body = json_encode( ['data' => [['id' => $item->getId(), 'type' => 'price', 'attributes' => ['price.value' => '13.37']]]] );
		$response = $this->object->patch( $this->request( $body ), $this->view->response() );

		$this->assertEquals( 403, $response->getStatusCode() );
		$this->assertEquals( '10.00', $this->get( $item->getId() )->getValue() );
	}


	public function testPostEditorService()
	{
		$this->editor();

		$body = json_encode( ['data' => ['type' => 'price', 'attributes' => [
			'price.domain' => 'service', 'price.value' => '13.37', 'price.currencyid' => 'EUR'
		]]] );
		$response = $this->object->post( $this->request( $body ), $this->view->response() );

		$this->assertEquals( 403, $response->getStatusCode() );
	}


	public function testDeleteEditorServiceOwned()
	{
		$item = $this->price( 'service' );
		$this->editor();

		$this->view->addHelper( 'param', new \Aimeos\Base\View\Helper\Param\Standard( $this->view, ['id' => $item->getId()] ) );
		$response = $this->object->delete( $this->view->request(), $this->view->response() );

		$this->assertEquals( 403, $response->getStatusCode() );
		$this->assertEquals( $item->getId(), $this->get( $item->getId() )->getId() );
	}


	public function testDeleteBulkEditorServiceOwned()
	{
		$product = $this->price( 'product' );
		$service = $this->price( 'service' );
		$this->editor();

		$body = json_encode( ['data' => [
			['type' => 'price', 'id' => $product->getId()],
			['type' => 'price', 'id' => $service->getId()]
		]] );
		$response = $this->object->delete( $this->request( $body ), $this->view->response() );

		$manager = \Aimeos\MShop::create( $this->context, 'price' );
		$filter = $manager->filter()->add( 'price.id', '==', $this->priceIds );

		$this->assertEquals( 403, $response->getStatusCode() );
		$this->assertEquals( 2, count( $manager->search( $filter ) ) );
	}


	public function testPatchRelationshipsEditorService()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'product' );
		$product = $manager->save( $manager->create()->setCode( 'jsonadm-price-test' )->setLabel( 'test' ) );
		$this->productIds[] = $product->getId();
		$this->editor();

		$object = new \Aimeos\Admin\JsonAdm\Standard( $this->context, 'product' );
		$object->setAimeos( \TestHelper::getAimeos() )->setView( $this->view );

		$body = json_encode( ['data' => ['type' => 'product', 'relationships' => [
			'customer' => ['data' => [['type' => 'customer', 'id' => '1']]]
		]]] );
		$this->view->addHelper( 'param', new \Aimeos\Base\View\Helper\Param\Standard( $this->view, ['id' => $product->getId()] ) );
		$response = $object->patch( $this->request( $body ), $this->view->response() );

		$this->assertEquals( 403, $response->getStatusCode() );
		$this->assertEquals( 0, count( $manager->get( $product->getId(), ['customer'] )->getListItems( 'customer' ) ) );
	}


	protected function editor() : void
	{
		$this->view->addHelper( 'access', new \Aimeos\Base\View\Helper\Access\Standard( $this->view, ['editor'] ) );
	}


	protected function get( string $id ) : \Aimeos\MShop\Price\Item\Iface
	{
		return \Aimeos\MShop::create( $this->context, 'price' )->get( $id );
	}


	protected function patch( string $id, array $attr ) : \Psr\Http\Message\ResponseInterface
	{
		$this->view->addHelper( 'param', new \Aimeos\Base\View\Helper\Param\Standard( $this->view, ['id' => $id] ) );
		$body = json_encode( ['data' => ['type' => 'price', 'attributes' => $attr]] );

		return $this->object->patch( $this->request( $body ), $this->view->response() );
	}


	protected function price( string $domain ) : \Aimeos\MShop\Price\Item\Iface
	{
		$manager = \Aimeos\MShop::create( $this->context, 'price' );
		$item = $manager->save( $manager->create()->setDomain( $domain )->setValue( '10.00' )->setCurrencyId( 'EUR' ) );
		$this->priceIds[] = $item->getId();

		return $item;
	}


	protected function request( string $body ) : \Psr\Http\Message\ServerRequestInterface
	{
		return $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );
	}
}
