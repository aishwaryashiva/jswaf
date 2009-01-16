function jscms_AjaxRequest()
{
    var reqObj = null;
    try{
        reqObj = new XMLHttpRequest();
    } catch(e) {
    try { // ie6
        reqObj = new ActiveXObject( "Msxml2.XMLHTTP" );
    } catch(e) {
    try {
        reqObj = new ActiveXObject( "Microsoft.XMLHTTP" );
    }
    catch(e) {
        reqObj = null;
    }}}
    return reqObj;
}
