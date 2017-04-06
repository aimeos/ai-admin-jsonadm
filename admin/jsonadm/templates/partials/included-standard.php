<?php

$options = 0;
if( defined( 'JSON_PRETTY_PRINT' ) ) {
	$options = JSON_PRETTY_PRINT;
}


$build = function( \Aimeos\MW\View\Iface $view, array $items, array $fields )
{
	$list = [];

	$target = $view->config( 'admin/jsonadm/url/target' );
	$cntl = $view->config( 'admin/jsonadm/url/controller', 'jsonadm' );
	$action = $view->config( 'admin/jsonadm/url/action', 'get' );
	$config = $view->config( 'admin/jsonadm/url/config', [] );

	foreach( (array) $items as $item )
	{
		$id = $item->getId();
		$attributes = $item->toArray();
		$type = $item->getResourceType();

		if( isset( $fields[$type] ) ) {
			$attributes = array_intersect_key( $attributes, $fields[$type] );
		}

		$list[] = array(
			'id' => $id,
			'type' => $type,
			'attributes' => $attributes,
			'links' => array(
				'self' => $view->url( $target, $cntl, $action, array( 'resource' => $type, 'id' => $id ), [], $config ),
				'related' => array(
					'href' => $view->url( $target, $cntl, $action, array( 'resource' => $type, 'id' => null ), [], $config )
				)
			)
		);
	}

	return $list;
};


$response = [];
$fields = $this->param( 'fields', [] );

foreach( (array) $fields as $resource => $list ) {
	$fields[$resource] = array_flip( explode( ',', $list ) );
}

$response = $build( $this, $this->get( 'childItems', [] ), $fields );
$response = array_merge( $response, $build( $this, $this->get( 'refItems', [] ), $fields ) );


echo json_encode( $response, $options );