<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2026
 * @package Admin
 * @subpackage JsonAdm
 */


namespace Aimeos\Admin\JsonAdm\Order\Service\Attribute;


/**
 * JSON API order service attribute client
 *
 * @package Admin
 * @subpackage JsonAdm
 */
class Standard
	extends \Aimeos\Admin\JsonAdm\Standard
	implements \Aimeos\Admin\JsonAdm\Common\Iface
{
	/**
	 * Saves and returns the new or updated order service attribute
	 *
	 * The service providers look up their attributes like transaction IDs by type and
	 * code, so the type is always the one of the service and the code of existing
	 * attributes is read-only. Private values like the parent ID are ignored, so
	 * attributes can't be moved to services of other orders.
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager responsible for the items
	 * @param \stdClass $entry Object including "id" and "attributes" elements
	 * @return \Aimeos\MShop\Common\Item\Iface New or updated item
	 */
	protected function saveEntry( \Aimeos\MShop\Common\Manager\Iface $manager, \stdClass $entry ) : \Aimeos\MShop\Common\Item\Iface
	{
		$attr = (array) ( $entry->attributes ?? [] );
		unset( $attr['order.service.attribute.type'] );

		if( isset( $entry->id ) )
		{
			unset( $attr['order.service.attribute.code'] );
			$item = $manager->get( $entry->id );
		}
		else
		{
			$parentId = (string) ( $attr['order.service.attribute.parentid'] ?? '' );
			$service = \Aimeos\MShop::create( $this->context(), 'order/service' )->get( $parentId );
			$item = $manager->create()->setParentId( $service->getId() )->setType( $service->getType() );
		}

		$item = $manager->save( $item->fromArray( $attr ) );

		return $manager->get( $item->getId() );
	}
}
