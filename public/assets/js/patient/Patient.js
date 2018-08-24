Patient = {};

Patient.Launch = function(params){

	//url de la fiche questionnaire
	Patient.url_ajax_see = params.url_ajax_see;
	//cible la div '#bloc_questionnaire'
	Patient.id_global = params.id_global;
	//cible la div '#bloc_modal'
	Patient.id_modal = params.id_modal;
	
	Patient.id_content_modal = params.id_content_modal;
	
	Patient.id_container_global = '#container-global';

	/**
	 * fonction prévue pour le chargement des familles du patient, paramètres url et id
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
			$(Patient.id_container_global).hideLoading();
		});
		
	}

	/**
	 * Evenement global
	 */
	Patient.EventEdit = function(id)
	{
		// Event sur le bouton edit d'un Patient
		$(id).click(function() {
			//on passe l'url et l'id_done
			
			$(Patient.id_container_global).showLoading();
			Patient.Ajax($(this).attr('href'), Patient.id_content_modal);
			return false;
		});
	}
	
	/**
	 * Fonction intervenant au moment de la soumission du formulaire Ajax d'édition
	 */
	Patient.EventEditSubmit = function(url)
	{
		$("form[name*='patient']").on( "submit", function( event ) {
			
			$('#ajax_patient_edit').showLoading();
			
			$('#patient_save').prop('disabled', true).html('loading...');
			
			event.preventDefault();

			$.ajax({
				method: 'POST',
				url: url,
				data: $(this).serialize()
			})
			.done(function( reponse ) {
	
				$('#ajax_patient_edit').hideLoading();
				
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
	
	/**
	 * désactivation d'un patient au click
	 */
	Patient.EventDelete = function(id, url)
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
					Patient.Ajax(Patient.url_ajax_see, Patient.id_global);
				}
			});
		});
	}
}