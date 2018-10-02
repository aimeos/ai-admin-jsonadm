<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
 */


namespace Aimeos\Admin\JsonAdm\Plugin\Config;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp()
	{
		$this->context = \TestHelperJadm::getContext();
		$this->view = $this->context->getView();

		$this->object = new \Aimeos\Admin\JsonAdm\Plugin\Config\Standard( $this->context, 'plugin/config' );
		$this->object->setAimeos( \TestHelperJadm::getAimeos() );
		$this->object->setView( $this->view );
	}


	public function testGet()
	{
		$params = array(
			'id' => 'Autofill,Log',
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );

		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 8, $result['meta']['total'] );
		$this->assertInternalType( 'array', $result['data'] );
		$this->assertEquals( 'autofill.address', $result['data'][0]['id'] );
		$this->assertEquals( 'autofill.delivery', $result['data'][1]['id'] );
		$this->assertEquals( 'autofill.deliverycode', $result['data'][2]['id'] );
		$this->assertEquals( 'autofill.payment', $result['data'][3]['id'] );
		$this->assertEquals( 'autofill.paymentcode', $result['data'][4]['id'] );
		$this->assertEquals( 'autofill.useorder', $result['data'][5]['id'] );
		$this->assertEquals( 'autofill.orderaddress', $result['data'][6]['id'] );
		$this->assertEquals( 'autofill.orderservice', $result['data'][7]['id'] );

		$this->assertArrayNotHasKey( 'errors', $result );
	}
}