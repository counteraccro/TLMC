//objet JS pour la pagination
Pagination = {};

Pagination.Launch = function(params) {

	//url de la fiche de l'élément
	Pagination.url_ajax_see = params.url_ajax_see;
	//cible la div de la pagination
	Pagination.id_global = params.id_global;

	Pagination.id_container_global = '#container-global';

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
	Pagination.EventChangePage = function(id, url)
	{
		$(id).click(function(){
			event.preventDefault();

			//envoi d'une requête POST en AJAX
			$.ajax({
				method: 'POST',
				url: $(this).attr('href')
			})
			.done(function( reponse ) {

				if(reponse.statut === true)
				{
					Pagination.Ajax(Pagination.url_ajax_see, Pagination.id_global);
				}
			});
		});
	}
}