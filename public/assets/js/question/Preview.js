//objet JS pour l'affichage des previews
Preview = {};

Preview.Launch = function(params) {

	Preview.id_global = params.id_global;
	Preview.id_preview = params.id_global + ' ' + params.id_preview;
	Preview.liste_type_json = params.liste_type_json;
	Preview.id_contener_input_liste_val = '#contener-input-liste-val';
	Preview.json_liste_val = '';
	Preview.id_input_render = '#input-render-preview';
	Preview.id_textarea_render = '#textarea-render-preview';
	Preview.id_input_liste_valeur = '#question_liste_valeur';
	Preview.id_contener_input_def_value = '#contener-input-def-value';

	Preview.type = '';
	Preview.options_liste_valeur = '';
	Preview.def_selected_valeur = params.def_selected_valeur;
	Preview.html = '';
	Preview.input = '';
	Preview.label = '';
	Preview.label_bottom = '';
	Preview.label_top = '';
	Preview.message_erreur = '';

	Preview.tabStructureElement = {
			ChoiceType : '<select class="form-control" id="input-render-preview"><option>valeur</option></select>',
			TextareaType : '<textarea class="form-control" id="input-render-preview" placeholder="[valeur]"></textarea>',
			TextType : '<input type="text" class="form-control" id="input-render-preview" placeholder="[valeur]">',
			CheckboxType: 'contruction faite dans GestionElementListe()',
			RadioType: 'contruction faite dans GestionElementListe()'
	};

	/**
	 * Fonction permettant le chargement du preview
	 */
	Preview.Load = function()
	{
		$(Preview.id_global + " .preview, " + Preview.id_global + " .preview-hidden").each(function(){
			Preview.ConstructHtml($(this));
		});
		Preview.Render();
		Preview.AddMarqueurObligatoire($(Preview.id_global + ' #question_obligatoire'));
	}

	/**
	 * Fonction liée à l'évènement 'au changement' du type de champ souhaité
	 * Préparation du HTML, renvoi à la vue
	 */
	Preview.Event = function()
	{
		$(Preview.id_global + " .preview").change(function(){
			Preview.ConstructHtml($(this));
			Preview.Render();
		});

		// évènement à l'ajout d'une nouvelle valeur de réponse possible
		$(Preview.id_global +  ' #btn-add-input-list').click(function() {

			for (var i in Preview.json_liste_val)
			{
				// Chercher meilleur moyens de trouver la dernière clée en JSON
			}

			if (isNaN(i))
			{
				i = -1;
			}

			Preview.json_liste_val[parseInt(i) + 1] = {'value' : '', 'libelle' : ''};
			Preview.GestionElementListe();
			Preview.GestionDefaultData();
			Preview.Render();
			Preview.subRepeatEvent();
			return false;
		});

		/**
		 * Load du preview incluant le message d'erreur
		 */
		$(Preview.id_global + ' #checkbox_msg_erreur').change(function() {
		
			var _this = $(this);
				
			if($(Preview.id_global +  " " + Preview.id_input_render).tagName() != 'div')
			{
				if(_this.is(':checked'))
				{
					$(Preview.id_global +  " " + Preview.id_input_render).addClass('is-invalid');
				}
				else
				{
					$(Preview.id_global +  " " + Preview.id_input_render).removeClass('is-invalid');
				}
			}
			else 
			{
				$(Preview.id_global +  " " + Preview.id_input_render).each(function() {

					if(_this.is(':checked'))
					{
						$('#' + $(this).children()[0].id).addClass('is-invalid');
						$(Preview.id_global +  " .invalid-feedback").show();
					}
					else
					{
						$('#' + $(this).children()[0].id).removeClass('is-invalid');
						$(Preview.id_global +  " .invalid-feedback").hide();
					}
				});
			}
		});
		
		
		$(Preview.id_global + ' #question_obligatoire').change(function() {
			Preview.AddMarqueurObligatoire($(this));
		});
		
		
	}
	
	/**
	 * Ajoute une * ou non si la question est obligatoire
	 */
	Preview.AddMarqueurObligatoire = function(_this)
	{
		if(_this.is(':checked'))
		{
			$('#label-etoile').html('*');
		}
		else
	    {
			$('#label-etoile').html('');
	    }
	}

	/**
	 * Fonctions à répéter
	 */
	Preview.subRepeatEvent = function()
	{
		// évènement à la suppression d'une valeur de réponse possible
		$(Preview.id_global +  ' .btn-delete-element-list').click(function() {

			var id = $(this).data('id');
			delete Preview.json_liste_val[id];
			Preview.GestionElementListe();
			Preview.GestionDefaultData();
			Preview.Render();
			Preview.subRepeatEvent();
		});

		// permet de renvoyer les valeurs de réponses possibles dans le menu déroulant lorsque celles-ci sont modifiées
		$(Preview.id_global +  ' .input-val').change(function() {

			var id = $(this).data('id');
			var value = $("#list-val-col-" + id + " #input-value-"+ id).val();
			var libelle = $("#list-val-col-" + id + " #input-libelle-"+ id).val();
			Preview.json_liste_val[id] = {'value' : value, 'libelle' : libelle};
			Preview.GestionElementListe();
			Preview.GestionDefaultData();
			Preview.Render();
			Preview.subRepeatEvent();
		});

		$(Preview.id_global + ' #select-default-input').change(function() {

			Preview.def_selected_valeur = $(this).val();			
			Preview.GestionElementListe();
			Preview.GestionDefaultData();
			Preview.Render();
			Preview.subRepeatEvent();
		})
	}

	/**
	 * Identification de l'élément ciblé par l'utilisateur pour le preview
	 */
	Preview.ConstructHtml = function(form_element)
	{
		var id = form_element.attr('id');

		var val = form_element.val();

		switch (id) { 
		case 'question_liste_select_type':
			Preview.SelectType(form_element);
			break;
		case 'question_libelle' : 
			Preview.label = '<label for="input-render-preview">' + val + ' <span id="label-etoile" class="text-danger"></label>';
			break;
		case 'question_libelle_bottom' :
			Preview.label_bottom = '';
			if(val != "")
			{
				Preview.label_bottom = '<small class="form-text text-muted"><i>' + val + '</i></small>';
			}
			break;
		case 'question_libelle_top' : 
			Preview.label_top = '';
			if(val != "")
			{
				Preview.label_top = '<p>' + val + '</p>';
			}
			break;
		case 'question_liste_valeur':

			if(Preview.type == 'ChoiceType' || Preview.type == 'CheckboxType' || Preview.type == 'RadioType')
			{
				if(val !== '')
				{
					Preview.json_liste_val = JSON.parse(form_element.val());
				}
				else
				{
					Preview.json_liste_val = JSON.parse('{"0":{"value":"","libelle":""}}');
				}
				Preview.GestionElementListe();
			}
			break;
		case 'question_message_erreur' :
			Preview.message_erreur = '<label>' + val + '</label>';
			break;
		case 'question_valeur_defaut' :
			Preview.def_selected_valeur = val;
			Preview.GestionDefaultData();
			break;
		default:
			console.log('Element ' + id + ' introuvable');
		}
	}

	/**
	 * Renvoie le preview en fonction de l'élément ciblé par l'utilisateur
	 */
	Preview.Render = function()
	{
		Preview.html = '<div class="form-group">' + Preview.label_top + Preview.label + Preview.input + Preview.label_bottom + '<div class="invalid-feedback">' + Preview.message_erreur + '</div></div>';
		Preview.html = Preview.html.replace('[valeur]', Preview.def_selected_valeur);
		$(Preview.id_preview).html(Preview.html);
	}

	/**
	 *  Matching de l'élément ciblé par l'utilisateur avec son type
	 */
	Preview.SelectType = function(select)
	{
		for (var key in Preview.liste_type_json) {

			if(key == select.val())
			{
				if(Preview.tabStructureElement[key])
				{
					Preview.input = Preview.tabStructureElement[key];
					Preview.type = key;
				}
				else
				{
					Preview.input = '<div class="alert alert-warning" role="alert">Elément ' + key + ' non défini</div>';
					Preview.type = '';
				}
				break;
			}
		}

		if(Preview.type == 'ChoiceType' || Preview.type == 'CheckboxType' || Preview.type == 'RadioType')
		{
			$(Preview.id_global +  " " + Preview.id_input_liste_valeur).parent().hide();
			$(Preview.id_global + " #input-liste-val").show();
			Preview.ConstructHtml($(Preview.id_global +  " " + Preview.id_input_liste_valeur));

			$(Preview.id_global + ' #question_valeur_defaut').parent().hide();
			Preview.GestionDefaultData();
			Preview.subRepeatEvent();
			
			$(Preview.id_global + ' #question_regles').parent().hide();
			
			if(Preview.type == 'CheckboxType' || Preview.type == 'RadioType')
			{
				$(Preview.id_global + ' ' + Preview.id_contener_input_def_value).html('');
			}
		}
		else
		{
			$(Preview.id_global +  " " + Preview.id_input_liste_valeur).val('');
			$(Preview.id_contener_input_liste_val).html('');
			$(Preview.id_global + " #input-liste-val").hide();

			$(Preview.id_global + ' ' + Preview.id_contener_input_def_value).html('');
			$(Preview.id_global + ' #question_valeur_defaut').parent().show();
			$(Preview.id_global + ' #question_regles').parent().show();
		}
	}

	/**
	 * Conversion de la liste de valeurs envoyée par l'utilisateur en JSON
	 */
	Preview.GestionElementListe = function()
	{
		var json = Preview.json_liste_val;
		var html = '';
		var options = "";
		Preview.input = "";

		for (var i in json)
		{			
			html += '<div class="row no-gutters align-items-center" id="list-val-col-' + i + '">';
			html += '<div class="col-md-5">';
			html += '<input class="form-control liste-val-key input-val" type="text" value="' + json[i].value + '" data-id="'+ i +'" id="input-value-'+ i +'" />';
			html += '</div>';
			html += '<div class="col-md-5">';
			html += '<input type="text" class="form-control list-val-val input-val" value="' + json[i].libelle + '" data-id="'+ i +'" id="input-libelle-'+ i +'" />';
			html += '</div>';
			html += '<div class="col-md-2">';
			html += '&nbsp;&nbsp;<a href="#" data-id="' + i + '" class="btn btn-sm btn-outline-danger btn-delete-element-list"><span class="oi oi-x"></span></a>';
			html += '</div>';
			html += '</div>';

			var selected = '';
			if(Preview.def_selected_valeur == json[i].value)
			{
				selected = 'selected';
			}

			if (Preview.type == 'ChoiceType') {
				options += '<option value="' + json[i].value + '" ' + selected +'>' + json[i].libelle + '</option>';
			}
			else if(Preview.type == 'CheckboxType')
			{
				Preview.input += '<div class="form-check" id="input-render-preview"><input class="form-check-input" type="checkbox" value="' + json[i].value + '" id="check-' + i + '"><label class="form-check-label" for="check-' + i + '">' + json[i].libelle + '</label></div>';
			}
			else if(Preview.type == 'RadioType')
			{
				Preview.input += '<div class="form-check" id="input-render-preview"><input class="form-check-input" type="radio" name="radio-choice" value="' + json[i].value + '" id="radio-' + i + ' checked"><label class="form-check-label" for="radio-' + i + '">' + json[i].libelle + '</label></div>';
			}
	             
			Preview.options_liste_valeur = options;
		}
		$(Preview.id_contener_input_liste_val).html(html);

		valJson = JSON.stringify(json);
		if(valJson == '{}')
		{
			valJson = '';
		}

		$(Preview.id_global + ' #question_liste_valeur').val(valJson);

		if (Preview.type == 'ChoiceType') {
			Preview.input = Preview.tabStructureElement['ChoiceType'];
			Preview.input = Preview.input.replace('<option>valeur</option>', options);
		}

	}

	/**
	 * Fonction qui gère la prise en compte de la valeur par défaut, et qui la renvoie dans le preview
	 */
	Preview.GestionDefaultData = function()
	{
		var html = '';	
		if(Preview.type == 'ChoiceType')
		{
			html = '<div class="form-group"><label for="inputState">Selectionner une valeur par défaut</label><select id="select-default-input" class="form-control">' + Preview.options_liste_valeur + '</select></div>';
			$(Preview.id_global + ' ' + Preview.id_contener_input_def_value).html(html);
			$(Preview.id_global + ' #question_valeur_defaut').val(Preview.def_selected_valeur);
		}
	}
}

//extension de Jquery pour récupérer le type d'élément HTML (ex : tag = 'div')
$.fn.tagName = function() {    return this.get(0).tagName.toLowerCase(); }