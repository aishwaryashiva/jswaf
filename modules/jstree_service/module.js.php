function jstree_service()
{
    this.container = null;
    this.margin = 0;
    this.treeconf = null;

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
/*** the function that actually draws the tree ***/
    this.draw_tree = function( div, conf )
    {
        var JSTREE_id = 0;
        var JSTREE_common_style = "line-height:20px; margin:2px 0; padding-left:20px; cursor:pointer; font-family:sans-serif;";
        var JSTREE_parent_style = JSTREE_common_style+"background-image:url(../modules/jstree/img/arrow_open.png);background-repeat:no-repeat;";
        var JSTREE_child_style = JSTREE_common_style;

        var JSTREE_draw_tree_without_root = function( node, div, margin )
        {
            var len = node.children.length, i=0;
            for ( i=0; i<len; i++ )
                JSTREE_create_node( node.children[i], div, margin );
        }

        var JSTREE_create_node = function( node, div, margin )
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
                    "var d=MM_get_resource('JSTREE_service_dropdown');d("+JSTREE_id+");" );
                textDiv.setAttribute( "style", JSTREE_parent_style );
                JSTREE_id++;

                var i=0;
                for( i=0; i<node.children.length ; i++ )
                    JSTREE_create_node( node.children[i], childrenDiv, margin );
            }
            div.appendChild( newDiv );
        }

        if( conf.draw_root==false )
            JSTREE_draw_tree_without_root( conf.root, div, conf.margin );
        else
            JSTREE_create_node( conf.root, div, conf.margin );
    }

/* all the essentials here */
    this.init = function(conf)
    {
	this.treeconf = conf;
        return true;
    }
    this.service_request = function(res)
    {
        if( eval("this.prod."+res)!=undefined )
        {
            if( res=="JSTREE_service_dropdown" )
                return this.dropdown;
            else if( res=="jstree" )
            {
                return ( { "draw_tree":this.draw_tree, "conf":this.treeconf } );
            }
        }
        else
            return null;
    }
    this.req = <?php include('reqs.json'); ?>;
    this.prod = <?php include('prod.json'); ?>;
}


