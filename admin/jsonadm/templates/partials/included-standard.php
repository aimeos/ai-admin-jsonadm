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


$build = function( array $items ) use ( $fields )
{
	$list = [];

	$target = $this->config( 'admin/jsonadm/url/target' );
	$cntl = $this->config( 'admin/jsonadm/url/controller', 'jsonadm' );
	$action = $this->config( 'admin/jsonadm/url/action', 'get' );
	$config = $this->config( 'admin/jsonadm/url/config', [] );

	foreach( (array) $items as $item )
	{
		$id = $item->getId();
		$type = $item->getResourceType();
		$attributes = $item->toArray( true );

		if( isset( $fields[$type] ) ) {
			$attributes = array_intersect_key( $attributes, $fields[$type] );
		}

		$list[] = array(
			'id' => $id,
			'type' => $type,
			'attributes' => $attributes,
			'links' => array(
				'self' => $this->url( $target, $cntl, $action, array( 'resource' => $type, 'id' => $id ), [], $config ),
				'related' => array(
					'href' => $this->url( $target, $cntl, $action, array( 'resource' => $type, 'id' => null ), [], $config )
				)
			)
		);
	}

	return $list;
};


$response = [];
$response = $build( $this->get( 'childItems', [] ) );
$response = array_merge( $response, $build( $this->get( 'refItems', [] ) ) );


echo json_encode( $response, $options );