<?php
    include( '../php/jscms.php' );
    $pageconf = jscms_read_json_file( 'pageconf.json' );
    $theme = null;
    if( isset( $_GET['theme'] ) )
	$theme = $_GET['theme'];
    else
	$theme = $pageconf->theme;
?>

<?php    include( "../themes/$theme/index.theme" );	?>
