<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2017
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


$build = function( \Aimeos\MShop\Locale\Item\Site\Iface $item ) use ( $fields )
{
	$id = $item->getId();
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

	foreach( $item->getChildren() as $childItem )
	{
		$type = $childItem->getResourceType();
		$result['relationships'][$type][] = array( 'data' => array( 'id' => $childItem->getId(), 'type' => $type ) );
	}

	return $result;
};


$data = $this->get( 'data', [] );

if( is_array( $data ) )
{
	$response = [];

	foreach( $data as $item ) {
		$response[] = $build( $item );
	}
}
elseif( $data !== null )
{
	$response = $build( $data );
}
else
{
	$response = null;
}


echo json_encode( $response, $options );