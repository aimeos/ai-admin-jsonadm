<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2026
 */


namespace Aimeos\Admin;


class JsonAdmTest extends \PHPUnit\Framework\TestCase
{
	public function testCreateClient()
	{
		$context = \TestHelper::context();
		$aimeos = \TestHelper::getAimeos();

		$client = \Aimeos\Admin\JsonAdm::create( $context, $aimeos, 'order' );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Iface', $client );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Order\\Standard', $client );
	}


	public function testCreatePolicyDefault()
	{
		$context = \TestHelper::context()->setPolicy( null );
		$client = \Aimeos\Admin\JsonAdm::create( $context, \TestHelper::getAimeos(), 'order' );

		// the shared context must not be changed, only the copy used by the client
		$copy = ( new \ReflectionMethod( $client, 'context' ) )->invoke( $client );
		$this->assertInstanceOf( \Aimeos\MShop\Policy\Groups::class, $copy->policy() );
		$this->assertNull( $context->policy() );
	}


	public function testCreatePolicyCustom()
	{
		$context = \TestHelper::context()->setPolicy( new class implements \Aimeos\MShop\PolicyIface {
			public function allowed( \Aimeos\MShop\ContextIface $context, string $resource, string $action ) : ?bool {
				return false;
			}
		} );

		$this->expectException( \Aimeos\Admin\JsonAdm\Exception::class );
		$this->expectExceptionCode( 403 );
		\Aimeos\Admin\JsonAdm::create( $context, \TestHelper::getAimeos(), 'order' );
	}


	public function testCreateSubClient()
	{
		$context = \TestHelper::context();
		$aimeos = \TestHelper::getAimeos();

		$client = \Aimeos\Admin\JsonAdm::create( $context, $aimeos, 'coupon/config' );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Iface', $client );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Coupon\\Config\\Standard', $client );
	}


	public function testCreateStandard()
	{
		$context = \TestHelper::context();
		$aimeos = \TestHelper::getAimeos();

		$client = \Aimeos\Admin\JsonAdm::create( $context, $aimeos, 'stock/type' );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Iface', $client );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Standard', $client );
	}


	public function testCreateClientEmpty()
	{
		$context = \TestHelper::context();
		$aimeos = \TestHelper::getAimeos();

		$client = \Aimeos\Admin\JsonAdm::create( $context, $aimeos, '/' );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Iface', $client );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Standard', $client );
	}


	public function testCreateClientInvalidPath()
	{
		$context = \TestHelper::context();
		$aimeos = \TestHelper::getAimeos();

		$this->expectException( '\\Aimeos\\Admin\\JsonAdm\\Exception' );
		$this->expectExceptionCode( 400 );
		\Aimeos\Admin\JsonAdm::create( $context, $aimeos, '%^' );
	}


	public function testCreateClientInvalidName()
	{
		$context = \TestHelper::context();
		$aimeos = \TestHelper::getAimeos();

		$this->expectException( '\\Aimeos\\Admin\\JsonAdm\\Exception' );
		\Aimeos\Admin\JsonAdm::create( $context, $aimeos, '', '%^' );
	}


	public function testCreateClientUnknownName()
	{
		$context = \TestHelper::context();
		$aimeos = \TestHelper::getAimeos();

		$this->expectException( '\\Aimeos\\Admin\\JsonAdm\\Exception' );
		$this->expectExceptionCode( 404 );
		\Aimeos\Admin\JsonAdm::create( $context, $aimeos, 'order', 'Unknown' );
	}


	public function testForbidden()
	{
		$previous = new \Aimeos\MShop\PolicyException( 'test' );
		$e = \Aimeos\Admin\JsonAdm::forbidden( 'test', $previous );

		$this->assertInstanceOf( \Aimeos\Admin\JsonAdm\Exception::class, $e );
		$this->assertSame( $previous, $e->getPrevious() );
		$this->assertEquals( 403, $e->getCode() );
	}


	public function testPolicy()
	{
		// the default policy uses the user groups configured for the interface
		$context = \TestHelper::context();
		$context->config()->set( 'admin/jsonadm/resource/product/groups', ['editor'] );
		$context->config()->set( 'admin/jsonadm/resource/service/groups', ['admin'] );

		$view = new \Aimeos\Base\View\Standard();
		$view->addHelper( 'access', new \Aimeos\Base\View\Helper\Access\Standard( $view, ['editor'] ) );
		$context->setView( $view );

		$policy = \Aimeos\Admin\JsonAdm::policy();

		$this->assertTrue( $policy->allowed( $context, 'product', 'save' ) );
		$this->assertFalse( $policy->allowed( $context, 'service', 'save' ) );
		$this->assertNull( $policy->allowed( $context, 'unknown', 'save' ) );

		// the groups are configured per resource for all actions
		$this->assertTrue( $policy->allowed( $context, 'product', 'delete' ) );
	}
}
