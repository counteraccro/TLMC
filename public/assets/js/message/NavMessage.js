NavMessage = {};

NavMessage.Launch = function(params){
	
	NavMessage.urlNbMessageNoRead = params.urlNbMessageNoRead;

	NavMessage.GetNbMessageNoRead = function()
	{
		$.ajax({
			method: 'GET',
			url: NavMessage.urlNbMessageNoRead,
		})
		.done(function( json ) {
			
			if(json.nb_message == 0)
		    {
				$('.navbar-nav .nb-message-no-read').html('');
				$('.navbar-nav .nb-message-no-read-link').html('<span class="oi oi-envelope-closed"></span> Ma messagerie');
		    }
			else
			{
				var px = '';
				var ps = '';
				if(json.nb_message > 1)
			    {
					px = 'x';
					ps = 's';
			    }
				
				$('.navbar-nav .nb-message-no-read').html('<span class="badge badge-primary">' + json.nb_message + '</span>');
				$('.navbar-nav .nb-message-no-read-link').html(' <span class="badge badge-primary">' + json.nb_message + '</span> nouveau' + px + ' message' + ps);
			}
		});
	}
}