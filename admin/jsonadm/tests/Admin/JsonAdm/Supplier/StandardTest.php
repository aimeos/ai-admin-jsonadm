<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Admin\JsonAdm\Supplier;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp() : void
	{
		$this->context = \TestHelperJadm::getContext();
		$this->view = $this->context->view();

		$this->object = new \Aimeos\Admin\JsonAdm\Supplier\Standard( $this->context, 'supplier' );
		$this->object->setAimeos( \TestHelperJadm::getAimeos() );
		$this->object->setView( $this->view );
	}


	public function testGetIncluded()
	{
		$params = array(
			'filter' => array(
				'==' => array( 'supplier.code' => 'unitSupplier001' )
			),
			'include' => 'text,supplier/address'
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertEquals( 1, count( $result['data'] ) );
		$this->assertEquals( 'supplier', $result['data'][0]['type'] );
		$this->assertEquals( 3, count( $result['data'][0]['relationships']['text']['data'] ) );
		$this->assertEquals( 1, count( $result['data'][0]['relationships']['supplier/address']['data'] ) );
		$this->assertEquals( 4, count( $result['included'] ) );

		$this->assertArrayNotHasKey( 'errors', $result );
	}


	public function testGetFieldsIncluded()
	{
		$params = array(
			'filter' => array(
				'=~' => array( 'supplier.code' => 'unitSupplier00' )
			),
			'fields' => array(
				'supplier' => 'supplier.id,supplier.label'
			),
			'sort' => 'supplier.id',
			'include' => 'supplier/address'
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 3, $result['meta']['total'] );
		$this->assertEquals( 3, count( $result['data'] ) );
		$this->assertEquals( 'supplier', $result['data'][0]['type'] );
		$this->assertEquals( 2, count( $result['data'][0]['attributes'] ) );
		$this->assertEquals( 1, count( $result['data'][0]['relationships']['supplier/address'] ) );
		$this->assertEquals( 3, count( $result['included'] ) );

		$this->assertArrayNotHasKey( 'errors', $result );
	}
}
