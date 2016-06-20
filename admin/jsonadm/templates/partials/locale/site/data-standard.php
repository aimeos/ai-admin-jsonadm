<?php

$options = 0;
if( defined( 'JSON_PRETTY_PRINT' ) ) {
	$options = JSON_PRETTY_PRINT;
}


$build = function( \Aimeos\MW\View\Iface $view, \Aimeos\MShop\Locale\Item\Site\Iface $item, array $fields )
{
	$id = $item->getId();
	$attributes = $item->toArray();
	$type = $item->getResourceType();
	$params = array( 'resource' => $item->getResourceType(), 'id' => $id );

	$target = $view->config( 'admin/jsonadm/url/target' );
	$cntl = $view->config( 'admin/jsonadm/url/controller', 'jsonadm' );
	$action = $view->config( 'admin/jsonadm/url/action', 'get' );
	$config = $view->config( 'admin/jsonadm/url/config', array() );

	if( isset( $fields[$type] ) ) {
		$attributes = array_intersect_key( $attributes, $fields[$type] );
	}

	$result = array(
		'id' => $id,
		'type' => $type,
		'attributes' => $attributes,
		'links' => array(
			'self' => $view->url( $target, $cntl, $action, $params, array(), $config )
		),
		'relationships' => array()
	);

	foreach( $item->getChildren() as $childItem )
	{
		$type = $childItem->getResourceType();
		$result['relationships'][$type][] = array( 'data' => array( 'id' => $childItem->getId(), 'type' => $type ) );
	}

	return $result;
};


$fields = $this->param( 'fields', array() );

foreach( (array) $fields as $resource => $list ) {
	$fields[$resource] = array_flip( explode( ',', $list ) );
}


$data = $this->get( 'data', array() );

if( is_array( $data ) )
{
	$response = array();

	foreach( $data as $item ) {
		$response[] = $build( $this, $item, $fields );
	}
}
elseif( $data !== null )
{
	$response = $build( $this, $data, $fields );
}
else
{
	$response = null;
}


echo json_encode( $response, $options );