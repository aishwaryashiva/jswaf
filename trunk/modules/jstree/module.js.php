var JSTREE_treeconf = null;

function jstree()
{
    this.id = 'jstree';
    this.container = null;
    this.margin = 0;

    this.dropdown = function( childid )
    {
        var obj = document.getElementById( "JSTREE_children_"+childid );
        var par = document.getElementById( "JSTREE_text_"+childid );
        if( obj==null || par==null )
            return;
        if(obj.style.display=="none")
        {
            obj.style.display="";        
            par.style.backgroundImage = "url(../modules/jstree/img/arrow_open.png)";
        }
        else
        {
            obj.style.display="none";
            par.style.backgroundImage = "url(../modules/jstree/img/arrow_close.png)";
        }
    }

/* all the essentials here */
    this.init = function(conf_arg)
    {
	JSTREE_treeconf = conf_arg;
        this.container = MM_get_resource( this.req.placeholder );
        if( this.container == null )
        {
            return false;
        }
        this.margin = JSTREE_treeconf.margin;
        if( JSTREE_treeconf.draw_root == true )
        {
            JSTREE_create_node( JSTREE_treeconf.root, this.container, this.margin );
            return true;
        }
        else 
        {
            JSTREE_draw_tree_without_root( JSTREE_treeconf.root, this.container, this.margin );
            return true;
        }
    }
    this.main = function() {}
    this.service_request = function(res)
    {
        if( eval("this.prod."+res)!=undefined )
        {
            if( res=="JSTREE_dropdown" )
                return this.dropdown;
        }
        else
            return null;
    }
    this.unload = function() 
    {
	if( this.container )
	    this.container.parentNode.removeChild(this.container);
	JSTREE_id=0;
	return true;
    }
    this.req = <?php include('reqs.json'); ?>;
    this.prod = <?php include('prod.json'); ?>;
    this.css = <?php include('css.json'); ?>;
}

    var JSTREE_id = 0;
    var JSTREE_common_style = "line-height:20px; margin:2px 0; padding-left:20px; cursor:pointer; font-family:sans-serif;";
    var JSTREE_parent_style = JSTREE_common_style+"background-image:url(../modules/jstree/img/arrow_open.png);background-repeat:no-repeat;";
    var JSTREE_child_style = JSTREE_common_style;

    function JSTREE_draw_tree_without_root( node, div, margin )
    {
        var len = node.children.length, i=0;
        for ( i=0; i<len; i++ )
            JSTREE_create_node( node.children[i], div, margin );
    }

    function JSTREE_create_node( node, div, margin )
    {
        var newDiv = document.createElement( 'div' );
        var textDiv =  document.createElement( 'span' );
        var theText = document.createTextNode(node.title);

        newDiv.appendChild( textDiv );

        newDiv.setAttribute( "id", "JSTREE_node_"+JSTREE_id );
        textDiv.setAttribute( "id", "JSTREE_text_"+JSTREE_id );

        if( node.children == null )
        {
            if( node.type=="html" ) /* in case of a proper HTML link */
            {
                var link = document.createElement( 'a' );
                link.href = node.link;
                link.appendChild( theText );
                textDiv.appendChild( link );
            }
            else if( node.type=="js" ) /* in case of a javascript link */
            {
                textDiv.appendChild( theText );
                textDiv.setAttribute("onclick", node.link);
            }
            textDiv.setAttribute("style", JSTREE_child_style );
            JSTREE_id++;
        }
        else
        {
            var childrenDiv = document.createElement('div');
            newDiv.appendChild( childrenDiv );

            childrenDiv.setAttribute( "style", "margin-left:"+margin+"px" );
            childrenDiv.setAttribute( "id", "JSTREE_children_"+JSTREE_id );

            textDiv.appendChild( theText );
            textDiv.setAttribute( "onclick",
                    "var d=MM_get_resource('JSTREE_dropdown');d("+JSTREE_id+");" );
            textDiv.setAttribute( "style", JSTREE_parent_style );
            JSTREE_id++;

            var i=0;
            for( i=0; i<node.children.length ; i++ )
                JSTREE_create_node( node.children[i], childrenDiv, margin );
        }
        div.appendChild( newDiv );
    }


