<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\Admin\JsonAdm\Product;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp()
	{
		$this->context = \TestHelperJadm::getContext();
		$this->view = $this->context->getView();

		$this->object = new \Aimeos\Admin\JsonAdm\Product\Standard( $this->context, 'product' );
		$this->object->setAimeos( \TestHelperJadm::getAimeos() );
		$this->object->setView( $this->view );
	}


	public function testGetIncluded()
	{
		$params = array(
			'filter' => array(
				'==' => array( 'product.code' => 'CNE' )
			),
			'include' => 'text,product,product/property'
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertEquals( 1, count( $result['data'] ) );
		$this->assertEquals( 'product', $result['data'][0]['type'] );
		$this->assertEquals( 7, count( $result['data'][0]['relationships']['text'] ) );
		$this->assertArrayHaskey( 'self', $result['data'][0]['relationships']['text'][0]['data']['links'] );
		$this->assertEquals( 5, count( $result['data'][0]['relationships']['product'] ) );
		$this->assertArrayHaskey( 'self', $result['data'][0]['relationships']['product'][0]['data']['links'] );
		$this->assertEquals( 4, count( $result['data'][0]['relationships']['product/property'] ) );
		$this->assertEquals( 15, count( $result['included'] ) );
		$this->assertEquals( 'product/property', $result['included'][0]['type'] );
		$this->assertArrayHaskey( 'self', $result['included'][0]['links'] );
		$this->assertArrayHaskey( 'related', $result['included'][0]['links'] );

		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testGetFieldsIncluded()
	{
		$params = array(
			'fields' => array(
				'product' => 'product.id,product.code,product.label'
			),
			'sort' => 'product.code',
			'include' => 'product,product/property'
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
		$this->assertEquals( 3, count( $result['data'][0]['attributes'] ) );
		$this->assertEquals( 5, count( $result['data'][8]['relationships']['product'] ) );
		$this->assertEquals( 21, count( $result['included'] ) );

		$this->assertArrayNotHasKey( 'errors', $result );
	}
}