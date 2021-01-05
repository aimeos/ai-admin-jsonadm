<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 * @package Admin
 * @subpackage Jsonadm
 */


$target = $this->config( 'admin/jsonadm/url/target' );
$cntl = $this->config( 'admin/jsonadm/url/controller', 'jsonadm' );
$action = $this->config( 'admin/jsonadm/url/action', 'get' );
$config = $this->config( 'admin/jsonadm/url/config', [] );


$resources = $attributes = [];
$site = $this->param( 'site', 'default' );

foreach( $this->get( 'resources', [] ) as $resource ) {
	$resources[$resource] = $this->url( $target, $cntl, $action, ['site' => $site, 'resource' => $resource], [], $config );
}

foreach( $this->get( 'attributes', [] ) as $attr ) {
	$attributes[$attr->getCode()] = $attr->toArray( true );
}


?>
{
	"meta": {
		"prefix": <?= json_encode( $this->get( 'prefix' ) ); ?>,
		"resources": <?= json_encode( $resources ); ?>,
		"attributes": <?= json_encode( $attributes ); ?>

		<?php if( $this->csrf()->name() != '' ) : ?>
			, "csrf": {
				"name": "<?= $this->csrf()->name(); ?>",
				"value": "<?= $this->csrf()->value(); ?>"
			}
		<?php endif; ?>
	}

	<?php if( isset( $this->errors ) ) : ?>
		,"errors": <?= $this->partial( $this->config( 'admin/jsonadm/partials/template-errors', 'partials/errors-standard' ), array( 'errors' => $this->errors ) ); ?>

	<?php endif; ?>

}
