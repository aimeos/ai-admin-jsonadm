<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 * @package Admin
 * @subpackage JsonAdm
 */


namespace Aimeos\Admin\JsonAdm\Order\Product\Attribute;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;


/**
 * JSON API ordered product attribute client
 *
 * @package Admin
 * @subpackage JsonAdm
 */
class Standard
	extends \Aimeos\Admin\JsonAdm\Standard
	implements \Aimeos\Admin\JsonAdm\Common\Iface
{
	/**
	 * Deletes one or more items
	 *
	 * @param \Aimeos\Base\View\Iface $view View instance with "param" view helper
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param \Psr\Http\Message\ResponseInterface $response Response object
	 * @return \Psr\Http\Message\ResponseInterface Modified response object
	 * @throws \Aimeos\Admin\JsonAdm\Exception If the user isn't a super user
	 */
	protected function deleteItems( \Aimeos\Base\View\Iface $view, ServerRequestInterface $request, ResponseInterface $response ) : \Psr\Http\Message\ResponseInterface
	{
		// attributes of ordered products like selected options are part of what was purchased
		if( !$view->access( ['super'] ) ) {
			throw new \Aimeos\Admin\JsonAdm\Exception( 'Not allowed', 403 );
		}

		return parent::deleteItems( $view, $request, $response );
	}


	/**
	 * Saves and returns the new or updated item
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager responsible for the items
	 * @param \stdClass $entry Object including "id" and "attributes" elements
	 * @return \Aimeos\MShop\Common\Item\Iface New or updated item
	 * @throws \Aimeos\Admin\JsonAdm\Exception If the user isn't a super user
	 */
	protected function saveEntry( \Aimeos\MShop\Common\Manager\Iface $manager, \stdClass $entry ) : \Aimeos\MShop\Common\Item\Iface
	{
		if( !$this->view()->access( ['super'] ) ) {
			throw new \Aimeos\Admin\JsonAdm\Exception( 'Not allowed', 403 );
		}

		return parent::saveEntry( $manager, $entry );
	}
}
