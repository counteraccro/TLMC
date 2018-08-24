Evenement = {};

Evenement.Launch = function(params){

	//url de la fiche questionnaire
	Evenement.url_ajax_see = params.url_ajax_see;
	//cible la div '#bloc_questionnaire'
	Evenement.id_global = params.id_global;
	//cible la div '#bloc_modal'
	Evenement.id_modal = params.id_modal;
	
	Evenement.id_content_modal = params.id_content_modal;
	
	Evenement.id_container_global = '#container-global';

	/**
	 * fonction prévue pour le chargement des familles du evenement, paramètres url et id
	 */
	Evenement.LoadEvenement = function()
	{
		Evenement.Ajax(Evenement.url_ajax_see, Evenement.id_global);
	}
	
	/**
	 * Méthode Ajax qui va charger l'element présent dans l'URL
	 */
	Evenement.Ajax = function(url, id_done, method = 'GET')
	{		
		$.ajax({
			method: method,
			url: url,
		})
		.done(function( html ) {
			$(id_done).html(html)
			$(Evenement.id_container_global).hideLoading();
		});
		
	}

	/**
	 * Evenement global
	 */
	Evenement.EventEdit = function(id)
	{
		// Event sur le bouton edit d'un Evenement
		$(id).click(function() {
			//on passe l'url et l'id_done
			
			$(Evenement.id_container_global).showLoading();
			Evenement.Ajax($(this).attr('href'), Evenement.id_content_modal);
			return false;
		});
	}
	
	/**
	 * Fonction intervenant au moment de la soumission du formulaire Ajax d'édition
	 */
	Evenement.EventEditSubmit = function(url)
	{
		$("form[name*='evenement']").on( "submit", function( event ) {
			
			$('#ajax_evenement_edit').showLoading();
			
			$('#evenement_save').prop('disabled', true).html('loading...');
			
			event.preventDefault();

			$.ajax({
				method: 'POST',
				url: url,
				data: $(this).serialize()
			})
			.done(function( reponse ) {
	
				$('#ajax_evenement_edit').hideLoading();
				
				if(reponse.statut === true)
				{
					console.log('DAC');
					$(Evenement.id_modal).modal('hide');
					Evenement.Ajax(Evenement.url_ajax_see, Evenement.id_global);
				}
				else
				{
					$(Evenement.id_content_modal).html(reponse);
				}
			});
		});
	}
	
	/**
	 * désactivation d'un événement au click
	 */
	Evenement.EventDelete = function(id, url)
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
					Evenement.Ajax(Evenement.url_ajax_see, Evenement.id_global);
				}
			});
		});
	}
}