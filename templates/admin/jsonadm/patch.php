<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2023
 * @package Admin
 * @subpackage Jsonadm
 */


?>
{
	"meta": {
		"total": <?= $this->get( 'total', 0 ); ?>

		<?php if( $this->csrf()->name() != '' ) : ?>
			, "csrf": {
				"name": "<?= $this->csrf()->name(); ?>",
				"value": "<?= $this->csrf()->value(); ?>"
			}
		<?php endif; ?>

	}

	<?php if( isset( $this->errors ) ) : ?>
		, "errors": <?= $this->partial( $this->config( 'admin/jsonadm/partials/template-errors', 'partials/errors' ), array( 'errors' => $this->errors ) ); ?>

	<?php elseif( isset( $this->data ) ) : ?>
		, "data": <?= $this->partial( $this->config( 'admin/jsonadm/partials/template-data', 'partials/data' ), array( 'data' => $this->get( 'data' ) ) ); ?>

	<?php endif; ?>

}
