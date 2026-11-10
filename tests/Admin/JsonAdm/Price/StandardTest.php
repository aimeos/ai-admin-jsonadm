<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
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
		$context = \TestHelper::context();
		\Aimeos\MShop::create( $context, 'product' )->delete( $this->productIds );
		\Aimeos\MShop::create( $context, 'price' )->delete( $this->priceIds );
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


	public function testGetEditorServiceOwned()
	{
		$item = $this->price( 'service' );
		$this->editor();

		$this->view->addHelper( 'param', new \Aimeos\Base\View\Helper\Param\Standard( $this->view, ['id' => $item->getId()] ) );
		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		// shared items can contain sensitive data of the domain owning them and are reported like missing items
		$this->assertEquals( 404, $response->getStatusCode() );
		$this->assertArrayNotHasKey( 'data', $result );
	}


	public function testGetEditor()
	{
		$item = $this->price( 'product' );
		$this->editor();

		$this->view->addHelper( 'param', new \Aimeos\Base\View\Helper\Param\Standard( $this->view, ['id' => $item->getId()] ) );
		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( $item->getId(), $result['data']['id'] ?? null );
	}


	public function testSearchEditorServiceOwned()
	{
		$product = $this->price( 'product' );
		$service = $this->price( 'service' );
		$this->editor();

		$params = ['filter' => ['==' => ['price.id' => [$product->getId(), $service->getId()]]]];
		$this->view->addHelper( 'param', new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $params ) );
		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		// shared items of domains the user isn't allowed to access aren't returned
		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, $result['meta']['total'] ?? null );
		$this->assertEquals( $product->getId(), $result['data'][0]['id'] ?? null );
	}


	public function testPatchEditorServiceOwned()
	{
		$item = $this->price( 'service' );
		$this->editor();

		$response = $this->patch( $item->getId(), ['price.value' => '13.37'] );

		$this->assertEquals( 403, $response->getStatusCode() );
		$this->assertEquals( '10.00', $this->get( $item->getId() )->getValue() );
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


	public function testGetIncludedEditorDenied()
	{
		$product = $this->product( $this->price( 'product' ) );
		$this->context->config()->set( 'admin/jsonadm/resource/price/groups', ['admin'] );
		$this->editor();

		$result = $this->include( $product->getId(), 'price' );

		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertEmpty( $result['data']['relationships'] ?? [] );
		$this->assertEmpty( $result['included'] ?? [] );
	}


	protected function editor() : void
	{
		$this->view->addHelper( 'access', new \Aimeos\Base\View\Helper\Access\Standard( $this->view, ['editor'] ) );
		$this->context->setView( $this->view );
	}


	protected function get( string $id ) : \Aimeos\MShop\Price\Item\Iface
	{
		return \Aimeos\MShop::create( \TestHelper::context(), 'price' )->get( $id );
	}


	protected function include( string $id, string $include ) : array
	{
		$object = new \Aimeos\Admin\JsonAdm\Product\Standard( $this->context, 'product' );
		$object->setAimeos( \TestHelper::getAimeos() )->setView( $this->view );

		$this->view->addHelper( 'param', new \Aimeos\Base\View\Helper\Param\Standard( $this->view, ['id' => $id, 'include' => $include] ) );
		$response = $object->get( $this->view->request(), $this->view->response() );

		$this->assertEquals( 200, $response->getStatusCode() );
		return json_decode( (string) $response->getBody(), true );
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


	protected function product( ?\Aimeos\MShop\Price\Item\Iface $price = null ) : \Aimeos\MShop\Product\Item\Iface
	{
		$manager = \Aimeos\MShop::create( $this->context, 'product' );
		$item = $manager->create()->setCode( 'jsonadm-price-test' )->setLabel( 'test' );

		if( $price ) {
			$item->addListItem( 'price', $manager->createListItem()->setType( 'default' )->setRefId( $price->getId() ) );
		}

		$item = $manager->save( $item );
		$this->productIds[] = $item->getId();

		return $item;
	}


	protected function request( string $body ) : \Psr\Http\Message\ServerRequestInterface
	{
		return $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );
	}
}
