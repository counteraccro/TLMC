//objet JS pour la famille
Famille = {};

Famille.Launch = function(params) {

	//url de la fiche patient
	Famille.url_ajax_see = params.url_ajax_see;
	//cible la div '#bloc_patient_famille'
	Famille.id_global = params.id_global;
	//cible la div '#bloc_modal'
	Famille.id_modal = params.id_modal;

	Famille.id_content_modal = params.id_content_modal;

	Famille.id_container_global = '#container-global';

	/**
	 * fonction prévue pour le chargement des familles du patient, paramètres url et id
	 */
	Famille.LoadFamille = function()
	{
		Famille.Ajax(Famille.url_ajax_see, Famille.id_global);
	}

	/**
	 * Méthode Ajax qui va charger l'element présent dans l'URL
	 */
	Famille.Ajax = function(url, id_done, method = 'GET')
	{	
		$.ajax({
			method: method,
			url: url,
		})
		.done(function( html ) {
			$(id_done).html(html)
			$(Famille.id_container_global).hideLoading();
		});
	}

	/**
	 * Evenement ajout d'une famille
	 */
	Famille.EventAdd = function(id)
	{
		// Event sur le bouton add d'une famille
		$(id).click(function() {
			//on passe l'url et l'id_done

			$(Famille.id_container_global).showLoading();

			Famille.Ajax($(this).attr('href'), Famille.id_content_modal);
			return false;
		});
	}

	/**
	 * traitement du formulaire d'ajout d'une famille
	 */
	Famille.EventAddSubmit = function(url, patientJson)
	{
		$("form[name*='famille']").on( "submit", function( event ) {

			$('#ajax_famille_add').showLoading();

			$('#famille_save').prop('disabled', true).html('loading...');

			event.preventDefault();

			//envoi d'une requête POST en AJAX
			$.ajax({
				method: 'POST',
				url: url,
				data: $(this).serialize()
			})
			.done(function( reponse ) {

				$('#ajax_famille_add').hideLoading();

				if(reponse.statut === true)
				{
					//on cache la modale si le formulaire est valide
					$(Famille.id_modal).modal('hide');
					Famille.Ajax(Famille.url_ajax_see, Famille.id_global);
				}
				else
				{
					//on revient sur le formulaire s'il est incorrect
					$(Famille.id_content_modal).html(reponse);
				}
			});
		});

		$('#ajax_famille_add #famille_famille_adresse_select').change(function() {
				Famille.LoadAdresseSelect($(this), patientJson)
		});
	}

	/**
	 * Mise à jour des div adresse et info-adresse-patient à partir d'un select
	 */
	Famille.LoadAdresseSelect = function(form_element, patientJson)
	{
		var famille_add = '#ajax_famille_add';

		event.preventDefault();
		// cache le formulaire de famille adresse
		$(famille_add + ' #adresse').hide(500);
		
		var id = form_element.val();
		

		if(id == 0)
		{
			$(famille_add + ' #info-adresse-patient').html('');
			// afficher le formulaire de famille adresse
			$(famille_add + ' #adresse').show(500);

			//on initialise le formulaire d'adresse à vide
			$(famille_add + ' #famille_famille_adresse_numero_voie').val('');
			$(famille_add + ' #famille_famille_adresse_voie').val('');
			$(famille_add + ' #famille_famille_adresse_ville').val('');
			$(famille_add + ' #famille_famille_adresse_code_postal').val('');

			return true;
		}

		var i;
		for (i = 0; i < patientJson.familles.length; i++) 
		{ 			
			if(patientJson.familles[i].id == id)
			{
				familleAdresse = patientJson.familles[i].familleAdresse;

				//on remplit le formulaire d'adresse avec les infos de la famille sélectionnée dans le menu déroulant
				$(famille_add + ' #famille_famille_adresse_numero_voie').val(familleAdresse.numeroVoie);
				$(famille_add + ' #famille_famille_adresse_voie').val(familleAdresse.voie);
				$(famille_add + ' #famille_famille_adresse_ville').val(familleAdresse.ville);
				$(famille_add + ' #famille_famille_adresse_code_postal').val(familleAdresse.codePostal);

				// message d'info avec l'adresse sélectionnée confirmée
				$(famille_add + ' #info-adresse-patient').html("Adresse de <b>" + patientJson.familles[i].prenom + " " + patientJson.familles[i].nom + "</b> selectionnée");
				$(famille_add + ' #info-adresse-patient').append("<br /> <span class=\"oi oi-home\"></span> <i>" + familleAdresse.numeroVoie + " " + familleAdresse.voie + " " + familleAdresse.codePostal + " " + familleAdresse.ville + "</i><br />");

			}
		}
	}

	/**
	 * Evenement édition d'une famille ou d'une famille adresse
	 */
	Famille.EventEdit = function(id)
	{
		// Event sur le bouton edit d'une famille
		$(id).click(function() {
			//on passe l'url et l'id_done

			$(Famille.id_container_global).showLoading();

			Famille.Ajax($(this).attr('href'), Famille.id_content_modal);
			return false;
		});
	}

	/**
	 * traitement du formulaire d'édition d'une famille
	 */
	Famille.EventEditSubmit = function(url)
	{
		$("form[name*='famille']").on( "submit", function( event ) {
			
			$('#ajax_famille_edit').showLoading();

			$('#famille_save').prop('disabled', true).html('loading...');

			event.preventDefault();

			//envoi d'une requête POST en AJAX
			$.ajax({
				method: 'POST',
				url: url,
				data: $(this).serialize()
			})
			.done(function( reponse ) {

				$('#ajax_famille_edit').hideLoading();

				if(reponse.statut === true)
				{
					//on cache la modale si le formulaire est valide
					$(Famille.id_modal).modal('hide');
					Famille.Ajax(Famille.url_ajax_see, Famille.id_global);
				}
				else
				{
					//on revient sur le formulaire s'il est incorrect
					$(Famille.id_content_modal).html(reponse);
				}
			});
		});
	}

	/**
	 * traitement du formulaire d'édition d'une famille adresse
	 */
	Famille.EventEditAdresseSubmit = function(url)
	{
		$("form[name*='famille']").on( "submit", function( event ) {

			$('#ajax_famille_adresse_edit').showLoading();

			$('#famille_save').prop('disabled', true).html('loading...');

			event.preventDefault();

			//envoi d'une requête POST en AJAX
			$.ajax({
				method: 'POST',
				url: url,
				data: $(this).serialize()
			})
			.done(function( reponse ) {

				$('#ajax_famille_adresse_edit').hideLoading();

				if(reponse.statut === true)
				{
					//on cache la modale si le formulaire est valide
					$(Famille.id_modal).modal('hide');
					Famille.Ajax(Famille.url_ajax_see, Famille.id_global);
				}
				else
				{
					//on revient sur le formulaire s'il est incorrect
					$(Famille.id_content_modal).html(reponse);
				}
			});
		});
	}
	
	/**
	 * désactivation d'une famille au click
	 */
	Famille.EventDelete = function(id, url)
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
					Famille.Ajax(Famille.url_ajax_see, Famille.id_global);
				}
			});
		});
	}
}