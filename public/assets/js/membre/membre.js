//objet JS pour le Membre
Membre = {};

Membre.Launch = function(params) {

	//url pour charger les spécialité
	Membre.url_ajax_see = params.url_ajax_see;
	//cible la div '#bloc_specialite'
	Membre.id_specialite = params.id_specialite;
	//récupère l'id du bloc établissemnt
	Membre.id_bloc_etablissement = params.id_bloc_etablissement

	/**
	 * fonction prévue pour le chargement des spécialités d'un établissement
	 */
	Membre.LoadSpecialite = function()
	{
		Membre.Ajax(Membre.url_ajax_see + '/' + $(Membre.id_bloc_etablissement).val(), Membre.id_specialite);
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
			$(id_done).html(html);
		});
	}
	
	/**
	 * 
	 */
	Membre.EventChange = function()
	{
		$(Membre.id_bloc_etablissement).change(function(){
			event.preventDefault();
			
			Membre.LoadSpecialite();
			
		});
	}
}