function transparentia()
{
    this.id = "transparentia";
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
        this.header = document.getElementById( "theheader" );
        if( this.header==null )
            return false;

        this.body = document.getElementById( "thecontent" );
        if( this.body==null )
            return false;

        this.sidebar = document.getElementById( "thesidebar" );
        if( this.sidebar==null )
            return false;

        this.footer = document.getElementById( "thefooter" );
        if( this.footer==null )
            return false;

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
                return this.header;
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
                return this.footer;
        }
        else
            return null; // invalid resource request
    }
    this.req = <?php include('reqs.json'); ?>
    this.prod = <?php include('prod.json'); ?>
}
