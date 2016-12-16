<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\Admin\JsonAdm\Media;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $object;
	private $view;


	protected function setUp()
	{
		$this->context = \TestHelperJadm::getContext();
		$templatePaths = \TestHelperJadm::getJsonadmPaths();
		$this->view = $this->context->getView();

		$this->object = new \Aimeos\Admin\JsonAdm\Media\Standard( $this->context, $this->view, $templatePaths, 'media' );
	}


	public function testGetIncluded()
	{
		$params = array(
			'filter' => array(
				'==' => array( 'media.label' => 'cn_colombie_179x178' )
			),
			'include' => 'attribute'
		);
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $this->view, $params );
		$this->view->addHelper( 'param', $helper );

		$response = $this->object->get( $this->view->request(), $this->view->response() );
		$result = json_decode( (string) $response->getBody(), true );


		$this->assertEquals( 200, $response->getStatusCode() );
		$this->assertEquals( 1, count( $response->getHeader( 'Content-Type' ) ) );

		$this->assertEquals( 1, $result['meta']['total'] );
		$this->assertEquals( 1, count( $result['data'] ) );
		$this->assertEquals( 'media', $result['data'][0]['type'] );
		$this->assertEquals( 1, count( $result['data'][0]['relationships']['attribute'] ) );
		$this->assertEquals( 1, count( $result['included'] ) );

		$this->assertArrayNotHasKey( 'errors', $result );
	}
}