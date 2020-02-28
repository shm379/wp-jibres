<?php
public function wp_jibres_deactivate()
{
	add_action( 'admin_notices', 'jibres_deactivate_notice' );
}


function jibres_deactivate_notice()
{
	echo '<div class="error"><p>' . __( 'Jibres Plugin Activated', 'jibres') . '</p></div>';
}
?>