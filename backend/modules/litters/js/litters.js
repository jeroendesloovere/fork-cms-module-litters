if(!jsBackend){
	var jsBackend = new Object();
}

jsBackend.litters = {
	init: function(){
		// set up meta field
		if($('#name').length > 0) $('#name').doMeta();
	},

	// end
	eoo: true
}

$(jsBackend.litters.init);