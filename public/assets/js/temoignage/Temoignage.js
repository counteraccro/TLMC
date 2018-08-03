//objet JS pour le Témoignage
Temoignage = {};

Temoignage.Launch = function(params) {
	
	//url de la fiche membre
	Temoignage.url_ajax_see = params.url_ajax_see;
	//cible la div '#bloc_membre_temoignage'
	Temoignage.id_global = params.id_global;
	//cible la div '#bloc_modal'
	Temoignage.id_modal = params.id_modal;

	Temoignage.id_content_modal = params.id_content_modal;

	Temoignage.id_container_global = '#container-global';

	/**
	 * Méthode Ajax qui va charger l'element présent dans l'URL
	 */
	Temoignage.Ajax = function(url, id_done, method = 'GET')
	{	
		$.ajax({
			method: method,
			url: url,
		})
		.done(function( html ) {
			$(id_done).html(html);
			$(Temoignage.id_container_global).hideLoading();
		});
	}
	
	/**
	 * Fonction prévue pour le chargement des spécialités de l'établissement, paramètres url et id
	 */
	Temoignage.LoadTemoignage = function()
	{
		Temoignage.Ajax(Temoignage.url_ajax_see, Temoignage.id_global);
	}
	
	/**
	 * désactivation d'un témoignage au click
	 */
	Temoignage.EventDelete = function(id, url)
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
					Temoignage.Ajax(Temoignage.url_ajax_see, Temoignage.id_global);
				}
			});
		});
	}
	
}