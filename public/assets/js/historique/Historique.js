Historique = {};

Historique.Launch = function(params){

	//url de la fiche 
	Historique.url_ajax_see = params.url_ajax_see;
	//cible la div '#bloc_membre_historique' ou '#bloc_patient_historique' ou '#bloc_specialite_historique' ou '#bloc_evenement_historique'
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
}