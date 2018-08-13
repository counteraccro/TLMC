//objet JS pour la pagination
Pagination = {};

Pagination.Launch = function(params) {

	//url de la fiche de l'élément
	Pagination.url_ajax_see = params.url_ajax_see;
	//cible la div des familles du patient
	Pagination.id_global = params.id_global;

	/**
	 * Méthode Ajax qui va charger l'element présent dans l'URL
	 */
	Pagination.Ajax = function(url, id_done, method = 'GET')
	{	
		$.ajax({
			method: method,
			url: url,
		})
		.done(function( html ) {
			$(id_done).html(html);
		});
	}
	
	/**
	 * chargement de la nouvelle page au click
	 */
	Pagination.EventChangePage = function(id)
	{
		$(id).click(function(){
			event.preventDefault();
			
			Pagination.Ajax($(this).attr('href'), Pagination.id_global);
		});
	}
}