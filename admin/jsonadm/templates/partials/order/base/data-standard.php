<?php

$options = 0;
if( defined( 'JSON_PRETTY_PRINT' ) ) {
	$options = JSON_PRETTY_PRINT;
}

$build = function( \Aimeos\MW\View\Iface $view, \Aimeos\MShop\Order\Item\Base\Iface $item, array $fields, array $childItems )
{
	$id = $item->getId();
	$attributes = $item->toArray();
	$type = $item->getResourceType();
	$params = array( 'resource' => $item->getResourceType(), 'id' => $id );

	$target = $view->config( 'admin/jsonadm/url/target' );
	$cntl = $view->config( 'admin/jsonadm/url/controller', 'jsonadm' );
	$action = $view->config( 'admin/jsonadm/url/action', 'get' );
	$config = $view->config( 'admin/jsonadm/url/config', [] );

	if( isset( $fields[$type] ) ) {
		$attributes = array_intersect_key( $attributes, $fields[$type] );
	}

	$result = array(
		'id' => $id,
		'type' => $type,
		'attributes' => $attributes,
		'links' => array(
			'self' => $view->url( $target, $cntl, $action, $params, [], $config )
		),
		'relationships' => []
	);

	foreach( $childItems as $childId => $childItem )
	{
		if( $childItem->getBaseId() == $id )
		{
			$type = $childItem->getResourceType();
			$params = array( 'resource' => $childItem->getResourceType(), 'id' => $childId );

			$result['relationships'][$type][] = array( 'data' => array(
				'id' => $childId, 'type' => $type, 'links' => array(
					'self' => $view->url( $target, $cntl, $action, $params, [], $config )
				)
			) );
		}
	}

	return $result;
};


$fields = $this->param( 'fields', [] );

foreach( (array) $fields as $resource => $list ) {
	$fields[$resource] = array_flip( explode( ',', $list ) );
}


$data = $this->get( 'data', [] );
$childItems = $this->get( 'childItems', [] );

if( is_array( $data ) )
{
	$response = [];

	foreach( $data as $item ) {
		$response[] = $build( $this, $item, $fields, $childItems );
	}
}
elseif( $data !== null )
{
	$response = $build( $this, $data, $fields, $childItems );
}
else
{
	$response = null;
}


echo json_encode( $response, $options );