<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\Admin\JsonAdm\Locale\Site;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp()
	{
		$this->context = \TestHelperJadm::getContext();
		$this->view = $this->context->getView();

		$this->object = new \Aimeos\Admin\JsonAdm\Locale\Site\Standard( $this->context, 'locale/site' );
		$this->object->setAimeos( \TestHelperJadm::getAimeos() );
		$this->object->setView( $this->view );
	}


	public function testGet()
	{
		$params = array(
			'id' => $this->getSiteItem( 'unittest' )->getId(),
			'filter' => array(
				'==' => array( 'locale.status' => 0 )
			),
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
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
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
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
		$stub = $this->getSiteMock( array( 'getItem', 'moveItem', 'saveItem' ) );
		$item = $stub->createItem();

		$stub->expects( $this->once() )->method( 'saveItem' )
			->will( $this->returnValue( $stub->createItem() ) );
		$stub->expects( $this->exactly( 2 ) )->method( 'getItem' ) // 2x due to decorator
			->will( $this->returnValue( $item ) );


		$params = array( 'id' => '-1' );
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
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
		$stub = $this->getSiteMock( array( 'getItem', 'insertItem' ) );
		$item = $stub->createItem();

		$stub->expects( $this->any() )->method( 'getItem' )
			->will( $this->returnValue( $item ) );
		$stub->expects( $this->once() )->method( 'insertItem' );


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
		$manager = \Aimeos\MShop\Locale\Manager\Factory::createManager( $this->context )->getSubManager( 'site' );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'locale.site.code', $code ) );
		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No locale site item with code "%1$s" found', $code ) );
		}

		return $item;
	}


	protected function getSiteMock( array $methods )
	{
		$name = 'AdminJsonAdmStandard';
		$this->context->getConfig()->set( 'mshop/locale/manager/name', $name );

		$stub = $this->getMockBuilder( '\\Aimeos\\MShop\\Locale\\Manager\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( array( 'getSubManager' ) )
			->getMock();

		$siteStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Locale\\Manager\\Site\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( $methods )
			->getMock();

		$stub->expects( $this->once() )->method( 'getSubManager' )
			->will( $this->returnValue( $siteStub ) );

		\Aimeos\MShop\Locale\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Locale\\Manager\\' . $name, $stub );

		return $siteStub;
	}
}