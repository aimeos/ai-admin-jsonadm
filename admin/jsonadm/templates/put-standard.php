<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 * @package Admin
 * @subpackage Jsonadm
 */


?>
{
	<?php if( isset( $this->errors ) ) : ?>
		"errors": <?= $this->partial( $this->config( 'admin/jsonadm/partials/template-errors', 'partials/errors-standard' ), array( 'errors' => $this->errors ) ); ?>

	<?php endif; ?>

}
