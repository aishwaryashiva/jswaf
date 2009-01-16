function counter()
{
    this.id = "counter";
    this.count = 0;
    this.getCount = function() { return this.count; },
    this.hitCounter = function() { return (++this.count); },
    this.resetCounter = function() { this.count=0; return this.count; }

    this.display = null;

    this.init = function( conf ) 
    {
        return true;
    }
    this.main = function(){}
    this.service_request = function( res )
    {
        if( eval("this.prod."+res)!=undefined )
            return this;
        else
            return null;
    }
    this.unload = function() {}
    this.req = <?php include('reqs.json'); ?>
    this.prod = <?php include('prod.json'); ?>
}
