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
	 * fonction prévue pour le chargement du patient, paramètres url et id
	 */
	Patient.LoadPatient = function()
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

	//	 ------- Vue patient/ajax_see.html.twig ----------//

	/**
	 * Evenement global
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
	 * 
	 */
	Patient.EventAddSubmit = function(url)
	{
		$("form[name*='famille']").on( "submit", function( event ) {
			
			$('#famille_save').prop('disabled', true).html('loading...');
			
			event.preventDefault();

			$.ajax({
				method: 'POST',
				url: url,
				data: $(this).serialize()
			})
			.done(function( reponse ) {
	
				if(reponse.statut === true)
				{
					$(Patient.id_modal).modal('hide');
					Patient.Ajax(Patient.url_ajax_see, Patient.id_global);
				}
				else
				{
					$(Patient.id_content_modal).html(reponse);
				}
			});
		});
	}
}