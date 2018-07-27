//objet JS pour le questionnaire
Questionnaire = {};

Questionnaire.Launch = function(params) {

	//url de la fiche questionnaire
	Questionnaire.url_ajax_see = params.url_ajax_see;
	//cible la div '#bloc_questionnaire'
	Questionnaire.id_global = params.id_global;
	//cible la div '#bloc_modal'
	Questionnaire.id_modal = params.id_modal;
	
	Questionnaire.id_content_modal = params.id_content_modal;
	
	Questionnaire.id_container_global = '#container-global';

	/**
	 * fonction édition du questionnaire, paramètres url et id_questionnaire
	 */
	Questionnaire.EditQuestionnaire = function(url, id)
	{
		Questionnaire.Ajax(url, id_done);
	}

	/**
	 * fonction prévue pour le chargement du questionnaire, paramètres url et id
	 */
	Questionnaire.LoadQuestionnaire = function()
	{
		Questionnaire.Ajax(Questionnaire.url_ajax_see, Questionnaire.id_global);
	}

	/**
	 * Méthode Ajax qui va charger l'element présent dans l'URL
	 */
	Questionnaire.Ajax = function(url, id_done, method = 'GET')
	{		
		$.ajax({
			method: method,
			url: url,
		})
		.done(function( html ) {
			$(id_done).html(html)
			$(Questionnaire.id_container_global).hideLoading();
		});
	}

	//	 ------- Vue questionnaire/ajax_see.html.twig ----------//

	/**
	 * Evenement global
	 */
	Questionnaire.EventEdit = function(id)
	{
		// Event sur le bouton edit d'un questionnaire
		$(id).click(function() {
			//on passe l'url et l'id_done
			
			$(Questionnaire.id_container_global).showLoading();
			
			Questionnaire.Ajax($(this).attr('href'), Questionnaire.id_content_modal);
			return false;
		});;
	}


	/**
	 * 
	 */
	Questionnaire.EventEditSubmit = function(url)
	{
		$("form[name*='questionnaire']").on( "submit", function( event ) {
			
			$('#ajax_questionnaire_add').showLoading();
			
			$('#questionnaire_save').prop('disabled', true).html('loading...');
			
			event.preventDefault();

			$.ajax({
				method: 'POST',
				url: url,
				data: $(this).serialize()
			})
			.done(function( reponse ) {
	
				$('#ajax_questionnaire_add').hideLoading();
				
				if(reponse.statut === true)
				{
					$(Questionnaire.id_modal).modal('hide');
					Questionnaire.Ajax(Questionnaire.url_ajax_see, Questionnaire.id_global);
				}
				else
				{
					$(Questionnaire.id_content_modal).html(reponse);
				}
			});
		});
	}
}