/*
 * MM_conf is recieved from the theme; the jscms_include_mm() function from php/jscms.php
 * does this work.
 */
var MM_i = 0;
var MM_module_array = new Array();
var MM_module_objects = new Array();
var MM_module_loaded = new Array();

var MM_avail_res = {};
var MM_depends = {};
var MM_module_id = {};
var MM_reqObj = {};

function MM_fetch_all_modules()
{
   // read all modules to be loaded
   MM_reqObj = jscms_AjaxRequest();
   if( MM_reqObj == null )
   {
       MM_show_error( MM_conf.noAjax );
       return;
   }

   MM_reqObj.onreadystatechange = MM_fetch_helper;
   var path = "";
   /* if there are no modules to load */
   if( MM_conf.modules.length == 0 )
       return;
   path = "../modules/"+MM_conf.modules[MM_i].path+"/module.js.php";
   MM_reqObj.open( "GET", path, true );
   MM_reqObj.send(null);
}

function MM_fetch_helper()
{
   if( MM_reqObj.readyState == 4 )
   {
       if( MM_reqObj.status==200 )
       {
           // store the code of all modules in this array
           MM_module_array[MM_i] = MM_reqObj.responseText;
           MM_i++;
       }
       else
       {
           MM_show_error(
                MM_conf.status_error+"HTTP status was " +
		MM_reqObj.status+" for " +
		MM_conf.modules[MM_i].id
           );
       }
       if( MM_i < MM_conf.modules.length )
       { // load the next module
            MM_reqObj = jscms_AjaxRequest();
            if( MM_reqObj == null )
            {
                MM_show_error( MM_conf.noAjax );
                return;
            }
            MM_reqObj.onreadystatechange = MM_fetch_helper;
            var path = "";
            path = "../modules/"+MM_conf.modules[MM_i].path+"/module.js.php";
            MM_reqObj.open( "GET", path, true );
            MM_reqObj.send(null);
	    return;
       }
       else	// All modules have been fetched, now initialize them.
       {
	   MM_initialize_modules();
       }
   }
}

/* check if object has and id, init(), main(), unload() and service request
 * also see that its id matches what is supplied i MM_conf;
 */
function MM_validate_module( obj, id )
{
    if( obj && obj.id && obj.init && obj.main && obj.unload && obj.service_request
	    && obj.req && obj.prod && obj.id==id )
	return true;
    else return false;
}

/* now it is time to create objects of the modules */
function MM_initialize_modules()
{
    // these are local variables for some calculation...
    var j=0, k=null, obj=null, id;
    var can_initialize_module=true, some_module_initialized_this_time = false;
    var module_initialized = new Array();

    // compile each module, create an object of it, and validate it.
    for( j=0; j<MM_module_array.length ; j++ )
    {
	id = MM_conf.modules[j].id;
        eval( MM_module_array[j] ); // compile all the modules here
        obj = eval( "new "+id+"()" ); // create a new module object
	k = MM_validate_module( obj, id );
	if( k==true )
	{
	    MM_module_objects[j] = obj;
	    MM_module_id[ id ] = j;
	}
	else
	{
	    MM_module_objects[j] = null;
	    MM_show_error( "Attempt to load invalid module "+id );
	}
    }

    // add "soft" reqs to the modules "req" list
    var softreq = null;
    for( j=0 ; j<MM_module_objects.length ; j++ )
    {
	obj = MM_module_objects[j];
	if( !obj ) continue; // this was an invalid module!
	if( !MM_conf.modules[j].conf ) continue; // if conf==null, then module has no soft reqs.
	softreq = MM_conf.modules[j].conf._req;
	// there are any configuratble requirements.. then:
	if(softreq)
	{
	    for( k in softreq )
		obj.req[k] = softreq[k];
	}
    }
       
    // flags to show that no module object has initialized, and hence not loaded yet.
    for( j=0; j<MM_module_array.length ; j++ )
         module_initialized[j] = MM_module_loaded[j] = false; 
	 

    // this BIG loop will initialize all modules, in proper order.
    // also, the main() function of the modules will be called.
    while( true )
    {
	some_module_initialized_this_time = false;
	for( j=0; j<MM_module_objects.length ; j++ )
	{
	    if( module_initialized[j]==false )
            {
		obj = MM_module_objects[j];
		if( !obj ) continue; // it is an invalid module!
		// see if all its requirements are satisfied
		can_initialize_module=true;
		for( k in obj.req )
		{
		    if( !MM_avail_res[ obj.req[k] ] )
		    {
			can_initialize_module=false;
			break;
		    }
		}
		if( can_initialize_module==false )
		    continue;

		// if all reqs are satisfied...

		// 1. initialize it
		module_initialized[j] = true;
		if( obj.init( MM_conf.modules[j].conf ) == true )
		{
		    // if initialization succeeds...
		    some_module_initialized_this_time = true;
		    // 2. record all productions in dependencies object.
		    for( k in obj.prod )
		    {
			/* check if this resource already exists */
			if( !MM_avail_res[ obj.prod[k] ] )
			{
			    MM_avail_res[ obj.prod[k] ] = j;
			    MM_depends[ obj.prod[k] ] = {};
			}
			else
			    MM_show_error( "Resource '"+obj.prod[k]+
				"' already exists! Duplication detected!!"
			    );
		    }
		    // 3. record all dependendcies: record the _index_ of the module, not id.
		    for( k in obj.req )
			MM_depends[ obj.req[k] ][ obj.id ] = j;
		    // 4. now press the GO button, and record that the module is loaded.
		    obj.main();
		    MM_module_loaded[j] = true;
		}
		else { // if module fails to initialize
		    MM_show_error( "failed to initialize module: " + obj.id );
		}
	    } //else, just continue in the loop
	}  // end of "for ..." loop
 
	// If more modules can't be initialized, then break out... 
	if( some_module_initialized_this_time==false )
	    break;
    } //All modules are initialized.
}

/* locate the object's index in the MM_module_objects[], and return its service */
function MM_get_resource( res )
{
    var index = MM_avail_res[ res ];
    if( index ) /* this means the resource exists */
    {
	if( MM_module_loaded[ index ]==false )
	{
	    MM_log( "attempted to acquire resource from unloaded module " + 
		    MM_module_objects[index].id );
	    return null;
	}
        return MM_module_objects[index].service_request(res);
    }
    MM_log( "Requested non-existent resource "+res );
    return null; /* the resource does not exist */
}

/*
 * this will load a module that has _already been fetched_ (it is present in MM_conf).
 * If the requirements of the new module are not satisfied, the:
 *     if recursive==true, it will try to load modules that provide resources it requires.
 *     else, it will fail.
 *  returns true on success, false on failure.
 *  on success, populate the MM_depends table accordingly.
 */
function MM_load_module( id, recursive )
{
    if( !id ){ MM_log( "invalid call to MM_load_module!" ); return false; }
    if( recursive==undefined )
    {	recursive = false;
	MM_log( "using default value for 'recursive' while loading module "+id );
    }
    // sanity checks...
    var index = MM_module_id[ id ];
    var obj=null;
    if( !index ){ MM_log( "invalid id "+id+" in MM_load_module!" ); return false; }
    if( !(obj=MM_module_objects[index]) )
    { MM_log( "attempting to load invalid module "+id ); return false;}
    if( MM_module_loaded[index] )
    { MM_log( "attempting to load already loaded module "+id ); return false; }

    var i, k;
    for( i in obj.req )
    {
	k = MM_avail_res[ obj.req[i] ];
	if( !k ) // the resource does not exist!
	{
	    MM_log( "Failed to load module "+id+
		    "; Required resource "+obj.req[i]+" not available!" );
	    return false;
	}
	// the req is not produced by self, and the producing module is not loaded.
	if( k!=index &&  MM_module_loaded[ k ]==false )
	{
	    // check the recursive stuff here
	    if( recursive==false )
	    {
		MM_log( "Producer of required resource "+obj.req[i]+" not loaded." );
		return false;
	    }
	    else { // load the module that produces obj.req[i]	
		if( MM_load_module(MM_module_objects[k].id ,true) == false )
		{
		    MM_log( "failed to load module "+id );
		    return false;
		}
	    }
	}
    }

    // try initialising the object
    if( obj.init(MM_conf.modules[index].conf) == false )
    {
	MM_error("Failed to initialize module "+obj.id);
	return false;
    }

    // now all requirements are fulfilled. Now add the prods and reqs to MM_depends
    for( i in obj.prod )
    {
	// if that resource already exists, stop!
	if( MM_depends[ obj.prod[i] ] )
	{
	    MM_show_error( "Failed to load module "+id+
			   "Resource "+obj.prod[i]+" already exists." );
	    return false;
	}
	MM_depends[ obj.prod[i] ] = {};
    }

    for( i in obj.req )
	MM_depends[ obj.req[i] ][ obj.id ] = index;
    obj.main();
    MM_module_loaded[ index ] = true;
    return true; // at long last!
}

/*
 * locate the object in module having id=@arg{id}, and unload it.
 * DONT remove its productions from the MM_avail_res! just set MM_module_loaded[j] = false.
 * if recursive==false: if other modules depend on its productions, it will not be unloaded.
 * else: all depending modules will be unloaded.
 */
function MM_unload_module( id, recursive )
{
    if( !id ){ MM_log( "invalid call to MM_unload_module!" ); return false; }
    if( recursive==undefined )
    {
	recursive = false;
	MM_log( "using default value for 'recursive' while unloading module "+id );
    }
	
    var index = MM_module_id[ id ];
    var obj = null;
    if( !index || !(obj=MM_module_objects[index]) )
    {
	MM_show_error( "Attempt to unload non-existent module " + id );
	return false;
    }
    // now check if other modules depend on its produced resources.
    var i,j=null;
    for( i in obj.prod )
	for( j in MM_depends[ obj.prod[i] ] )
	{
	    // if some _other_ module depends on obj.prod[i], then... 
	    if( j!=obj.id )
	    {
		if( recursive==false ) return false;
		else if( MM_unload_module(j,true)==false ) return false;
	    }
	}
    // it is now safe to unload the module!
    if( obj.unload()==true )
    {
	// delete production entries from MM_depends
	for( i in obj.prod )
	    MM_depends[ obj.prod[i] ] = null;

	// delete requirement entries from MM_depends
	for( j in obj.req )
	    MM_depends[ obj.req[j] ][ obj.id ] = null;

	// set the module to be "unloaded";
	MM_module_loaded[ index ] = false;
	MM_log( "Module "+obj.id+" unloaded." );
	return true;
    }
    return false;
}

function MM_show_error( str )
{
    // MM_log( str );
    alert( str );
}
function MM_log( str )
{
    // alert( str );
}

MM_fetch_all_modules();
