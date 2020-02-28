<?php
public function wp_jibres_activate()
{
	add_action( 'admin_notices', 'jibres_activate_notice' );
}


function jibres_activate_notice()
{
	echo '<div class="error"><p>' . __( 'Jibres Plugin Activated', 'jibres') . '</p></div>';
}
?>