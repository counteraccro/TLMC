//objet JS pour la pagination
Pagination = {};

Pagination.Launch = function() {

	/**
	 * Méthode Ajax qui va charger l'element présent dans l'URL
	 */
	Pagination.Ajax = function(url, id_done, method = 'GET')
	{	
		$(id_done).showLoading();
		
		$.ajax({
			method: method,
			url: url,
		})
		.done(function( html ) {
			$(id_done).html(html);
			$(id_done).hideLoading();
		});
	}
	
	/**
	 * chargement de la nouvelle page au click
	 */
	Pagination.EventChangePage = function(id, id_done)
	{
		$(id).click(function(){
				
			event.preventDefault();
			Pagination.Ajax($(this).attr('href'), id_done);
		});
	}
}