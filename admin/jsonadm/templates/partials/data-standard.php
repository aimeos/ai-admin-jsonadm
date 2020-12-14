<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2020
 * @package Admin
 * @subpackage Jsonadm
 */


$options = 0;
if( defined( 'JSON_PRETTY_PRINT' ) ) {
	$options = JSON_PRETTY_PRINT;
}


$fields = $this->param( 'fields', [] );

foreach( (array) $fields as $resource => $list ) {
	$fields[$resource] = array_flip( explode( ',', $list ) );
}


$build = function( \Aimeos\MShop\Common\Item\Iface $item, \Aimeos\Map $childItems, \Aimeos\Map $listItems ) use ( $fields )
{
	$id = strtoupper( $item->getId() );
	$type = $item->getResourceType();
	$params = array( 'resource' => $type, 'id' => $id );
	$attributes = $item->toArray( true );

	$target = $this->config( 'admin/jsonadm/url/target' );
	$cntl = $this->config( 'admin/jsonadm/url/controller', 'jsonadm' );
	$action = $this->config( 'admin/jsonadm/url/action', 'get' );
	$config = $this->config( 'admin/jsonadm/url/config', [] );

	if( isset( $fields[$type] ) ) {
		$attributes = array_intersect_key( $attributes, $fields[$type] );
	}

	$result = array(
		'id' => $id,
		'type' => $type,
		'attributes' => $attributes,
		'links' => array(
			'self' => $this->url( $target, $cntl, $action, $params, [], $config )
		),
		'relationships' => []
	);

	foreach( $childItems as $childItem )
	{
		if( $childItem->getParentId() == $id )
		{
			$type = $childItem->getResourceType();
			$result['relationships'][$type][] = array( 'data' => array( 'id' => $childItem->getId(), 'type' => $type ) );
		}
	}

	if( $item instanceof \Aimeos\MShop\Common\Item\Lists\Iface )
	{
		$result['relationships'][$item->getDomain()][] = ['data' => [
			'type' => $item->getDomain(),
			'id' => $item->getRefId()
		]];
	}

	foreach( $listItems as $listId => $listItem )
	{
		if( $listItem->getParentId() == $id )
		{
			$type = $listItem->getDomain();
			$params = array( 'resource' => $listItem->getResourceType(), 'id' => $listId );

			$result['relationships'][$type][] = array( 'data' => array(
				'id' => $listItem->getRefId(),
				'type' => $type,
				'attributes' => $listItem->toArray( true ),
				'links' => array(
					'self' => $this->url( $target, $cntl, $action, $params, [], $config )
				)
			) );
		}
	}

	return $result;
};


$data = $this->get( 'data', [] );
$childItems = $this->get( 'childItems', map() );
$listItems = $this->get( 'listItems', map() );

if( is_map( $data ) )
{
	$response = [];

	foreach( $data as $item ) {
		$response[] = $build( $item, $childItems, $listItems );
	}
}
elseif( $data !== null )
{
	$response = $build( $data, $childItems, $listItems );
}
else
{
	$response = null;
}


echo json_encode( $response, $options );
