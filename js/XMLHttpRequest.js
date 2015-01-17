var HTTP = {};
HTTP._factories = [
	function(){return new XMLHttpRequest();},
	function(){return new ActiveXObject("Msxml2.XMLHTTP");},
	function(){return new ActiveXObject("Microsoft.XMLHTTP");}
];

HTTP.newRequest = function(){
	if(HTTP._factory != null) return HTTP._factory();
	for(var i=0; i < HTTP._factories.length; i++){
		try{
			var factory = HTTP._factories[i];
			var request = factory();
			if(request != null){
				HTTP._factory = factory;
				return request;
			}
		}catch(e){
			continue;
		}
	}
	HTTP._factory = new function(){
		throw new Error("XMLHttpRequest not supported");
	}
	HTTP._factory();
}