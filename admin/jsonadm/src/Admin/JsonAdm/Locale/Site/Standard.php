<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 * @package Admin
 * @subpackage JsonAdm
 */


namespace Aimeos\Admin\JsonAdm\Locale\Site;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;


/**
 * JSON API locale site  client
 *
 * @package Admin
 * @subpackage JsonAdm
 */
class Standard
	extends \Aimeos\Admin\JsonAdm\Standard
	implements \Aimeos\Admin\JsonAdm\Common\Iface
{
	/** admin/jsonadm/locale/site/decorators/excludes
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
	 * @see admin/jsonadm/locale/site/decorators/global
	 * @see admin/jsonadm/locale/site/decorators/local
	 */

	/** admin/jsonadm/locale/site/decorators/global
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
	 *  admin/jsonadm/locale/site/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\Admin\Jsonadm\Common\Decorator\Decorator1" only to the
	 * "locale/site " Jsonadm client.
	 *
	 * @param array List of decorator names
	 * @since 2016.01
	 * @category Developer
	 * @see admin/jsonadm/common/decorators/default
	 * @see admin/jsonadm/locale/site/decorators/excludes
	 * @see admin/jsonadm/locale/site/decorators/local
	 */

	/** admin/jsonadm/locale/site/decorators/local
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
	 *  admin/jsonadm/locale/site/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\Admin\Jsonadm\Catalog\Decorator\Decorator2" only to the
	 * "locale/site " Jsonadm client.
	 *
	 * @param array List of decorator names
	 * @since 2016.01
	 * @category Developer
	 * @see admin/jsonadm/common/decorators/default
	 * @see admin/jsonadm/locale/site/decorators/excludes
	 * @see admin/jsonadm/locale/site/decorators/global
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
		/** admin/jsonadm/partials/locale/site/template-data
		 * Relative path to the data partial template file for the locale site  client
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
		$this->view()->assign( array( 'partial-data' => 'admin/jsonadm/partials/locale/site/template-data' ) );

		/** admin/jsonadm/partials/locale/site/template-included
		 * Relative path to the included partial template file for the locale site  client
		 *
		 * Partials are templates which are reused in other templates and generate
		 * reoccuring blocks filled with data from the assigned values. The included
		 * partial creates the "included" part for the JSON API response.
		 *
		 * The partial template files are usually stored in the templates/partials/ folder
		 * of the core or the extensions. The configured path to the partial file must
		 * be relative to the templates/ folder, e.g. "partials/included-standard".
		 *
		 * @param string Relative path to the template file
		 * @since 2021.04
		 * @category Developer
		 */
		$this->view()->assign( array( 'partial-included' => 'admin/jsonadm/partials/locale/site/template-included' ) );

		return parent::get( $request, $response );
	}


	/**
	 * Retrieves the item or items and adds the data to the view
	 *
	 * @param \Aimeos\MW\View\Iface $view View instance
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param \Psr\Http\Message\ResponseInterface $response Response object
	 * @return \Psr\Http\Message\ResponseInterface Modified response object
	 */
	protected function getItems( \Aimeos\MW\View\Iface $view, ServerRequestInterface $request, ResponseInterface $response ) : \Psr\Http\Message\ResponseInterface
	{
		$context = $this->getContext();
		$manager = \Aimeos\MShop::create( $context, 'locale/site' );
		$search = $this->initCriteria( $manager->filter(), $view->param() );
		$total = 1;

		if( !$view->access( ['super'] ) )
		{
			$customerManager = \Aimeos\MShop::create( $context, 'customer' );
			$siteid = $customerManager->get( $context->getUserId() )->getSiteId();
			$search->add( $search->is( 'locale.site.siteid', $view->access( ['admin'] ) ? '=~' : '==', $siteid ) );
		}

		if( $id = $view->param( 'id' ) )
		{
			$search->add( $search->is( 'locale.site.id', '==', $id ) )->slice( 0, 1 );
			$view->data = $manager->search( $search, [], $total )
				->first( new \Aimeos\MShop\Exception( $view->translate( 'admin', 'Item not available' ) ) );
		}
		else
		{
			$view->data = $manager->search( $search, [], $total );
		}

		$view->childItems = map();
		$view->listItems = map();
		$view->refItems = map();
		$view->total = $total;

		return $response;
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
		if( isset( $entry->id ) )
		{
			$item = $manager->get( $entry->id );

			if( isset( $entry->attributes ) && ( $attr = (array) $entry->attributes ) ) {
				$item = $item->fromArray( $attr, true );
			}

			$item = $manager->save( $item );
		}
		else
		{
			$item = $manager->create();

			if( isset( $entry->attributes ) && ( $attr = (array) $entry->attributes ) ) {
				$item = $item->fromArray( $attr, true );
			}

			$item = $manager->insert( $item );
		}

		if( isset( $entry->relationships ) ) {
			$this->saveRelationships( $manager, $item, $entry->relationships );
		}

		return $manager->get( $item->getId() );
	}
}
