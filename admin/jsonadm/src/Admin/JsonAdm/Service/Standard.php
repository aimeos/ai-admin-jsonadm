<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2020
 * @package Admin
 * @subpackage JsonAdm
 */


namespace Aimeos\Admin\JsonAdm\Service;


/**
 * JSON API service client
 *
 * @package Admin
 * @subpackage JsonAdm
 */
class Standard
	extends \Aimeos\Admin\JsonAdm\Standard
	implements \Aimeos\Admin\JsonAdm\Common\Iface
{
	/** admin/jsonadm/service/decorators/excludes
	 * Excludes decorators added by the "common" option from the JSON API clients
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to remove a decorator added via
	 * "admin/jsonadm/common/decorators/default" before they are wrapped
	 * around the Jsonadm client.
	 *
	 *  admin/jsonadm/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\Admin\JsonAdm\Common\Decorator\*") added via
	 * "admin/jsonadm/common/decorators/default" for the JSON API client.
	 *
	 * @param array List of decorator names
	 * @since 2016.01
	 * @category Developer
	 * @see admin/jsonadm/common/decorators/default
	 * @see admin/jsonadm/service/decorators/global
	 * @see admin/jsonadm/service/decorators/local
	 */

	/** admin/jsonadm/service/decorators/global
	 * Adds a list of globally available decorators only to the Jsonadm client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\Admin\Jsonadm\Common\Decorator\*") around the Jsonadm
	 * client.
	 *
	 *  admin/jsonadm/service/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\Admin\Jsonadm\Common\Decorator\Decorator1" only to the
	 * "service" Jsonadm client.
	 *
	 * @param array List of decorator names
	 * @since 2016.01
	 * @category Developer
	 * @see admin/jsonadm/common/decorators/default
	 * @see admin/jsonadm/service/decorators/excludes
	 * @see admin/jsonadm/service/decorators/local
	 */

	/** admin/jsonadm/service/decorators/local
	 * Adds a list of local decorators only to the Jsonadm client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\Admin\Jsonadm\Service\Decorator\*") around the Jsonadm
	 * client.
	 *
	 *  admin/jsonadm/service/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\Admin\Jsonadm\Service\Decorator\Decorator2" only to the
	 * "service" Jsonadm client.
	 *
	 * @param array List of decorator names
	 * @since 2016.01
	 * @category Developer
	 * @see admin/jsonadm/common/decorators/default
	 * @see admin/jsonadm/service/decorators/excludes
	 * @see admin/jsonadm/service/decorators/global
	 */


	/**
	 * Returns the list items for association relationships
	 *
	 * @param \Aimeos\Map $items List of items implementing \Aimeos\MShop\Common\Item\Iface
	 * @param array $include List of resource types that should be fetched
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Lists\Iface
	 */
	protected function getListItems( \Aimeos\Map $items, array $include ) : \Aimeos\Map
	{
		$manager = \Aimeos\MShop::create( $this->getContext(), 'service/lists' );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', 'service.lists.parentid', $items->keys()->toArray() ),
			$search->compare( '==', 'service.lists.domain', $include ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		return $manager->searchItems( $search );
	}
	
	/**
	* Saves and returns the new or updated item
	*
	* @param \Aimeos\MShop\Common\Manager\Iface $manager Manager responsible for the items
	* @param \stdClass $entry Object including "id" and "attributes" elements
	* @return \Aimeos\MShop\Common\Item\Iface New or updated item
	*/
	protected function saveEntry( \Aimeos\MShop\Common\Manager\Iface $manager, \stdClass $entry ) : \Aimeos\MShop\Common\Item\Iface
	{
		if( isset( $entry->id ) ) {
			$item = $manager->getItem( $entry->id );
		} else {
			$item = $manager->createItem();
		}
		
		if( isset( $entry->attributes ) && ( $attr = (array) $entry->attributes ) ) {
			if( isset( $attr["service.config"] ) ) {
				$attr["service.config"] = (array) $attr["service.config"];
			}
			$item = $item->fromArray( $attr, true );
		}
		
		$item = $manager->saveItem( $item );
		
		if( isset( $entry->relationships ) ) {
			$this->saveRelationships( $manager, $item, $entry->relationships );
		}
		
		return $manager->getItem( $item->getId() );
	}
}
