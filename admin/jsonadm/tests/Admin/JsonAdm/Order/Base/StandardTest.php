<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Admin\JsonAdm\Order\Base;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp() : void
	{
		$this->context = \TestHelperJadm::getContext();
		$this->view = $this->context->getView();

		$this->object = new \Aimeos\Admin\JsonAdm\Order\Base\Standard( $this->context, 'order/base' );
		$this->object->setAimeos( \TestHelperJadm::getAimeos() );
		$this->object->setView( $this->view );
	}


	public function testGetIncluded()
	{
		$params = array(
			'filter' => array(
				'==' => array( 'order.base.price' => '4800.00' )
			),
			'include' => 'order/base/address,order/base/product'
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertEquals( 1, count( $result['data'] ) );
		$this->assertEquals( 'order/base', $result['data'][0]['type'] );
		$this->assertEquals( 2, count( $result['data'][0]['relationships'] ) );
		$this->assertEquals( 1, count( $result['data'][0]['relationships']['order/base/address'] ) );
		$this->assertEquals( 6, count( $result['data'][0]['relationships']['order/base/product']['data'] ) );
		$this->assertEquals( 7, count( $result['included'] ) );

		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testGetFieldsIncluded()
	{
		$params = array(
			'fields' => array(
				'order/base' => 'order.base.languageid,order.base.currencyid',
				'order/base/product' => 'order.base.product.name,order.base.product.price'
			),
			'sort' => 'order.base.id',
			'include' => 'order/base/product'
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertGreaterThanOrEqual( 4, $result['meta']['total'] );
		$this->assertGreaterThanOrEqual( 4, count( $result['data'] ) );
		$this->assertEquals( 'order/base', $result['data'][0]['type'] );
		$this->assertEquals( 2, count( $result['data'][0]['attributes'] ) );
		$this->assertGreaterThanOrEqual( 4, count( $result['data'][0]['relationships']['order/base/product']['data'] ) );
		$this->assertGreaterThanOrEqual( 14, count( $result['included'] ) );
		$this->assertGreaterThanOrEqual( 2, count( $result['included'][0]['attributes'] ) );

		$this->assertArrayNotHasKey( 'errors', $result );
	}
}
