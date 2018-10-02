<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2018
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


$build = function( \Aimeos\MShop\Order\Item\Iface $item, array $childItems ) use ( $fields )
{
	$id = $item->getId();
	$baseId = $item->getBaseId();
	$type = $item->getResourceType();
	$params = array( 'resource' => $item->getResourceType(), 'id' => $id );
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

	foreach( $childItems as $childId => $childItem )
	{
		if( $childItem instanceof \Aimeos\MShop\Order\Item\Status\Iface && $childItem->getParentId() == $id
			|| $childItem instanceof \Aimeos\MShop\Order\Item\Base\Iface && $childItem->getId() == $baseId
		) {
			$type = $childItem->getResourceType();
			$params = array( 'resource' => $childItem->getResourceType(), 'id' => $childId );

			$result['relationships'][$type][] = array( 'data' => array(
				'id' => $childId, 'type' => $type,
				'links' => array(
					'self' => $this->url( $target, $cntl, $action, $params, [], $config )
				)
			) );
		}
	}

	return $result;
};


$data = $this->get( 'data', [] );
$childItems = $this->get( 'childItems', [] );

if( is_array( $data ) )
{
	$response = [];

	foreach( $data as $item ) {
		$response[] = $build( $item, $childItems );
	}
}
elseif( $data !== null )
{
	$response = $build( $data, $childItems );
}
else
{
	$response = null;
}


echo json_encode( $response, $options );