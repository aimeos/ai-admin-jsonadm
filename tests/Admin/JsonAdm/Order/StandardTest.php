<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\Admin\JsonAdm\Order;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();
		$this->view = $this->context->view();

		$this->object = new \Aimeos\Admin\JsonAdm\Order\Standard( $this->context, 'order' );
		$this->object->setAimeos( \TestHelper::getAimeos() );
		$this->object->setView( $this->view );
	}


	public function testGetAggregate()
	{
		$params = array(
			'filter' => array(
				'==' => array( 'order.editor' => 'core' )
			),
			'aggregate' => 'order.cdate',
		);
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertEquals( 1, count( $result['data'] ) );
		$this->assertEquals( 'aggregate', $result['data'][0]['type'] );
		$this->assertGreaterThan( 0, $result['data'][0]['attributes'] );

		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testGetAggregateMultiple()
	{
		$params = array(
			'filter' => array(
				'==' => array( 'order.editor' => 'core' )
			),
			'aggregate' => 'order.statuspayment,order.cdate',
		);
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 2, $result['meta']['total'] );
		$this->assertEquals( 2, count( $result['data'] ) );
		$this->assertEquals( 'aggregate', $result['data'][0]['type'] );
		$this->assertEquals( 1, count( $result['data'][0]['attributes'] ) );
		$this->assertEquals( 'aggregate', $result['data'][1]['type'] );
		$this->assertEquals( 1, count( $result['data'][1]['attributes'] ) );

		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testGetIncluded()
	{
		$params = array(
			'filter' => array(
				'==' => array( 'order.datepayment' => '2008-02-15 12:34:56' )
			),
			'include' => 'order,order/status,order/address,order/product,order/service,order/coupon,order/product/attribute,order/service/attribute'
		);
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertEquals( 1, count( $result['data'] ) );
		$this->assertEquals( 'order', $result['data'][0]['type'] );
		$this->assertEquals( 1, count( $result['data'][0]['relationships']['order/status'] ) );
		$this->assertEquals( 1, count( $result['data'][0]['relationships']['order'] ) );
		$this->assertGreaterThanOrEqual( 31, count( $result['included'] ) );

		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testGetFieldsIncluded()
	{
		$params = array(
			'fields' => array(
				'order' => 'order.id,order.channel'
			),
			'sort' => 'order.id',
			'include' => 'order/status'
		);
		$helper = new \Aimeos\Base\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertGreaterThanOrEqual( 4, $result['meta']['total'] );
		$this->assertGreaterThanOrEqual( 4, count( $result['data'] ) );
		$this->assertEquals( 'order', $result['data'][0]['type'] );
		$this->assertEquals( 2, count( $result['data'][0]['attributes'] ) );
		$this->assertGreaterThanOrEqual( 1, count( $result['data'][0]['relationships']['order/status'] ) );
		$this->assertGreaterThanOrEqual( 3, count( $result['included'] ) );

		$this->assertArrayNotHasKey( 'errors', $result );
	}
}
