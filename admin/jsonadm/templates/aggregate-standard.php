<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2018
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
		"total": <?= count( $data ); ?>

		<?php if( $this->csrf()->name() != '' ) : ?>
			, "csrf": {
				"name": "<?= $this->csrf()->name(); ?>",
				"value": "<?= $this->csrf()->value(); ?>"
			}
		<?php endif; ?>

	},

	<?php if( isset( $this->errors ) ) : ?>

		"errors": <?= $this->partial( $this->config( $this->get( 'partial-errors', 'admin/jsonadm/partials/template-errors' ), 'partials/errors-standard.php' ), array( 'errors' => $this->errors ) ); ?>

	<?php elseif( isset( $this->data ) ) : ?>

		"data": <?= json_encode( $entries ); ?>

	<?php endif; ?>
}
