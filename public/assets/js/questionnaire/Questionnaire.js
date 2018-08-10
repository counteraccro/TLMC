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
		$(Questionnaire.id_global).showLoading();
		
		$.ajax({
			method: method,
			url: url,
		})
		.done(function( html ) {			
			$(id_done).html(html)
			$(Questionnaire.id_global).hideLoading();
		});
	}

	/**
	 * Evenement à l'édition
	 */
	Questionnaire.EventEdit = function(id)
	{
		// Event sur le bouton edit d'un questionnaire
		$(id).click(function() {
			//on passe l'url et l'id_done
						
			Questionnaire.Ajax($(this).attr('href'), Questionnaire.id_content_modal);
			return false;
		});
	}
	
	/**
	 * Evenement à la publication
	 */
	Questionnaire.EventPublication = function(id)
	{
		// Event sur le bouton publier d'un questionnaire
		$(id).click(function() {
			
			$('[data-toggle="tooltip"]').tooltip('hide');
			
			if($(this).data('publish') == 0)
			{
				$(Questionnaire.id_container_global).showLoading();
			}
			
			$.ajax({
				method: 'GET',
				url: $(this).attr('href'),
			})
			.done(function( reponse ) 
			{
				if(reponse.statut === true)
				{
					//$(Questionnaire.id_container_global).hideLoading();
					Questionnaire.Ajax(Questionnaire.url_ajax_see, Questionnaire.id_global);
				}
				else
				{
					$(Questionnaire.id_content_modal).html(reponse)
					$(Questionnaire.id_container_global).hideLoading();
				}
			});
			
			return false;
		});
	}
	
	Questionnaire.EventConfirmationPublication = function(url, id)
	{
		$(id).click(function() 
		{
			$('#ajax_questionnaire_publication').showLoading();
			$(id).prop('disabled', true).html('loading...');
			event.preventDefault();

			$.ajax({
				method: 'GET',
				url: url,
			})
			.done(function( reponse ) {
	
				$('#ajax_questionnaire_publication').hideLoading();
				
				if(reponse.statut === true)
				{
					$(Questionnaire.id_modal).modal('hide');
					Questionnaire.Ajax(Questionnaire.url_ajax_see, Questionnaire.id_global);
				}
				else
				{
					$(ajax_questionnaire_publication).html(reponse);
				}
			});
		});
	}
	
	/**
	 *  Event au changement du titre d'un questionnaire
	 *  Slugification du titre
	 */
	Questionnaire.EventOnTitle = function(id_title, id_slug, id_slug_text)
	{
		$(id_title).change(function() {
			Questionnaire.Slug(id_title, id_slug, id_slug_text)
		});
	}
	
	/**
	 * Génération du slug (url au format exemple : "questionnaire-de-satisfaction-soiree-evenement")
	 */
	Questionnaire.Slug = function(id_title, id_slug, id_slug_text)
	{
		//Récupération de la valeur saisie pour le slug	
		var textToSlug = $(id_title).val();

		//Gestion des accents (exporter dans AppController ?)
		String.prototype.sansAccent = function(){
		    var accent = [
		        /[\300-\306]/g, /[\340-\346]/g, // A, a
		        /[\310-\313]/g, /[\350-\353]/g, // E, e
		        /[\314-\317]/g, /[\354-\357]/g, // I, i
		        /[\322-\330]/g, /[\362-\370]/g, // O, o
		        /[\331-\334]/g, /[\371-\374]/g, // U, u
		        /[\321]/g, /[\361]/g, // N, n
		        /[\307]/g, /[\347]/g, // C, c
		    ];
		    var noaccent = ['A','a','E','e','I','i','O','o','U','u','N','n','C','c'];
		     
		    var str = this;
		    for(var i = 0; i < accent.length; i++){
		        str = str.replace(accent[i], noaccent[i]);
		    }     
		    return str;
		}
		
		//Passage en format 'slug' : Gestion des accents, minuscules, espaces, caractères spéciaux 
		var sluggedText = textToSlug.sansAccent().toLowerCase().replace(/ +/g,'-').replace(/[.'^$"_&@%#;\,!:²><()\[\]\/\{\}\*\+\=]/g, '');
		
		//Renvoi des élements en slug dans les champs
		$(id_slug).val(sluggedText);
		$(id_slug_text).html('<div class="d-inline p-2 bg-dark text-white">URL public du questionnaire : http://[URL-A-MODIFIER]/' + sluggedText + '</div>');
				
		return false;
	}

	/**
	 * Fonction intervenant au moment de la soumission du formulaire Ajax d'édition
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