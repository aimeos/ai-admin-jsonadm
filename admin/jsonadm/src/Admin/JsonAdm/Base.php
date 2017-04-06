<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package Admin
 * @subpackage JsonAdm
 */


namespace Aimeos\Admin\JsonAdm;


/**
 * JSON API common client
 *
 * @package Admin
 * @subpackage JsonAdm
 */
abstract class Base
{
	private $view;
	private $context;
	private $templatePaths;
	private $path;


	/**
	 * Initializes the client
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @param array $templatePaths List of file system paths where the templates are stored
	 * @param string $path Name of the client separated by slashes, e.g "product/property"
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MW\View\Iface $view, array $templatePaths, $path )
	{
		$this->view = $view;
		$this->context = $context;
		$this->templatePaths = $templatePaths;
		$this->path = $path;
	}


	/**
	 * Catch unknown methods
	 *
	 * @param string $name Name of the method
	 * @param array $param List of method parameter
	 * @throws \Aimeos\Admin\JsonAdm\Exception If method call failed
	 */
	public function __call( $name, array $param )
	{
		throw new \Aimeos\Admin\JsonAdm\Exception( sprintf( 'Unable to call method "%1$s"', $name ) );
	}


	/**
	 * Returns the items with parent/child relationships
	 *
	 * @param array $items List of items implementing \Aimeos\MShop\Common\Item\Iface
	 * @param array $include List of resource types that should be fetched
	 * @return array List of items implementing \Aimeos\MShop\Common\Item\Iface
	 */
	protected function getChildItems( array $items, array $include )
	{
		return [];
	}


	/**
	 * Returns the context item object
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context object
	 */
	protected function getContext()
	{
		return $this->context;
	}


	/**
	 * Returns the list of domains that are available as resources
	 *
	 * @param \Aimeos\MW\View\Iface $view View object with "resource" parameter
	 * @return array List of domain names
	 */
	protected function getDomains( \Aimeos\MW\View\Iface $view )
	{
		if( ( $domains = $view->param( 'resource' ) ) == '' )
		{
			/** admin/jsonadm/domains
			 * A list of domain names whose clients are available for the JSON API
			 *
			 * The HTTP OPTIONS method returns a list of resources known by the
			 * JSON API including their URLs. The list of available resources
			 * can be exteded dynamically be implementing a new Jsonadm client
			 * class handling request for this new domain.
			 *
			 * To add the new domain client to the list of resources returned
			 * by the HTTP OPTIONS method, you have to add its name in lower case
			 * to the existing configuration.
			 *
			 * @param array List of domain names
			 * @since 2016.01
			 * @category Developer
			 */
			$default = array(
				'attribute', 'catalog', 'coupon', 'customer', 'locale', 'media', 'order',
				'plugin', 'price', 'product', 'service', 'supplier', 'stock', 'tag', 'text'
			);
			$domains = $this->getContext()->getConfig()->get( 'admin/jsonadm/domains', $default );
		}

		return (array) $domains;
	}


	/**
	 * Returns the IDs sent in the request body
	 *
	 * @param \stdClass $request Decoded request body
	 * @return array List of item IDs
	 */
	protected function getIds( $request )
	{
		$ids = [];

		if( isset( $request->data ) )
		{
			foreach( (array) $request->data as $entry )
			{
				if( isset( $entry->id ) ) {
					$ids[] = $entry->id;
				}
			}
		}

		return $ids;
	}


	/**
	 * Returns the list items for association relationships
	 *
	 * @param array $items List of items implementing \Aimeos\MShop\Common\Item\Iface
	 * @param array $include List of resource types that should be fetched
	 * @return array List of items implementing \Aimeos\MShop\Common\Item\Lists\Iface
	 */
	protected function getListItems( array $items, array $include )
	{
		return [];
	}


	/**
	 * Returns the path to the client
	 *
	 * @return string Client path, e.g. "product/property"
	 */
	protected function getPath()
	{
		return $this->path;
	}


	/**
	 * Returns the items associated via a lists table
	 *
	 * @param array $listItems List of items implementing \Aimeos\MShop\Common\Item\Lists\Iface
	 * @return array List of items implementing \Aimeos\MShop\Common\Item\Iface
	 */
	protected function getRefItems( array $listItems )
	{
		$list = $map = [];
		$context = $this->getContext();

		foreach( $listItems as $listItem ) {
			$map[$listItem->getDomain()][] = $listItem->getRefId();
		}

		foreach( $map as $domain => $ids )
		{
			$manager = \Aimeos\MShop\Factory::createManager( $context, $domain );

			$search = $manager->createSearch();
			$search->setConditions( $search->compare( '==', $domain . '.id', $ids ) );

			$list = array_merge( $list, $manager->searchItems( $search ) );
		}

		return $list;
	}


	/**
	 * Returns the paths to the template files
	 *
	 * @return array List of file system paths
	 */
	protected function getTemplatePaths()
	{
		return $this->templatePaths;
	}


	/**
	 * Returns the view object
	 *
	 * @return \Aimeos\MW\View\Iface View object
	 */
	protected function getView()
	{
		return $this->view;
	}


	/**
	 * Initializes the criteria object based on the given parameter
	 *
	 * @param \Aimeos\MW\Criteria\Iface $criteria Criteria object
	 * @param array $params List of criteria data with condition, sorting and paging
	 * @return \Aimeos\MW\Criteria\Iface Initialized criteria object
	 */
	protected function initCriteria( \Aimeos\MW\Criteria\Iface $criteria, array $params )
	{
		$this->initCriteriaConditions( $criteria, $params );
		$this->initCriteriaSortations( $criteria, $params );
		$this->initCriteriaSlice( $criteria, $params );

		return $criteria;
	}


	/**
	 * Initializes the criteria object with conditions based on the given parameter
	 *
	 * @param \Aimeos\MW\Criteria\Iface $criteria Criteria object
	 * @param array $params List of criteria data with condition, sorting and paging
	 */
	protected function initCriteriaConditions( \Aimeos\MW\Criteria\Iface $criteria, array $params )
	{
		if( !isset( $params['filter'] ) ) {
			return;
		}

		$existing = $criteria->getConditions();
		$criteria->setConditions( $criteria->toConditions( (array) $params['filter'] ) );

		$expr = array( $criteria->getConditions(), $existing );
		$criteria->setConditions( $criteria->combine( '&&', $expr ) );
	}


	/**
	 * Initializes the criteria object with the slice based on the given parameter.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $criteria Criteria object
	 * @param array $params List of criteria data with condition, sorting and paging
	 */
	protected function initCriteriaSlice( \Aimeos\MW\Criteria\Iface $criteria, array $params )
	{
		$start = ( isset( $params['page']['offset'] ) ? (int) $params['page']['offset'] : 0 );
		$size = ( isset( $params['page']['limit'] ) ? (int) $params['page']['limit'] : 25 );

		$criteria->setSlice( $start, $size );
	}


	/**
	 * Initializes the criteria object with sortations based on the given parameter
	 *
	 * @param \Aimeos\MW\Criteria\Iface $criteria Criteria object
	 * @param array $params List of criteria data with condition, sorting and paging
	 */
	protected function initCriteriaSortations( \Aimeos\MW\Criteria\Iface $criteria, array $params )
	{
		if( !isset( $params['sort'] ) ) {
			return;
		}

		$sortation = [];

		foreach( explode( ',', $params['sort'] ) as $sort )
		{
			if( $sort[0] === '-' ) {
				$sortation[] = $criteria->sort( '-', substr( $sort, 1 ) );
			} else {
				$sortation[] = $criteria->sort( '+', $sort );
			}
		}

		$criteria->setSortations( $sortation );
	}


	/**
	 * Creates of updates several items at once
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager responsible for the items
	 * @param \stdClass $request Object with request body data
	 * @return array List of items
	 */
	protected function saveData( \Aimeos\MShop\Common\Manager\Iface $manager, \stdClass $request )
	{
		$data = [];

		if( isset( $request->data ) )
		{
			foreach( (array) $request->data as $entry ) {
				$data[] = $this->saveEntry( $manager, $entry );
			}
		}

		return $data;
	}


	/**
	 * Saves and returns the new or updated item
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager responsible for the items
	 * @param \stdClass $entry Object including "id" and "attributes" elements
	 * @return \Aimeos\MShop\Common\Item\Iface New or updated item
	 */
	protected function saveEntry( \Aimeos\MShop\Common\Manager\Iface $manager, \stdClass $entry )
	{
		if( isset( $entry->id ) ) {
			$item = $manager->getItem( $entry->id );
		} else {
			$item = $manager->createItem();
		}

		$item = $this->addItemData( $manager, $item, $entry, $item->getResourceType() );
		$manager->saveItem( $item );

		if( isset( $entry->relationships ) ) {
			$this->saveRelationships( $manager, $item, $entry->relationships );
		}

		return $manager->getItem( $item->getId() );
	}


	/**
	 * Saves the item references associated via the list
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager responsible for the items
	 * @param \Aimeos\MShop\Common\Item\Iface $item Domain item with an unique ID set
	 * @param \stdClass $relationships Object including the <domain>/data/attributes structure
	 */
	protected function saveRelationships( \Aimeos\MShop\Common\Manager\Iface $manager,
		\Aimeos\MShop\Common\Item\Iface $item, \stdClass $relationships )
	{
		$id = $item->getId();
		$listManager = $manager->getSubManager( 'lists' );

		foreach( (array) $relationships as $domain => $list )
		{
			if( isset( $list->data ) )
			{
				foreach( (array) $list->data as $data )
				{
					$listItem = $this->addItemData( $listManager, $listManager->createItem(), $data, $domain );

					if( isset( $data->id ) ) {
						$listItem->setRefId( $data->id );
					}

					$listItem->setParentId( $id );
					$listItem->setDomain( $domain );

					$listManager->saveItem( $listItem, false );
				}
			}
		}
	}


	/**
	 * Adds the data from the given object to the item
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager object
	 * @param \Aimeos\MShop\Common\Item\Iface $item Item object to add the data to
	 * @param \stdClass $data Object with "attributes" property
	 * @param string $domain Domain of the type item
	 * @return \Aimeos\MShop\Common\Item\Iface Item including the data
	 */
	protected function addItemData(\Aimeos\MShop\Common\Manager\Iface $manager,
		\Aimeos\MShop\Common\Item\Iface $item, \stdClass $data, $domain )
	{
		if( isset( $data->attributes ) )
		{
			$attr = (array) $data->attributes;
			$key = str_replace( '/', '.', $item->getResourceType() );

			if( isset( $attr[$key.'.type'] ) )
			{
				$typeItem = $manager->getSubManager( 'type' )->findItem( $attr[$key.'.type'], [], $domain );
				$attr[$key.'.typeid'] = $typeItem->getId();
			}

			$item->fromArray( $attr );
		}

		return $item;
	}
}
