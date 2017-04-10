<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2017
 * @package Admin
 * @subpackage Jsonadm
 */


$data = $this->get( 'data', [] );
$entries = [];

foreach( $data as $key => $value ) {
	$entries[] = array( 'id' => $key, 'type' => 'aggregate', 'attributes' => $value );
}

?>
{
	"meta": {
		"total": <?php echo count( $data ); ?>

	},

	<?php if( isset( $this->errors ) ) : ?>

		"errors": <?php echo $this->partial( $this->config( $this->get( 'partial-errors', 'admin/jsonadm/partials/template-errors' ), 'partials/errors-standard.php' ), array( 'errors' => $this->errors ) ); ?>

	<?php elseif( isset( $this->data ) ) : ?>

		"data": <?php echo json_encode( $entries ); ?>

	<?php endif; ?>
}
