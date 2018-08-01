//objet JS pour la Specialite
Specialite = {};

Specialite.Launch = function(params) {

	//url pour charger les spécialité
	Specialite.url_ajax_see = params.url_ajax_see;
	//cible la div '#bloc_specialite'
	Specialite.id_specialite = params.id_specialite;
	//récupère l'id du bloc établissemnt
	Specialite.id_bloc_etablissement = params.id_bloc_etablissement;
	
	//cible la div '#bloc_patient'
	Specialite.id_global = params.id_global;
	//cible la div '#bloc_modal'
	Specialite.id_modal = params.id_modal;

	Specialite.id_content_modal = params.id_content_modal;

	Specialite.id_container_global = '#container-global';

	/**
	 * fonction prévue pour le chargement d'un dropdown des spécialités d'un établissement
	 */
	Specialite.LoadDropdownSpecialite = function()
	{
		Specialite.Ajax(Specialite.url_ajax_see + '/' + $(Specialite.id_bloc_etablissement).val(), Specialite.id_specialite);
	}
	
	/**
	 * Méthode Ajax qui va charger l'element présent dans l'URL
	 */
	Specialite.Ajax = function(url, id_done, method = 'GET')
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
	 * 
	 */
	Specialite.EventChange = function()
	{
		$(Specialite.id_bloc_etablissement).change(function(){
			event.preventDefault();
			
			Specialite.LoadDropdownSpecialite();
			
		});
	}
	
	/**
	 * fonction prévue pour le chargement des spécialités de l'établissement, paramètres url et id
	 */
	Specialite.LoadSpecialite = function()
	{
		Specialite.Ajax(Specialite.url_ajax_see, Specialite.id_global);
	}
}