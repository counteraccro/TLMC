//objet JS pour le questionnaire
QuestionnaireForm = {};

QuestionnaireForm.Launch = function(params) {

	//url de la fiche questionnaire
	QuestionnaireForm.url_ajax_see = params.url_ajax_see;
	//cible la div '#bloc_questionnaire'
	QuestionnaireForm.id_global = params.id_global;
	//cible la div '#bloc_modal'
	QuestionnaireForm.id_modal = params.id_modal;
	
	QuestionnaireForm.id_content_modal = params.id_content_modal;

	/**
	 * fonction édition du questionnaire, paramètres url et id_questionnaire
	 */
	QuestionnaireForm.EditQuestionnaire = function(url, id)
	{
		QuestionnaireForm.Ajax(url, id_done);
	}

	/**
	 * fonction prévue pour le chargement du questionnaire, paramètres url et id
	 */
	QuestionnaireForm.LoadQuestionnaire = function()
	{
		QuestionnaireForm.Ajax(QuestionnaireForm.url_ajax_see, QuestionnaireForm.id_global);
	}

	/**
	 * Méthode Ajax qui va charger l'element présent dans l'URL
	 */
	QuestionnaireForm.Ajax = function(url, id_done, method = 'GET')
	{		
		$.ajax({
			method: method,
			url: url,
		})
		.done(function( html ) {
			$(id_done).html(html)
		});
	}

	//	 ------- Vue questionnaire/ajax_see.html.twig ----------//

	/**
	 * Evenement global
	 */
	QuestionnaireForm.EventEdit = function(id)
	{
		// Event sur le bouton edit d'un questionnaire
		$(id).click(function() {
			//on passe l'url et l'id_done
			
			console.log(QuestionnaireForm.id_content_modal);
			
			QuestionnaireForm.Ajax($(this).attr('href'), QuestionnaireForm.id_content_modal);
			return false;
		});;
	}


	/**
	 * 
	 */
	QuestionnaireForm.EventEditSubmit = function(url)
	{
		$("form[name*='questionnaire']").on( "submit", function( event ) {
			
			$('#questionnaire_save').prop('disabled', true).html('loading...');
			
			event.preventDefault();

			$.ajax({
				method: 'POST',
				url: url,
				data: $(this).serialize()
			})
			.done(function( reponse ) {
	
				if(reponse.statut === true)
				{
					$(QuestionnaireForm.id_modal).modal('hide');
					QuestionnaireForm.Ajax(QuestionnaireForm.url_ajax_see, QuestionnaireForm.id_global);
				}
				else
				{
					$(QuestionnaireForm.id_content_modal).html(reponse);
				}
			});
		});
	}
}