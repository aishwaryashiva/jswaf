function fader()
{
    this.id = "fader";
    this.FADER_conf = null;
    this.container = null;
    this.FADER_div = null;

    this.call_timer = function( id,timeout,opacity )
    {
        var conf = MM_get_resource( 'FADER_conf' );
	    if( !conf ) return;
        var set_opacity = function(opacity,id)
        {
            var obj =document.getElementById(id).style;
	    if(!obj) return;
            obj.opacity=(opacity/100);
            if( obj.opacity!=null )
                return;
            obj.MozOpacity=(opacity/100);
            obj.KhtmlOpacity=(opacity/100);
            obj.filter="alpha(opacity = " + opacity + ")";
        }
        var change_source = function(id)
        {
            if( conf.current_image >= conf.image.length )
                conf.current_image=0;
            document.getElementById(id).src = conf.cache[ conf.current_image++ ].src;
        }    

        var obj = document.getElementById( id );
	if( !obj ) return;
        var state = obj.getAttribute("state");
        var rate = obj.getAttribute("rate");

        if( rate!=null )
            rate = parseInt( rate );
    
        if(state == null)   //new page load state
        {
            obj.setAttribute("state","0");
            obj.setAttribute("rate", "1" );
            setTimeout(
                "var v=MM_get_resource('FADER_fade_timer');if(v)v('"+id+"',"+timeout+","+opacity+");",
                100
            );
        }
        if(state == "0")    //fade-out state
        {
            opacity -= rate;
            rate=rate + conf.fade_speed;
            if( opacity <= 0 )
            {
                set_opacity(0,id);
                change_source(id);
                obj.setAttribute("state","1");
                obj.setAttribute("rate", "0" );
            }
            else
            {
                set_opacity(opacity,id);
                obj.setAttribute("rate", rate );
            }
            setTimeout(
                "var v=MM_get_resource('FADER_fade_timer');if(v)v('"+id+"',"+timeout+","+opacity+");",
                conf.fadeout_time
            );
        }
        else if(state == "1")    //fade-in state
        {
            opacity += rate;
            rate = rate + conf.fade_speed;
            if( opacity >= 100 )
            {
                set_opacity( 100, id );
                obj.setAttribute("state","2");
                obj.setAttribute("rate", "0" );
            }
            else
            {
                set_opacity(opacity,id);
                obj.setAttribute("rate", rate );
            }
            setTimeout(
                "var v=MM_get_resource('FADER_fade_timer');if(v)v('"+id+"',"+timeout+","+opacity+");",
                 conf.fadein_time
            );
        }
        else if(state== "2") // waiting state of the function
        {
            obj.setAttribute("state","0");
            setTimeout(
                "var v=MM_get_resource('FADER_fade_timer');if(v)v('"+id+"',"+timeout+","+opacity+");",
                conf.waiting_time
            );
        }
    }

    this.main_fader = function( div )
    {
        var k = 1000;
        var i=0;
        for( i = 0; i<this.FADER_conf.photo_displayed; i++)
        {
                var newdiv = document.createElement('img');
                var s = "FADER_image"+i;
                newdiv.setAttribute('id',s);
		if( this.FADER_conf.current_image >= this.FADER_conf.image.length )
		    this.FADER_conf.current_image = 0;
                newdiv.src = this.FADER_conf.cache[ this.FADER_conf.current_image++ ].src;
                newdiv.setAttribute('style','padding:10px;');
                newdiv.setAttribute('width',this.FADER_conf.width);
                newdiv.setAttribute('height',this.FADER_conf.height);
                div.appendChild(newdiv);
                setTimeout( 
                    "var v=MM_get_resource('FADER_fade_timer');if(v)v('"+s+"',1000,100);", 
                    k+i*200
                );
        }
    }

    /* all the routine stuff */
    this.init = function(conf_arg)
    {
	this.FADER_conf = conf_arg;
        this.container = MM_get_resource( this.req.placeholder );
        if(this.container == null)
            return false;

       if(this.FADER_conf.image.length == 0)
            return false;

        for(i=0;i<this.FADER_conf.image.length;i++)
        {
            this.FADER_conf.cache[i]=new Image();
            this.FADER_conf.cache[i].src = this.FADER_conf.image[i];
        }

        this.FADER_div = document.createElement('div');
        this.FADER_div.setAttribute("style","background-color:#444;float:left");
        this.container.appendChild(this.FADER_div);
        return true;
    } 
    this.main = function()
    {
        this.main_fader( this.FADER_div );
    }
    this.unload = function()
    {
	this.container.removeChild(this.FADER_div);
	this.container.parentNode.removeChild( this.container );
	return true;
    }
    this.service_request = function(res)
    {
        if(eval("this.prod."+res) !=undefined )
        {
            if(res == 'FADER_container')
                return this.FADER_div;
            else if( res == 'FADER_fade_timer' )
                return this.call_timer;
            else if( res == 'FADER_conf' )
                return this.FADER_conf;
        }
        return null;
                
    }
    this.req = <?php include('reqs.json'); ?>;
    this.prod = <?php include('prod.json');?>;
}


function FADER_myGetElementById(id)
{
    if(document.getElementById)
    {
        return document.getElementById(id);
    }
    else if(document.all)
    {
        return document.all[id];
    }else if(document.layers)
    { return document.layers[id]; }
    else // uh oh...  
    { return null; }
}






