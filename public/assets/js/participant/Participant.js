Participant = {};

Participant.Launch = function(params){

	//url de la fiche 
	Participant.url_ajax_see = params.url_ajax_see;
	//cible la div '#bloc_participant_participant' ou '#bloc_patient_participant' ou '#bloc_specialite_participant' ou '#bloc_evenement_participant'
	Participant.id_global = params.id_global;
	//cible la div '#bloc_modal'
	Participant.id_modal = params.id_modal;
	
	Participant.id_content_modal = params.id_content_modal;
	
	Participant.id_container_global = '#container-global';

	/**
	 * fonction prévue pour le chargement de l'participant du membre, du patient, de l'événement ou de la spécialité, paramètres url et id
	 */
	Participant.LoadParticipant = function()
	{
		Participant.Ajax(Participant.url_ajax_see, Participant.id_global);
	}
	
	/**
	 * Méthode Ajax qui va charger l'element présent dans l'URL
	 */
	Participant.Ajax = function(url, id_done, method = 'GET')
	{		
		$.ajax({
			method: method,
			url: url,
		})
		.done(function( html ) {
			$(id_done).html(html)
			$(Participant.id_container_global).hideLoading();
			$(Participant.id_global).hideLoading();
		});
	}
	
	/**
	 * Evenement ajout participant
	 */
	Participant.EventAdd = function(id)
	{
		// Event sur le bouton edit d'un Participant
		$(id).click(function() {
			//on passe l'url et l'id_done
			
			$(Participant.id_container_global).showLoading();
			Participant.Ajax($(this).attr('href'), Participant.id_content_modal);
			return false;
		});
	}
	
	/**
	 * Fonction intervenant au moment de la soumission du formulaire Ajax d'édition
	 */
	Participant.EventAddSubmit = function(url)
	{
		$("form[name*='participant']").on( "submit", function( event ) {
			
			$('#ajax_participant_add').showLoading();
			
			$('#participant_save').prop('disabled', true).html('Chargement...');
			
			event.preventDefault();

			$.ajax({
				method: 'POST',
				url: url,
				data: $(this).serialize()
			})
			.done(function( reponse ) {
	
				$('#ajax_participant_add').hideLoading();
				
				if(reponse.statut === true)
				{
					$(Participant.id_modal).modal('hide');
					Participant.Ajax(Participant.url_ajax_see, Participant.id_global);
				}
				else
				{
					$(Participant.id_content_modal).html(reponse);
				}
			});
		});
	}
	
	/**
	 * Evenement édition
	 */
	Participant.EventEdit = function(id)
	{
		// Event sur le bouton edit d'un Participant
		$(id).click(function() {
			//on passe l'url et l'id_done
			
			$(Participant.id_container_global).showLoading();
			Participant.Ajax($(this).attr('href'), Participant.id_content_modal);
			return false;
		});
	}
	
	/**
	 * Fonction intervenant au moment de la soumission du formulaire Ajax d'édition
	 */
	Participant.EventEditSubmit = function(url)
	{
		$("form[name*='participant']").on( "submit", function( event ) {
			
			$('#ajax_participant_edit').showLoading();
			
			$('#participant_save').prop('disabled', true).html('Chargement...');
			
			event.preventDefault();

			$.ajax({
				method: 'POST',
				url: url,
				data: $(this).serialize()
			})
			.done(function( reponse ) {
	
				$('#ajax_participant_edit').hideLoading();
				
				if(reponse.statut === true)
				{
					$(Participant.id_modal).modal('hide');
					Participant.Ajax(Participant.url_ajax_see, Participant.id_global);
				}
				else
				{
					$(Participant.id_content_modal).html(reponse);
				}
			});
		});
	}
}