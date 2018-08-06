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
		});
	}
	
	/**
	 *  Event au changement du titre d'un questionnaire
	 *  Slugification du titre
	 */
	Questionnaire.EventOnTitle = function(id_title, id_slug, id_slug_text)
	{
		$(id_title).change(function() {
			//Récupération de la valeur saisie pour le slug	
			var textToSlug = $(this).val();

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
			
			//A SUPPRIMER
			//var textToSlug = "Sa mère prend de l'$Héroïne% et prépare des Crêpes.";
			
			//Passage en format 'slug' : Gestion des accents, minuscules, espaces, caractères spéciaux 
			var sluggedText = textToSlug.sansAccent().toLowerCase().replace(/ +/g,'-').replace(/[.'$"&%#8217;]/g, '');
			
			//Renvoi des élements en slug dans les champs
			$(id_slug).val(sluggedText);
			$(id_slug_text).html(sluggedText);
			
			
			return false;
		});
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