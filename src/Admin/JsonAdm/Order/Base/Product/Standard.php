<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 * @package Admin
 * @subpackage JsonAdm
 */


namespace Aimeos\Admin\JsonAdm\Order\Base\Product;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;


/**
 * JSON API ordered product client
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
		// what was purchased for which price is part of the order and the invoice
		if( !$view->access( ['super'] ) ) {
			throw new \Aimeos\Admin\JsonAdm\Exception( 'Not allowed', 403 );
		}

		return parent::deleteItems( $view, $request, $response );
	}


	/**
	 * Updates the delivery and payment states of an ordered product
	 *
	 * Like in the admin backend, what was purchased for which price is immutable,
	 * so ordered products can't be added and only their states can be changed.
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager responsible for the items
	 * @param \stdClass $entry Object including "id" and "attributes" elements
	 * @return \Aimeos\MShop\Common\Item\Iface Updated item
	 */
	protected function saveEntry( \Aimeos\MShop\Common\Manager\Iface $manager, \stdClass $entry ) : \Aimeos\MShop\Common\Item\Iface
	{
		if( !isset( $entry->id ) ) {
			throw new \Aimeos\Admin\JsonAdm\Exception( 'Adding ordered products is not allowed', 403 );
		}

		$attr = array_intersect_key( (array) ( $entry->attributes ?? [] ), array_flip( [
			'order.base.product.statusdelivery',
			'order.base.product.statuspayment',
			'order.base.product.qtyopen',
			'order.base.product.timeframe',
			'order.base.product.notes',
		] ) );

		$item = $manager->save( $manager->get( $entry->id )->fromArray( $attr ) );

		return $manager->get( $item->getId() );
	}
}
