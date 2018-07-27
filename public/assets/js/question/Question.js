//objet JS pour la question
Question = {};

Question.Launch = function(params) {

	//url de la fiche question
	Question.url_ajax_see = params.url_ajax_see;
	//cible la div '#bloc_question'
	Question.id_global = params.id_global;
	//cible la div '#bloc_modal'
	Question.id_modal = params.id_modal;
	
	Questionnaire.id_container_global = '#container-global';
	
	Question.id_content_modal = params.id_content_modal;

	/**
	 * fonction édition de la question, paramètres url et id_question
	 */
	Question.EditQuestion = function(url, id)
	{
		Question.Ajax(url, id_done);
	}

	/**
	 * Méthode Ajax qui va charger l'element présent dans l'URL
	 */
	Question.Ajax = function(url, id_done, method = 'GET')
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

	//	 ------- Vue question/ajax_see.html.twig ----------//

	/**
	 * Evenement global
	 */
	Question.EventEdit = function(id)
	{
		// Event sur le bouton edit d'une question
		$(id).click(function() {
			//on passe l'url et l'id_done		
			$(Questionnaire.id_container_global).showLoading();
			Question.Ajax($(this).attr('href'), Question.id_content_modal);
			return false;
		});;
	}


	/**
	 * 
	 */
	Question.EventEditSubmit = function(url)
	{
		$("form[name*='question']").on( "submit", function( event ) {
			
			$('#question_save').prop('disabled', true).html('loading...');
			
			event.preventDefault();

			$.ajax({
				method: 'POST',
				url: url,
				data: $(this).serialize()
			})
			.done(function( reponse ) {
	
				if(reponse.statut === true)
				{
					$(Question.id_modal).modal('hide');
					Question.Ajax(Question.url_ajax_see, Question.id_global);
				}
				else
				{
					$(Question.id_content_modal).html(reponse);
				}
			});
		});
	}
	
	Question.Preview = function(params)
	{
		
	}
}