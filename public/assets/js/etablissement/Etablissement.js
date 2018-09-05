Etablissement = {};

Etablissement.Launch = function(params){

	//url de la fiche établissement
	Etablissement.url_ajax_see = params.url_ajax_see;
	//cible la div où est affiché les infos de l'établissement
	Etablissement.id_global = params.id_global;
	//cible la div '#bloc_modal'
	Etablissement.id_modal = params.id_modal;
	//cible la div avec le contenu de la modale
	Etablissement.id_content_modal = params.id_content_modal;
	
	Etablissement.id_container_global = '#container-global';

	/**
	 * fonction prévue pour le chargement des informations de l'etablissement, paramètres url et id
	 */
	Etablissement.LoadEtablissement = function()
	{
		Etablissement.Ajax(Etablissement.url_ajax_see, Etablissement.id_global);
	}
	
	/**
	 * Méthode Ajax qui va charger l'element présent dans l'URL
	 */
	Etablissement.Ajax = function(url, id_done, method = 'GET')
	{		
		$.ajax({
			method: method,
			url: url,
		})
		.done(function( html ) {
			$(id_done).html(html)
			$(Etablissement.id_container_global).hideLoading();
		});
		
	}

	/**
	 * Action effectuée lorsque le bouton id est utilisé
	 */
	Etablissement.EventEdit = function(id)
	{
		// Event sur le bouton edit d'un Etablissement
		$(id).click(function() {
			//on passe l'url et l'id_done
			
			$(Etablissement.id_container_global).showLoading();
			Etablissement.Ajax($(this).attr('href'), Etablissement.id_content_modal);
			return false;
		});
	}
	
	/**
	 * Fonction intervenant au moment de la soumission du formulaire Ajax d'édition
	 */
	Etablissement.EventEditSubmit = function(url)
	{
		$("form[name*='etablissement']").on( "submit", function( event ) {
			
			$('#ajax_etablissement_edit').showLoading();
			
			$('#etablissement_save').prop('disabled', true).html('Chargement...');
			
			event.preventDefault();

			$.ajax({
				method: 'POST',
				url: url,
				data: $(this).serialize()
			})
			.done(function( reponse ) {
	
				$('#ajax_etablissement_edit').hideLoading();
				
				if(reponse.statut === true)
				{
					$(Etablissement.id_modal).modal('hide');
					Etablissement.Ajax(Etablissement.url_ajax_see, Etablissement.id_global);
				}
				else
				{
					$(Etablissement.id_content_modal).html(reponse);
				}
			});
		});
	}
	
	/**
	 * désactivation d'un établissement au click
	 */
	Etablissement.EventDelete = function(id, url)
	{
		$(id).click(function(){
			$(Etablissement.id_global).showLoading();
			event.preventDefault();

			//envoi d'une requête POST en AJAX
			$.ajax({
				method: 'POST',
				url: $(this).attr('href')
			})
			.done(function( reponse ) {

				if(reponse.statut === true)
				{
					$(Etablissement.id_global).hideLoading();
					Etablissement.Ajax(Etablissement.url_ajax_see, Etablissement.id_global);
				}
			});
		});
	}
}