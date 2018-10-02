<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
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


$build = function( array $attrItems, $id ) use ( $fields )
{
	$result = [];
	$type = 'criteria/attribute';

	foreach( $attrItems as $attrItem )
	{
		$attributes = $attrItem->toArray( true );

		if( isset( $fields[$type] ) ) {
			$attributes = array_intersect_key( $attributes, $fields[$type] );
		}

		$result[] = array(
			'type' => $type,
			'id' => $attrItem->getCode(),
			'attributes' => $attributes,
		);
	}

	return $result;
};


$configItems = $this->get( 'configItems', [] );


?>
{
	"meta": {
		"total": <?= count( $configItems ); ?>

	}

	<?php if( isset( $this->errors ) ) : ?>

		, "errors": <?= $this->partial( $this->config( $this->get( 'partial-errors', 'admin/jsonadm/partials/template-errors' ), 'partials/errors-standard.php' ), array( 'errors' => $this->errors ) ); ?>

	<?php else : ?>

		, "data": <?= json_encode( $build( $configItems, $this->param( 'id' ) ), $options ); ?>

	<?php endif; ?>
}
