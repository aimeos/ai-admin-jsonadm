<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2020
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
	private $context;
	private $aimeos;
	private $view;
	private $path;


	/**
	 * Initializes the client
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context MShop context object
	 * @param string $path Name of the client separated by slashes, e.g "product/property"
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context, string $path )
	{
		$this->context = $context;
		$this->path = $path;
	}


	/**
	 * Catch unknown methods
	 *
	 * @param string $name Name of the method
	 * @param array $param List of method parameter
	 * @throws \Aimeos\Admin\JsonAdm\Exception If method call failed
	 */
	public function __call( string $name, array $param )
	{
		throw new \Aimeos\Admin\JsonAdm\Exception( sprintf( 'Unable to call method "%1$s"', $name ) );
	}


	/**
	 * Returns the Aimeos bootstrap object
	 *
	 * @return \Aimeos\Bootstrap The Aimeos bootstrap object
	 */
	public function getAimeos() : \Aimeos\Bootstrap
	{
		if( !isset( $this->aimeos ) ) {
			throw new \Aimeos\Admin\JsonAdm\Exception( sprintf( 'Aimeos object not available' ) );
		}

		return $this->aimeos;
	}


	/**
	 * Sets the Aimeos bootstrap object
	 *
	 * @param \Aimeos\Bootstrap $aimeos The Aimeos bootstrap object
	 * @return \Aimeos\Admin\JsonAdm\Iface Reference to this object for fluent calls
	 */
	public function setAimeos( \Aimeos\Bootstrap $aimeos ) : \Aimeos\Admin\JsonAdm\Iface
	{
		$this->aimeos = $aimeos;
		return $this;
	}


	/**
	 * Returns the view object that will generate the admin output.
	 *
	 * @return \Aimeos\MW\View\Iface The view object which generates the admin output
	 */
	public function getView() : \Aimeos\MW\View\Iface
	{
		if( !isset( $this->view ) ) {
			throw new \Aimeos\Admin\JsonAdm\Exception( sprintf( 'No view available' ) );
		}

		return $this->view;
	}


	/**
	 * Sets the view object that will generate the admin output.
	 *
	 * @param \Aimeos\MW\View\Iface $view The view object which generates the admin output
	 * @return \Aimeos\Admin\JsonAdm\Iface Reference to this object for fluent calls
	 */
	public function setView( \Aimeos\MW\View\Iface $view ) : \Aimeos\Admin\JsonAdm\Iface
	{
		$this->view = $view;
		return $this;
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
		return map();
	}


	/**
	 * Returns the context item object
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context object
	 */
	protected function getContext() : \Aimeos\MShop\Context\Item\Iface
	{
		return $this->context;
	}


	/**
	 * Returns the list of domains that are available as resources
	 *
	 * @param \Aimeos\MW\View\Iface $view View object with "resource" parameter
	 * @return array List of domain names
	 */
	protected function getDomains( \Aimeos\MW\View\Iface $view ) : array
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
			 * @see admin/jsonadm/resources
			 */
			$domains = $this->getContext()->getConfig()->get( 'admin/jsonadm/domains', [] );
		}

		return (array) $domains;
	}


	/**
	 * Returns the list items for association relationships
	 *
	 * @param \Aimeos\Map $items List of items implementing \Aimeos\MShop\Common\Item\Iface
	 * @param array $include List of resource types that should be fetched
	 * @return array List of items implementing \Aimeos\MShop\Common\Item\Lists\Iface
	 */
	protected function getListItems( \Aimeos\Map $items, array $include ) : \Aimeos\Map
	{
		return map();
	}


	/**
	 * Returns the path to the client
	 *
	 * @return string Client path, e.g. "product/property"
	 */
	protected function getPath() : string
	{
		return $this->path;
	}


	/**
	 * Returns the items associated via a lists table
	 *
	 * @param \Aimeos\Map $listItems List of items implementing \Aimeos\MShop\Common\Item\Lists\Iface
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Iface
	 */
	protected function getRefItems( \Aimeos\Map $listItems ) : \Aimeos\Map
	{
		$map = [];
		$list = map();
		$context = $this->getContext();

		foreach( $listItems as $listItem ) {
			$map[$listItem->getDomain()][] = $listItem->getRefId();
		}

		foreach( $map as $domain => $ids )
		{
			$manager = \Aimeos\MShop::create( $context, $domain );

			$search = $manager->createSearch();
			$search->setConditions( $search->compare( '==', str_replace( '/', '.', $domain ) . '.id', $ids ) );

			$list = $list->merge( $manager->searchItems( $search ) );
		}

		return $list;
	}


	/**
	 * Returns the list of allowed resources
	 *
	 * @param \Aimeos\MW\View\Iface $view View object with "access" helper
	 * @param array List of all available resources
	 * @return array List of allowed resources
	 */
	protected function getAllowedResources( \Aimeos\MW\View\Iface $view, array $resources ) : array
	{
		$config = $this->getContext()->getConfig();
		$allowed = [];

		foreach( $resources as $resource )
		{
			if( $view->access( $config->get( 'admin/jsonadm/resource/' . $resource . '/groups', [] ) ) === true ) {
				$allowed[] = $resource;
			}
		}

		return $allowed;
	}


	/**
	 * Returns the list of additional resources
	 *
	 * @param \Aimeos\MW\View\Iface $view View object with "resource" parameter
	 * @return array List of domain names
	 */
	protected function getResources( \Aimeos\MW\View\Iface $view ) : array
	{
		/** admin/jsonadm/resources
		 * A list of additional resources name whose clients are available for the JSON API
		 *
		 * The HTTP OPTIONS method returns a list of resources known by the
		 * JSON API including their URLs. The list of available resources
		 * can be exteded dynamically be implementing a new Jsonadm client
		 * class handling request for this new domain.
		 *
		 * The resource config lists the resources that are not automatically
		 * derived from the admin/jsonadm/domains configuration.
		 *
		 * @param array List of domain names
		 * @since 2017.07
		 * @category Developer
		 * @see admin/jsonadm/domains
		 */
		return (array) $view->config( 'admin/jsonadm/resources', [] );
	}


	/**
	 * Initializes the criteria object based on the given parameter
	 *
	 * @param \Aimeos\MW\Criteria\Iface $criteria Criteria object
	 * @param array $params List of criteria data with condition, sorting and paging
	 * @return \Aimeos\MW\Criteria\Iface Initialized criteria object
	 */
	protected function initCriteria( \Aimeos\MW\Criteria\Iface $criteria, array $params ) : \Aimeos\MW\Criteria\Iface
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
	 * @return \Aimeos\MW\Criteria\Iface Initialized criteria object
	 */
	protected function initCriteriaConditions( \Aimeos\MW\Criteria\Iface $criteria, array $params ) : \Aimeos\MW\Criteria\Iface
	{
		if( !isset( $params['filter'] ) ) {
			return $criteria;
		}

		if( ( $cond = $criteria->toConditions( (array) $params['filter'] ) ) !== null ) {
			return $criteria->setConditions( $criteria->combine( '&&', [$cond, $criteria->getConditions()] ) );
		}

		return $criteria;
	}


	/**
	 * Initializes the criteria object with the slice based on the given parameter.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $criteria Criteria object
	 * @param array $params List of criteria data with condition, sorting and paging
	 * @return \Aimeos\MW\Criteria\Iface Initialized criteria object
	 */
	protected function initCriteriaSlice( \Aimeos\MW\Criteria\Iface $criteria, array $params ) : \Aimeos\MW\Criteria\Iface
	{
		$start = ( isset( $params['page']['offset'] ) ? (int) $params['page']['offset'] : 0 );
		$size = ( isset( $params['page']['limit'] ) ? (int) $params['page']['limit'] : 25 );

		return $criteria->setSlice( $start, $size );
	}


	/**
	 * Initializes the criteria object with sortations based on the given parameter
	 *
	 * @param \Aimeos\MW\Criteria\Iface $criteria Criteria object
	 * @param array $params List of criteria data with condition, sorting and paging
	 * @return \Aimeos\MW\Criteria\Iface Initialized criteria object
	 */
	protected function initCriteriaSortations( \Aimeos\MW\Criteria\Iface $criteria, array $params ) : \Aimeos\MW\Criteria\Iface
	{
		if( !isset( $params['sort'] ) ) {
			return $criteria;
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

		return $criteria->setSortations( $sortation );
	}


	/**
	 * Creates of updates several items at once
	 *
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager responsible for the items
	 * @param \stdClass $request Object with request body data
	 * @return \Aimeos\Map List of items
	 */
	protected function saveData( \Aimeos\MShop\Common\Manager\Iface $manager, \stdClass $request ) : \Aimeos\Map
	{
		$data = [];

		if( isset( $request->data ) )
		{
			foreach( (array) $request->data as $entry ) {
				$data[] = $this->saveEntry( $manager, $entry );
			}
		}

		return map( $data );
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

		if( isset( $entry->attributes ) && ( $attr = (array) $entry->attributes ) )
		{
			if( $item instanceof \Aimeos\MShop\Common\Item\Config\Iface )
			{
				$key = str_replace( '/', '.', $this->path ) . '.config';
				$attr[$key] = (array) ( $attr[$key] ?? [] );
			}

			$item = $item->fromArray( $attr, true );
		}

		$item = $manager->saveItem( $item );

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
					$listItem = $listManager->createItem()->setType( 'default' );

					if( isset( $data->attributes ) && ( $attr = (array) $data->attributes ) ) {
						$key = str_replace( '/', '.', $this->path ) . '.config';
						$attr[$key] = (array) ( $attr[$key] ?? [] );
						$listItem = $listItem->fromArray( $attr, true );
					}

					if( isset( $data->id ) ) {
						$listItem->setRefId( $data->id );
					}

					$listItem->setParentId( $id )->setDomain( $domain );
					$listManager->saveItem( $listItem, false );

					if( $domain === "product" )
					{
						$productManager = \Aimeos\MShop::create( $this->context, 'product' );
						$domains = $this->context->getConfig()->get( 'mshop/index/manager/standard/domains', [] );

						$item = $productManager->getItem( $listItem->getRefId(), $domains );
						\Aimeos\MShop::create( $this->context, 'index' )->rebuild( [$item->getId() => $item] );
					}
				}
			}
		}
	}
}
