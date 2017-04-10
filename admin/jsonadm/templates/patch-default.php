<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2017
 * @package Admin
 * @subpackage Jsonadm
 */


?>
{
	"meta": {
		"total": <?php echo $this->get( 'total', 0 ); ?>

	}

	<?php if( isset( $this->errors ) ) : ?>

		, "errors": <?php echo $this->partial( $this->config( 'admin/jsonadm/partials/template-errors', 'partials/errors-standard.php' ), array( 'errors' => $this->errors ) ); ?>

	<?php elseif( isset( $this->data ) ) : ?>

		, "data": <?php echo $this->partial( $this->config( 'admin/jsonadm/partials/template-data', 'partials/data-standard.php' ), array( 'data' => $this->get( 'data' ) ) ); ?>

	<?php endif; ?>

}
