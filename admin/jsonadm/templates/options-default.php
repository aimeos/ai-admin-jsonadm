<?php

$target = $this->config( 'admin/jsonadm/url/target' );
$cntl = $this->config( 'admin/jsonadm/url/controller', 'jsonadm' );
$action = $this->config( 'admin/jsonadm/url/action', 'get' );
$config = $this->config( 'admin/jsonadm/url/config', [] );

$resources = $attributes = [];
$site = $this->param( 'site', 'default' );

foreach( $this->get( 'resources', [] ) as $resource ) {
	$resources[$resource] = $this->url( $target, $cntl, $action, array( 'site' => $site, 'resource' => $resource, 'id' => '' ), [], $config );
}

foreach( $this->get( 'attributes', [] ) as $attr ) {
	$attributes[$attr->getCode()] = $attr->toArray();
}

?>
{
	"meta": {
		"prefix": <?php echo json_encode( $this->get( 'prefix' ) ); ?>,
		"resources": <?php echo json_encode( $resources ); ?>,
		"attributes": <?php echo json_encode( $attributes ); ?>
	}
<?php if( isset( $this->errors ) ) : ?>
	,"errors": <?php echo $this->partial( $this->config( 'admin/jsonadm/partials/template-errors', 'partials/errors-standard.php' ), array( 'errors' => $this->errors ) ); ?>
<?php endif; ?>

}
