<?php

$data = $this->get( 'data', array() );
$entries = array();

foreach( $data as $key => $value ) {
	$entries[] = array( 'id' => $key, 'type' => 'aggregate', 'attributes' => $value );
}

?>
{
	"meta": {
		"total": <?php echo count( $data ); ?>

	},
<?php	if( isset( $this->errors ) ) : ?>
	"errors": <?php echo $this->partial( $this->config( $this->get( 'partial-errors', 'admin/jsonadm/partials/template-errors' ), 'partials/errors-standard.php' ), array( 'errors' => $this->errors ) ); ?>
<?php	elseif( isset( $this->data ) ) : ?>
	"data": <?php echo json_encode( $entries ); ?>
<?php	endif; ?>
}
