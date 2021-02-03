<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package Admin
 * @subpackage JsonAdm
 */


namespace Aimeos\Admin\JsonAdm\Catalog;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;


/**
 * JSON API catalog client
 *
 * @package Admin
 * @subpackage JsonAdm
 */
class Standard
	extends \Aimeos\Admin\JsonAdm\Standard
	implements \Aimeos\Admin\JsonAdm\Common\Iface
{
	/** admin/jsonadm/catalog/decorators/excludes
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
	 * @see admin/jsonadm/catalog/decorators/global
	 * @see admin/jsonadm/catalog/decorators/local
	 */

	/** admin/jsonadm/catalog/decorators/global
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
	 *  admin/jsonadm/catalog/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\Admin\Jsonadm\Common\Decorator\Decorator1" only to the
	 * "catalog" Jsonadm client.
	 *
	 * @param array List of decorator names
	 * @since 2016.01
	 * @category Developer
	 * @see admin/jsonadm/common/decorators/default
	 * @see admin/jsonadm/catalog/decorators/excludes
	 * @see admin/jsonadm/catalog/decorators/local
	 */

	/** admin/jsonadm/catalog/decorators/local
	 * Adds a list of local decorators only to the Jsonadm client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\Admin\Jsonadm\Catalog\Decorator\*") around the Jsonadm
	 * client.
	 *
	 *  admin/jsonadm/catalog/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\Admin\Jsonadm\Catalog\Decorator\Decorator2" only to the
	 * "catalog" Jsonadm client.
	 *
	 * @param array List of decorator names
	 * @since 2016.01
	 * @category Developer
	 * @see admin/jsonadm/common/decorators/default
	 * @see admin/jsonadm/catalog/decorators/excludes
	 * @see admin/jsonadm/catalog/decorators/global
	 */


	/**
	 * Returns the requested resource or the resource list
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param \Psr\Http\Message\ResponseInterface $response Response object
	 * @return \Psr\Http\Message\ResponseInterface Modified response object
	 */
	public function get( ServerRequestInterface $request, ResponseInterface $response ) : \Psr\Http\Message\ResponseInterface
	{
		/** admin/jsonadm/partials/catalog/template-data
		 * Relative path to the data partial template file for the catalog client
		 *
		 * Partials are templates which are reused in other templates and generate
		 * reoccuring blocks filled with data from the assigned values. The data
		 * partial creates the "data" part for the JSON API response.
		 *
		 * The partial template files are usually stored in the templates/partials/ folder
		 * of the core or the extensions. The configured path to the partial file must
		 * be relative to the templates/ folder, e.g. "partials/data-standard".
		 *
		 * @param string Relative path to the template file
		 * @since 2016.07
		 * @category Developer
		 */
		$this->getView()->assign( array( 'partial-data' => 'admin/jsonadm/partials/catalog/template-data' ) );

		return parent::get( $request, $response );
	}


	/**
	 * Returns the items with parent/child relationships
	 *
	 * @param \Aimeos\Map $items List of items implementing \Aimeos\MShop\Common\Item\Iface
	 * @param array $include List of resource types that should be fetched
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Iface
	 */
	protected function getChildItems( \Aimeos\Map $items, array $include ) : \Aimeos\Map
	{
		$list = map();

		if( in_array( 'catalog', $include ) )
		{
			foreach( $items as $item ) {
				$list = $list->push( $item )->merge( $this->getChildItems( map( $item->getChildren() ), $include ) );
			}
		}

		return $list;
	}


	/**
	 * Retrieves the item or items and adds the data to the view
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param \Psr\Http\Message\ResponseInterface $response Response object
	 * @return \Psr\Http\Message\ResponseInterface Modified response object
	 */
	protected function getItems( \Aimeos\MW\View\Iface $view, ServerRequestInterface $request, ResponseInterface $response ) : \Psr\Http\Message\ResponseInterface
	{
		$manager = \Aimeos\MShop::create( $this->getContext(), 'catalog' );

		if( ( $key = $view->param( 'aggregate' ) ) !== null )
		{
			$search = $this->initCriteria( $manager->filter(), $view->param() );
			$view->data = $manager->aggregate( $search, explode( ',', $key ) );
			return $response;
		}

		$include = ( ( $include = $view->param( 'include' ) ) !== null ? explode( ',', $include ) : [] );
		$search = $this->initCriteria( $manager->filter(), $view->param() );
		$total = 1;

		if( ( $id = $view->param( 'id' ) ) == null )
		{
			$view->data = $manager->search( $search, $include, $total );
			$view->listItems = $this->getListItems( $view->data, $include );
			$view->childItems = map();
		}
		else
		{
			$level = \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE;
			if( in_array( 'catalog', $include ) ) {
				$level = \Aimeos\MW\Tree\Manager\Base::LEVEL_LIST;
			}

			$view->data = $manager->getTree( $id, $include, $level, $search );
			$view->listItems = $this->getListItems( map( [$id => $view->data] ), $include );
			$view->childItems = $this->getChildItems( map( $view->data->getChildren() ), $include );
		}

		$view->refItems = $this->getRefItems( $view->listItems );
		$view->total = $total;

		return $response;
	}


	/**
	 * Returns the list items for association relationships
	 *
	 * @param \Aimeos\Map $items List of items implementing \Aimeos\MShop\Common\Item\Iface
	 * @param array $include List of resource types that should be fetched
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Lists\Iface
	 */
	protected function getListItems( \Aimeos\Map $items, array $include ) : \Aimeos\Map
	{
		return $items->getListItems( null, null, null, false )->collapse();
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
		$targetId = ( isset( $entry->targetid ) ? $entry->targetid : null );
		$refId = ( isset( $entry->refid ) ? $entry->refid : null );

		if( isset( $entry->id ) )
		{
			$item = $manager->get( $entry->id );

			if( isset( $entry->attributes ) && ( $attr = (array) $entry->attributes ) ) {
				$item = $item->fromArray( $attr, true );
			}

			$item = $manager->save( $item );

			if( isset( $entry->parentid ) && $targetId !== null ) {
				$manager->move( $item->getId(), $entry->parentid, $targetId, $refId );
			}
		}
		else
		{
			$item = $manager->create();

			if( isset( $entry->attributes ) && ( $attr = (array) $entry->attributes ) ) {
				$item = $item->fromArray( $attr, true );
			}

			$item = $manager->insert( $item, $targetId, $refId );
		}

		if( isset( $entry->relationships ) ) {
			$this->saveRelationships( $manager, $item, $entry->relationships );
		}

		return $manager->get( $item->getId() );
	}
}
