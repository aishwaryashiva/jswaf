function greenlush()
{
    this.id = "greenlush";
    this.header = null;
    this.sidebar = null;
    this.body = null;
    this.footer = null;

    this.getHeader  = function() { return this.header; }
    this.getSidebar = function() { return this.sidebar; }
    this.getBody    = function() { return this.body; }
    this.getFooter  = function() { return this.footer; }

    this.init = function(conf) 
    {
        var temp = document.getElementById( "header" );
        if( temp==null )
            return false;
        this.header = temp.childNodes[0];

        this.body = document.getElementById( "maincontent" );
        if( this.body==null )
            return false;

        this.sidebar = document.getElementById( "sidebar" );
        if( this.sidebar==null )
            return false;

        temp = document.getElementById( "footer" );
        if( temp==null )
            return false;
        this.footer = temp;

        return true;
    }
    this.main = function() {}
    this.unload = function() { return false; }

    this.service_request = function( res )
    {
        // see if this module is supposed to provide for this resource
        if( eval("this.prod."+res)!=undefined ) 
        {
            // valid resource request...
            if( res=="header" )
            {
                var newHeaderItem = document.createElement('div');
                this.header.appendChild(newHeaderItem);
                return newHeaderItem;
            }
            else if( res=="body" )
            {
                var newInsideContent = document.createElement('div');
                newInsideContent.setAttribute( 'class', 'insidecontent' );
                this.body.appendChild(newInsideContent);
                return newInsideContent;
            }
            else if( res=="sidebar" )
            {
                var newSidebarItem = document.createElement('div');
                newSidebarItem.setAttribute('class','sidebar-item');
                this.sidebar.appendChild(newSidebarItem);
                return newSidebarItem;
            }
            else if( res=="footer" )
            {
                var newFooterItem = document.createElement('div');
                this.footer.appendChild(newFooterItem);
                return newFooterItem;
            }
        }
        else
            return null; // invalid resource request
    }
    this.req = <?php include('reqs.json'); ?>
    this.prod = <?php include('prod.json'); ?>
}
