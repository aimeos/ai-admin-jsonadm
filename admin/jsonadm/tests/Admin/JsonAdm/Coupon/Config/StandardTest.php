<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 */


namespace Aimeos\Admin\JsonAdm\Coupon\Config;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp() : void
	{
		$this->context = \TestHelperJadm::getContext();
		$this->view = $this->context->getView();

		$this->object = new \Aimeos\Admin\JsonAdm\Coupon\Config\Standard( $this->context, 'coupon/config' );
		$this->object->setAimeos( \TestHelperJadm::getAimeos() );
		$this->object->setView( $this->view );
	}


	public function testGet()
	{
		$params = array(
			'id' => 'None,Required,Basket',
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 2, $result['meta']['total'] );
		$this->assertIsArray( $result['data'] );
		$this->assertEquals( 'basket.total-value-min', $result['data'][0]['id'] );
		$this->assertEquals( 'basket.total-value-max', $result['data'][1]['id'] );

		$this->assertArrayNotHasKey( 'errors', $result );
	}
}
