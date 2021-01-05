<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Admin\JsonAdm\Index;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp() : void
	{
		$this->context = \TestHelperJadm::getContext();
		$this->view = $this->context->getView();

		$this->object = new \Aimeos\Admin\JsonAdm\Index\Standard( $this->context, 'index' );
		$this->object->setAimeos( \TestHelperJadm::getAimeos() );
		$this->object->setView( $this->view );
	}


	public function testDelete()
	{
		$this->getIndexMock( ['remove'] )->expects( $this->once() )->method( 'remove' );
		$id = \Aimeos\MShop::create( $this->context, 'product' )->find( 'CNC' )->getId();

		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, ['id' => $id] );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->delete( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertArrayNotHasKey( 'included', $result );
		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testDeleteMultiple()
	{
		$this->getIndexMock( ['remove'] )->expects( $this->once() )->method( 'remove' );
		$id = \Aimeos\MShop::create( $this->context, 'product' )->find( 'CNC' )->getId();

		$body = '{"data": ["' . $id . '"]}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->delete( $request, $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertArrayNotHasKey( 'included', $result );
		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testPost()
	{
		$this->getIndexMock( ['rebuild'] )->expects( $this->once() )->method( 'rebuild' );
		$id = \Aimeos\MShop::create( $this->context, 'product' )->find( 'CNC' )->getId();

		$body = '{"data": "' . $id . '"}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->post( $request, $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 201, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertArrayNotHasKey( 'included', $result );
		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testPostMultiple()
	{
		$this->getIndexMock( ['rebuild'] )->expects( $this->once() )->method( 'rebuild' );
		$id = \Aimeos\MShop::create( $this->context, 'product' )->find( 'CNC' )->getId();

		$body = '{"data": ["' . $id . '"]}';
		$request = $this->view->request()->withBody( $this->view->response()->createStreamFromString( $body ) );

		$response = $this->object->post( $request, $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 201, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertArrayNotHasKey( 'included', $result );
		$this->assertArrayNotHasKey( 'errors', $result );
	}


	protected function getIndexMock( array $methods )
	{
		$name = 'ClientJsonAdmStandard';
		$this->context->getConfig()->set( 'mshop/index/manager/name', $name );

		$stub = $this->getMockBuilder( '\\Aimeos\\MShop\\Index\\Manager\\Standard' )
			->setConstructorArgs( array( $this->context ) )
			->setMethods( $methods )
			->getMock();

		\Aimeos\MShop\Index\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Index\\Manager\\' . $name, $stub );

		return $stub;
	}
}
