//objet JS pour le patient
Patient = {};

Patient.Launch = function(params) {

	//url de la fiche patient
	Patient.url_ajax_see = params.url_ajax_see;
	//cible la div '#bloc_patient'
	Patient.id_global = params.id_global;
	//cible la div '#bloc_modal'
	Patient.id_modal = params.id_modal;

	Patient.id_content_modal = params.id_content_modal;

	/**
	 * fonction édition de la famille, paramètres url et id_patient
	 */
	Patient.AddFamille = function(url, id)
	{
		Patient.Ajax(url, id_done);
	}

	/**
	 * fonction prévue pour le chargement des familles du patient, paramètres url et id
	 */
	Patient.LoadFamille = function()
	{
		Patient.Ajax(Patient.url_ajax_see, Patient.id_global);
	}

	/**
	 * Méthode Ajax qui va charger l'element présent dans l'URL
	 */
	Patient.Ajax = function(url, id_done, method = 'GET')
	{		
		$.ajax({
			method: method,
			url: url,
		})
		.done(function( html ) {
			$(id_done).html(html)
		});
	}

	/**
	 * Evenement ajout
	 */
	Patient.EventAdd = function(id)
	{
		// Event sur le bouton add d'une famille
		$(id).click(function() {
			//on passe l'url et l'id_done

			console.log(Patient.id_content_modal);

			Patient.Ajax($(this).attr('href'), Patient.id_content_modal);
			return false;
		});;
	}


	/**
	 * traitement du formulaire
	 */
	Patient.EventAddSubmit = function(url, patientJson)
	{
		$("form[name*='famille']").on( "submit", function( event ) {

			$('#famille_save').prop('disabled', true).html('loading...');

			event.preventDefault();
			
			//envoi d'une requête POST en AJAX
			$.ajax({
				method: 'POST',
				url: url,
				data: $(this).serialize()
			})
			.done(function( reponse ) {

				if(reponse.statut === true)
				{
					//on cache la modale si le formulaire est valide
					$(Patient.id_modal).modal('hide');
					Patient.Ajax(Patient.url_ajax_see, Patient.id_global);
				}
				else
				{
					//on revient sur le formulaire s'il est incorrect
					$(Patient.id_content_modal).html(reponse);
				}
			});
		});

		// action effectuée lors de la sélection d'une adresse
		$('#ajax_famille_add #famille_famille_adresse').change(function() {
			
			var famille_add = '#ajax_famille_add';
			
			event.preventDefault();
			// cache le formulaire de famille adresse
			$(famille_add + ' #adresse').hide(500);

			var id = $(this).val();
			
			if(id == 0)
			{
				//
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

		});

	}
}