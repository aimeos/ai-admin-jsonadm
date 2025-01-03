<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2025
 */


namespace Aimeos\Admin\JsonAdm\Locale\Site;


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

		$this->object = new \Aimeos\Admin\JsonAdm\Locale\Site\Standard( $this->context, 'locale/site' );
		$this->object->setAimeos( \TestHelper::getAimeos() );
		$this->object->setView( $this->view );
	}


	protected function tearDown() : void
	{
		\Aimeos\MShop::cache( false );
		unset( $this->object, $this->view, $this->context );
	}


	public function testGet()
	{
		$params = array(
			'id' => $this->getSiteItem( 'unittest' )->getId(),
			'filter' => array(
				'==' => array( 'locale.site.status' => 1 )
			),
		);
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertEquals( 'locale/site', $result['data']['type'] );

		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testGetSearch()
	{
		$params = array(
			'filter' => array(
				'==' => array( 'locale.site.code' => 'unittest' )
			),
		);
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertEquals( 1, count( $result['data'] ) );
		$this->assertEquals( 'locale/site', $result['data'][0]['type'] );

		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testPatch()
	{
		$stub = $this->getSiteMock( array( 'get', 'move', 'save' ) );
		$item = $stub->create()->setId( '-1' );

		$stub->expects( $this->once() )->method( 'save' )
			->willReturn( $item );
		$stub->expects( $this->exactly( 2 ) )->method( 'get' ) // 2x due to decorator
			->willReturn( $item );


		$params = array( 'id' => '-1' );
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$body = '{"data": {"parentid": "1", "targetid": 2, "type": "locale/site", "attributes": {"locale.site.label": "test"}}}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->patch( $request, $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertArrayHasKey( 'data', $result );
		$this->assertEquals( 'locale/site', $result['data']['type'] );

		$this->assertArrayNotHasKey( 'included', $result );
		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testPost()
	{
		$stub = $this->getSiteMock( array( 'get', 'insert' ) );
		$item = $stub->create();

		$stub->expects( $this->any() )->method( 'get' )
			->willReturn( $item );
		$stub->expects( $this->once() )->method( 'insert' )
			->willReturn( $item->setId( '-1' ) );


		$body = '{"data": {"type": "locale/site", "attributes": {"locale.site.code": "unittest", "locale.site.label": "Unit test"}}}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->post( $request, $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 201, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertArrayHasKey( 'data', $result );
		$this->assertEquals( 'locale/site', $result['data']['type'] );

		$this->assertArrayNotHasKey( 'included', $result );
		$this->assertArrayNotHasKey( 'errors', $result );
	}


	protected function getSiteItem( $code )
	{
		$manager = \Aimeos\MShop::create( $this->context, 'locale/site' );
		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'locale.site.code', $code ) );

		if( ( $item = $manager->search( $search )->first() ) === null ) {
			throw new \RuntimeException( sprintf( 'No locale site item with code "%1$s" found', $code ) );
		}

		return $item;
	}


	protected function getSiteMock( array $methods )
	{
		$this->context->config()->set( 'mshop/locale/manager/site/name', 'Standard' );

		$siteStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Locale\\Manager\\Site\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->onlyMethods( $methods )
			->getMock();

		\Aimeos\MShop::inject( '\\Aimeos\\MShop\\Locale\\Manager\\Site\\Standard', $siteStub );

		return $siteStub;
	}
}
