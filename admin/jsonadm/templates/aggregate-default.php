<?php

$data = $this->get( 'data', array() );

?>
{
	"meta": {
		"total": <?php echo count( $data ); ?>

	},
<?php	if( isset( $this->errors ) ) : ?>
	"errors": <?php echo $this->partial( $this->config( $this->get( 'partial-errors', 'admin/jsonadm/partials/template-errors' ), 'partials/errors-standard.php' ), array( 'errors' => $this->errors ) ); ?>
<?php	elseif( isset( $this->data ) ) : ?>
	"data": [
<?php		foreach( $data as $key => $value ) : ?>
<?php			echo json_encode( array( 'id' => $key, 'type' => 'aggregate', 'attributes' => $value ) ); ?>
<?php	 	endforeach; ?>
    ]
<?php	endif; ?>
}
