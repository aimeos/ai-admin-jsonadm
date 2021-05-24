<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Admin\JsonAdm\Catalog;


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

		$this->object = new \Aimeos\Admin\JsonAdm\Catalog\Standard( $this->context, 'catalog' );
		$this->object->setAimeos( \TestHelperJadm::getAimeos() );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		\Aimeos\MShop::cache( false );
	}


	public function testGetSearch()
	{
		$params = array(
			'filter' => array(
				'==' => array( 'catalog.code' => 'cafe' )
			),
			'include' => 'text'
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertEquals( 1, count( $result['data'] ) );
		$this->assertEquals( 'catalog', $result['data'][0]['type'] );
		$this->assertEquals( 6, count( $result['data'][0]['relationships']['text']['data'] ) );
		$this->assertEquals( 6, count( $result['included'] ) );

		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testGetTree()
	{
		$params = array(
			'id' => $this->getCatalogItem( 'root' )->getId(),
			'include' => 'catalog,text'
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertEquals( 'catalog', $result['data']['type'] );
		$this->assertEquals( 2, count( $result['data']['relationships']['catalog']['data'] ) );
		$this->assertEquals( 2, count( $result['included'] ) );

		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testPatch()
	{
		$stub = $this->getCatalogMock( array( 'get', 'move', 'save' ) );
		$item = $stub->create()->setId( '-1' );

		$stub->expects( $this->once() )->method( 'move' );
		$stub->expects( $this->once() )->method( 'save' )
			->will( $this->returnValue( $item ) );
		$stub->expects( $this->exactly( 2 ) )->method( 'get' ) // 2x due to decorator
			->will( $this->returnValue( $item ) );


		$params = array( 'id' => '-1' );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$body = '{"data": {"parentid": "1", "targetid": 2, "type": "catalog", "attributes": {"catalog.label": "test"}}}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->patch( $request, $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertArrayHasKey( 'data', $result );
		$this->assertEquals( 'catalog', $result['data']['type'] );

		$this->assertArrayNotHasKey( 'included', $result );
		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testPost()
	{
		$stub = $this->getCatalogMock( array( 'get', 'insert' ) );
		$item = $stub->create()->setId( '-1' );

		$stub->expects( $this->any() )->method( 'get' )
			->will( $this->returnValue( $item ) );
		$stub->expects( $this->once() )->method( 'insert' )
			->will( $this->returnValue( $item ) );


		$body = '{"data": {"type": "catalog", "attributes": {"catalog.code": "test", "catalog.label": "Test catalog"}}}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->post( $request, $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 201, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertArrayHasKey( 'data', $result );
		$this->assertEquals( 'catalog', $result['data']['type'] );

		$this->assertArrayNotHasKey( 'included', $result );
		$this->assertArrayNotHasKey( 'errors', $result );
	}


	protected function getCatalogItem( $code )
	{
		$manager = \Aimeos\MShop::create( $this->context, 'catalog' );
		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'catalog.code', $code ) );

		if( ( $item = $manager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( sprintf( 'No catalog item with code "%1$s" found', $code ) );
		}

		return $item;
	}


	protected function getCatalogMock( array $methods )
	{
		$name = 'ClientJsonAdmStandard';
		$this->context->getConfig()->set( 'mshop/catalog/manager/name', $name );

		$stub = $this->getMockBuilder( '\\Aimeos\\MShop\\Catalog\\Manager\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( $methods )
			->getMock();

		\Aimeos\MShop\Product\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Catalog\\Manager\\' . $name, $stub );

		return $stub;
	}
}
