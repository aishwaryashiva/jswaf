<?
function jscms_read_json_file( $conffile )
{
    $str = file_get_contents( $conffile );
    if( $str == FALSE )
        return null;
    return ( json_decode($str) );
}
function jscms_include_common_js()
{
    echo "<script type='text/javascript' src='../js/common.js'></script>\n";
}
function jscms_include_mm()
{
    GLOBAL $theme;
    $mmconftext = file_get_contents( 'mmconf.json' );
    echo <<<MM_CONF
<script type='text/javascript' src='mmconf.js.php'>
</script>

<script type='text/javascript'>

var MM_temp = MM_conf.modules.length;
MM_conf.modules[ MM_temp ] = {};
MM_conf.modules[ MM_temp ].id = '$theme';
MM_conf.modules[ MM_temp ].path = '$theme';
MM_conf.modules[ MM_temp ].remote = false;
MM_conf.modules[ MM_temp ].conf = null;


</script>
MM_CONF;

    echo "<script type='text/javascript' src='../mm/mm.js'></script>\n";
}
?>


