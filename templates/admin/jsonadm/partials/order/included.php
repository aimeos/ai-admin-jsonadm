<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2023
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


$build = function( \Aimeos\Map $items ) use ( $fields )
{
	$list = [];

	$target = $this->config( 'admin/jsonadm/url/target' );
	$cntl = $this->config( 'admin/jsonadm/url/controller', 'jsonadm' );
	$action = $this->config( 'admin/jsonadm/url/action', 'get' );
	$config = $this->config( 'admin/jsonadm/url/config', [] );

	foreach( $items as $item )
	{
		$id = $item->getId();
		$type = $item->getResourceType();
		$attributes = $item->toArray( true );

		if( isset( $fields[$type] ) ) {
			$attributes = array_intersect_key( $attributes, $fields[$type] );
		}

		$entry = array(
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

		if( $item instanceof \Aimeos\MShop\Order\Item\Iface )
		{
			foreach( $item->getAddresses()->flat() as $addr ) {
				$entry['relationships']['order/address']['data'][] = ['id' => $addr->getId(), 'type' => 'order/address'];
			}

			foreach( $item->getCoupons() as $code => $products ) {
				$entry['relationships']['order/coupon']['data'][] = ['id' => $code, 'type' => 'order/coupon'];
			}

			foreach( $item->getProducts() as $prod ) {
				$entry['relationships']['order/product']['data'][] = ['id' => $prod->getId(), 'type' => 'order/product'];
			}

			foreach( $item->getServices()->flat() as $serv ) {
				$entry['relationships']['order/service']['data'][] = ['id' => $serv->getId(), 'type' => 'order/service'];
			}
		}

		if( $item instanceof \Aimeos\MShop\Order\Item\Service\Iface )
		{
			foreach( $item->getAttributeItems()->flat() as $serv ) {
				$entry['relationships']['order/service/attribute']['data'][] = ['id' => $serv->getId(), 'type' => 'order/service/attribute'];
			}
		}

		if( $item instanceof \Aimeos\MShop\Order\Item\Product\Iface )
		{
			foreach( $item->getAttributeItems()->flat() as $serv ) {
				$entry['relationships']['order/product/attribute']['data'][] = ['id' => $serv->getId(), 'type' => 'order/service/attribute'];
			}
		}

		$list[] = $entry;
	}

	return $list;
};


$response = $build( $this->get( 'childItems', map() ) );


echo json_encode( $response, $options );
