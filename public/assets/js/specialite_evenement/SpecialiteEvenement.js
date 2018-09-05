//objet JS pour le lien specialite_evenement
SpecialiteEvenement = {};

SpecialiteEvenement.Launch = function(params) {

	//url de la fiche événement ou spécialité
	SpecialiteEvenement.url_ajax_see = params.url_ajax_see;
	//cible la div '#bloc_evenement_specialite' ou '#bloc_specialite_evenement'
	SpecialiteEvenement.id_global = params.id_global;
	//cible la div '#bloc_evenement_global'
	SpecialiteEvenement.id_modal = params.id_modal;
	//cible la div '#bloc_modal'
	SpecialiteEvenement.id_content_modal = params.id_content_modal;

	SpecialiteEvenement.id_container_global = '#container-global';

	/**
	 * fonction prévue pour le chargement des spécialités associées à l'événement, paramètres url et id
	 */
	SpecialiteEvenement.LoadSpecialiteEvenement = function()
	{
		SpecialiteEvenement.Ajax(SpecialiteEvenement.url_ajax_see, SpecialiteEvenement.id_global);
	}

	/**
	 * Méthode Ajax qui va charger l'element présent dans l'URL
	 */
	SpecialiteEvenement.Ajax = function(url, id_done, method = 'GET')
	{	
		$.ajax({
			method: method,
			url: url,
		})
		.done(function( html ) {
			$(id_done).html(html)
			$(SpecialiteEvenement.id_container_global).hideLoading();
		});
	}

	/**
	 * Evenement ajout d'un lien spécialité - événement
	 */
	SpecialiteEvenement.EventAdd = function(id)
	{
		// Event sur le bouton add d'un lien spécialité - événement
		$(id).click(function() {
			//on passe l'url et l'id_done

			$(SpecialiteEvenement.id_container_global).showLoading();

			SpecialiteEvenement.Ajax($(this).attr('href'), SpecialiteEvenement.id_content_modal);
			return false;
		});
	}

	/**
	 * traitement du formulaire d'ajout d'un lien spécialité - événement
	 */
	SpecialiteEvenement.EventAddSubmit = function(url)
	{
		$("form[name*='specialite_evenement']").on( "submit", function( event ) {

			$('#ajax_specialite_evenement_add').showLoading();

			$('#specialite_evenement_save').prop('disabled', true).html('Chargement...');

			event.preventDefault();

			//envoi d'une requête POST en AJAX
			$.ajax({
				method: 'POST',
				url: url,
				data: $(this).serialize()
			})
			.done(function( reponse ) {

				$('#ajax_specialite_evenement_add').hideLoading();

				if(reponse.statut === true)
				{
					//on cache la modale si le formulaire est valide
					$(SpecialiteEvenement.id_modal).modal('hide');
					SpecialiteEvenement.Ajax(SpecialiteEvenement.url_ajax_see, SpecialiteEvenement.id_global);
				}
				else
				{
					//on revient sur le formulaire s'il est incorrect
					$(SpecialiteEvenement.id_content_modal).html(reponse);
				}
			});
		});
	}

	/**
	 * Evenement édition d'un lien spécialité - événement
	 */
	SpecialiteEvenement.EventEdit = function(id)
	{
		// Event sur le bouton edit d'un lien spécialité - événement
		$(id).click(function() {
			//on passe l'url et l'id_done

			$(SpecialiteEvenement.id_container_global).showLoading();

			SpecialiteEvenement.Ajax($(this).attr('href'), SpecialiteEvenement.id_content_modal);
			return false;
		});
	}

	/**
	 * traitement du formulaire d'édition d'un lien spécialité - événement
	 */
	SpecialiteEvenement.EventEditSubmit = function(url)
	{
		$("form[name*='specialite_evenement']").on( "submit", function( event ) {
			
			$('#ajax_specialite_evenement_edit').showLoading();

			$('#specialite_evenement_save').prop('disabled', true).html('Chargement...');

			event.preventDefault();

			//envoi d'une requête POST en AJAX
			$.ajax({
				method: 'POST',
				url: url,
				data: $(this).serialize()
			})
			.done(function( reponse ) {

				$('#ajax_specialite_evenement_edit').hideLoading();

				if(reponse.statut === true)
				{
					//on cache la modale si le formulaire est valide
					$(SpecialiteEvenement.id_modal).modal('hide');
					SpecialiteEvenement.Ajax(SpecialiteEvenement.url_ajax_see + '/' + reponse.page, SpecialiteEvenement.id_global);
				}
				else
				{
					//on revient sur le formulaire s'il est incorrect
					$(SpecialiteEvenement.id_content_modal).html(reponse);
				}
			});
		});
	}
}