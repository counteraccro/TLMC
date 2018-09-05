Membre = {};

Membre.Launch = function(params){

	//url de la fiche membre
	Membre.url_ajax_see = params.url_ajax_see;
	//cible la div '#bloc_membre'
	Membre.id_global = params.id_global;
	//cible la div '#bloc_modal'
	Membre.id_modal = params.id_modal;
	
	Membre.id_content_modal = params.id_content_modal;
	
	Membre.id_container_global = '#container-global';

	/**
	 * fonction prévue pour le chargement des infos du membre, paramètres url et id
	 */
	Membre.LoadMembre = function()
	{
		Membre.Ajax(Membre.url_ajax_see, Membre.id_global);
	}
	
	/**
	 * Méthode Ajax qui va charger l'element présent dans l'URL
	 */
	Membre.Ajax = function(url, id_done, method = 'GET')
	{		
		$.ajax({
			method: method,
			url: url,
		})
		.done(function( html ) {
			$(id_done).html(html)
			$(Membre.id_container_global).hideLoading();
		});
		
	}

	/**
	 * Action effectuée lorsque le bouton id est utilisé
	 */
	Membre.EventEdit = function(id)
	{
		// Event sur le bouton edit d'un Membre
		$(id).click(function() {
			//on passe l'url et l'id_done
			
			$(Membre.id_container_global).showLoading();
			Membre.Ajax($(this).attr('href'), Membre.id_content_modal);
			return false;
		});
	}
	
	/**
	 * Fonction intervenant au moment de la soumission du formulaire Ajax d'édition
	 */
	Membre.EventEditSubmit = function(url)
	{
		$("form[name*='membre']").on( "submit", function( event ) {
			
			$('#ajax_membre_edit').showLoading();
			
			$('#membre_save').prop('disabled', true).html('Chargement...');
			
			event.preventDefault();

			$.ajax({
				method: 'POST',
				url: url,
				data: $(this).serialize()
			})
			.done(function( reponse ) {
	
				$('#ajax_membre_edit').hideLoading();
				
				if(reponse.statut === true)
				{
					$(Membre.id_modal).modal('hide');
					Membre.Ajax(Membre.url_ajax_see, Membre.id_global);
				}
				else
				{
					$(Membre.id_content_modal).html(reponse);
				}
			});
		});
	}
	
	/**
	 * désactivation d'un membre au click ou suppression de l'avatar
	 */
	Membre.EventDelete = function(id, url)
	{
		$(id).click(function(){
			$(Membre.id_global).showLoading();
			event.preventDefault();

			//envoi d'une requête POST en AJAX
			$.ajax({
				method: 'POST',
				url: $(this).attr('href')
			})
			.done(function( reponse ) {

				if(reponse.statut === true)
				{
					$(Membre.id_global).hideLoading();
					Membre.Ajax(Membre.url_ajax_see, Membre.id_global);
				}
			});
		});
	}
}