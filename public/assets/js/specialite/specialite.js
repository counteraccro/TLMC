//objet JS pour la Specialite
Specialite = {};

Specialite.Launch = function(params) {

	//url pour charger les spécialités
	Specialite.url_ajax_see = params.url_ajax_see;
	//cible la div '#bloc_specialite'
	Specialite.id_specialite = params.id_specialite;
	//cible la div '#bloc_etablissement'
	Specialite.id_bloc_etablissement = params.id_bloc_etablissement;
	
	//cible la div '#bloc_etablissement_specialite'
	Specialite.id_global = params.id_global;
	//cible la div '#bloc_modal'
	Specialite.id_modal = params.id_modal;

	Specialite.id_content_modal = params.id_content_modal;

	Specialite.id_container_global = '#container-global';

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
			$(Specialite.id_container_global).hideLoading();
			$(Specialite.id_specialite).hideLoading();
		});
	}
	
	/**
	 * fonction prévue pour le chargement d'un dropdown des spécialités d'un établissement
	 */
	Specialite.LoadDropdownSpecialite = function()
	{
		Specialite.Ajax(Specialite.url_ajax_see + '/' + $(Specialite.id_bloc_etablissement).val(), Specialite.id_specialite);
	}
	
	/**
	 * Evénement changement du select établissement
	 */
	Specialite.EventChange = function(id)
	{
		$(Specialite.id_bloc_etablissement).change(function(){
			event.preventDefault();
			//désactivation du bouton de sauvegarde pour éviter les erreurs
			$(id).prop('disabled', true);
			//affichage du loader
			$(Specialite.id_specialite).showLoading();
			//chargement du dropdown
			Specialite.LoadDropdownSpecialite();
			
		});
	}
	
	/**
	 * Fonction prévue pour le chargement des spécialités de l'établissement, paramètres url et id
	 */
	Specialite.LoadSpecialite = function()
	{
		Specialite.Ajax(Specialite.url_ajax_see, Specialite.id_global);
	}
	
	/**
	 * Evénement ajout d'une spécialité
	 */
	Specialite.EventAdd = function(id)
	{
		// Event sur le bouton add d'une famille
		$(id).click(function() {
			//on passe l'url et l'id_done

			$(Specialite.id_container_global).showLoading();

			Specialite.Ajax($(this).attr('href'), Specialite.id_content_modal);
			return false;
		});
	}
	
	/**
	 * Traitement du formulaire d'ajout d'une spécialité
	 */
	Specialite.EventAddSubmit = function(url)
	{
		$("form[name*='specialite']").on( "submit", function( event ) {

			$('#ajax_specialite_add').showLoading();

			$('#specialite_save').prop('disabled', true).html('Chargement...');

			event.preventDefault();

			//envoi d'une requête POST en AJAX
			$.ajax({
				method: 'POST',
				url: url,
				data: $(this).serialize()
			})
			.done(function( reponse ) {

				$('#ajax_specialite_add').hideLoading();

				if(reponse.statut === true)
				{
					//on cache la modale si le formulaire est valide
					$(Specialite.id_modal).modal('hide');
					Specialite.Ajax(Specialite.url_ajax_see, Specialite.id_global);
				}
				else
				{
					//on revient sur le formulaire s'il est incorrect
					$(Specialite.id_content_modal).html(reponse);
				}
			});
		});
	}
	
	/**
	 * Evénement édition d'une spécialité
	 */
	Specialite.EventEdit = function(id)
	{
		// Event sur le bouton edit d'une famille
		$(id).click(function() {
			//on passe l'url et l'id_done

			$(Specialite.id_container_global).showLoading();
			Specialite.Ajax($(this).attr('href'), Specialite.id_content_modal);
			return false;
		});
	}

	/**
	 * traitement du formulaire d'édition d'une spécialité
	 */
	Specialite.EventEditSubmit = function(url)
	{
		$("form[name*='specialite']").on( "submit", function( event ) {
			
			$('#ajax_specialite_edit').showLoading();

			$('#specialite_save').prop('disabled', true).html('Chargement...');

			event.preventDefault();

			//envoi d'une requête POST en AJAX
			$.ajax({
				method: 'POST',
				url: url,
				data: $(this).serialize()
			})
			.done(function( reponse ) {

				$('#ajax_specialite_edit').hideLoading();

				if(reponse.statut === true)
				{
					//on cache la modale si le formulaire est valide
					$(Specialite.id_modal).modal('hide');
					Specialite.Ajax(Specialite.url_ajax_see, Specialite.id_global);
				}
				else
				{
					//on revient sur le formulaire s'il est incorrect
					$(Specialite.id_content_modal).html(reponse);
				}
			});
		});
	}
	
	/**
	 * désactivation d'une spécialité au click
	 */
	Specialite.EventDelete = function(id, url)
	{
		$(id).click(function(){
			$(Specialite.id_global).showLoading();
			event.preventDefault();

			//envoi d'une requête POST en AJAX
			$.ajax({
				method: 'POST',
				url: $(this).attr('href')
			})
			.done(function( reponse ) {

				if(reponse.statut === true)
				{
					$(Specialite.id_global).hideLoading();
					Specialite.Ajax(Specialite.url_ajax_see, Specialite.id_global);
				}
			});
		});
	}
	
}