<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\Admin\JsonAdm\Rule;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp() : void
	{
		$this->context = \TestHelperJadm::getContext();
		$this->view = $this->context->view();

		$this->object = new \Aimeos\Admin\JsonAdm\Rule\Standard( $this->context, 'rule' );
		$this->object->setAimeos( \TestHelperJadm::getAimeos() );
		$this->object->setView( $this->view );
	}


	public function testGet()
	{
		$params = array(
			'filter' => array(
				'==' => array( 'rule.type' => 'catalog' )
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
		$this->assertEquals( 'rule', $result['data'][0]['type'] );

		$this->assertArrayNotHasKey( 'errors', $result );
	}
}
