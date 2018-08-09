Historique = {};

Historique.Launch = function(params){

	//url de la fiche 
	Historique.url_ajax_see = params.url_ajax_see;
	//cible la div '#bloc_historique_historique' ou '#bloc_patient_historique' ou '#bloc_specialite_historique' ou '#bloc_evenement_historique'
	Historique.id_global = params.id_global;
	//cible la div '#bloc_modal'
	Historique.id_modal = params.id_modal;
	
	Historique.id_content_modal = params.id_content_modal;
	
	Historique.id_container_global = '#container-global';

	/**
	 * fonction prévue pour le chargement de l'historique du membre, du patient, de l'événement ou de la spécialité, paramètres url et id
	 */
	Historique.LoadHistorique = function()
	{
		Historique.Ajax(Historique.url_ajax_see, Historique.id_global);
	}
	
	/**
	 * Méthode Ajax qui va charger l'element présent dans l'URL
	 */
	Historique.Ajax = function(url, id_done, method = 'GET')
	{		
		$.ajax({
			method: method,
			url: url,
		})
		.done(function( html ) {
			$(id_done).html(html)
			$(Historique.id_container_global).hideLoading();
		});
	}
	
	/**
	 * Evenement global
	 */
	Historique.EventEdit = function(id)
	{
		// Event sur le bouton edit d'un Historique
		$(id).click(function() {
			//on passe l'url et l'id_done
			
			$(Historique.id_container_global).showLoading();
			Historique.Ajax($(this).attr('href'), Historique.id_content_modal);
			return false;
		});
	}
	
	/**
	 * Fonction intervenant au moment de la soumission du formulaire Ajax d'édition
	 */
	Historique.EventEditSubmit = function(url)
	{
		$("form[name*='historique']").on( "submit", function( event ) {
			
			$('#ajax_historique_edit').showLoading();
			
			$('#historique_save').prop('disabled', true).html('loading...');
			
			event.preventDefault();

			$.ajax({
				method: 'POST',
				url: url,
				data: $(this).serialize()
			})
			.done(function( reponse ) {
	
				$('#ajax_historique_edit').hideLoading();
				
				if(reponse.statut === true)
				{
					$(Historique.id_modal).modal('hide');
					Historique.Ajax(Historique.url_ajax_see, Historique.id_global);
				}
				else
				{
					$(Historique.id_content_modal).html(reponse);
				}
			});
		});
	}
}