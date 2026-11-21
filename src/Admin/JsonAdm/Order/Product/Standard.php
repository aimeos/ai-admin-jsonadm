<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 * @package Admin
 * @subpackage JsonAdm
 */


namespace Aimeos\Admin\JsonAdm\Order\Product;


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
			'order.product.statusdelivery',
			'order.product.statuspayment',
			'order.product.qtyopen',
			'order.product.timeframe',
			'order.product.notes',
		] ) );

		$item = $manager->save( $manager->get( $entry->id )->fromArray( $attr ) );

		return $manager->get( $item->getId() );
	}
}
