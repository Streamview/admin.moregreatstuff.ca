/**
 * 
 */
var app = {
	    
	notySuccess:function (text, additional_options) {
		var options = {
			text: text,
			layout: 'bottomRight',
		    theme: 'relax',
		    type: 'success',
		    timeout:3000,
		    animation: {
			    open:'animated tada', // jQuery animate function property object
			    close:'animated rotateOutDownRight', // jQuery animate function property object
			    easing:'swing', // easing
			    speed: 500 // opening & closing animation speed
			}, 
			layout:'bottomRight'
		};
		
		noty(mergeObjects(options, additional_options));
	},
	
	notyError:function (text) {
		noty({
			text: text,
			layout: 'bottomRight',
		    theme: 'relax',
		    type: 'error',
		    timeout:5000,
		    animation: {
			    open:'animated bounceInLeft', // jQuery animate function property object
			    close:'animated rotateOutDownRight', // jQuery animate function property object
			    easing:'swing', // easing
			    speed: 500 // opening & closing animation speed
			}, 
			layout:'bottomRight'
		});
	},
	
	notyConfirm:function (text, ok_callback, cancel_callback) {
		noty({
			text: text,
			layout: 'bottomRight',
		    theme: 'relax',
		    type: 'information',
		    timeout:3000,
		    animation: {
			    open:'animated bounceInLeft', // jQuery animate function property object
			    close:'animated rotateOutDownRight', // jQuery animate function property object
			    easing:'swing', // easing
			    speed: 500 // opening & closing animation speed
			},
			layout:'bottomRight', 
			buttons:[{
				addClass: 'btn btn-primary', 
				text: 'Ok', 
				onClick:ok_callback
		  		},
		  		{
		  			addClass: 'btn btn-danger', 
		  			text: 'Cancel', 
			  		onClick: cancel_callback
				}
		  	]

		});		
	},
};