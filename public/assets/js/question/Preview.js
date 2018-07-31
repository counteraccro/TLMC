//objet JS pour l'affichage des previews
Preview = {};

Preview.Launch = function(params) {

	Preview.id_global = params.id_global;
	Preview.id_preview = params.id_global + ' ' + params.id_preview;
	Preview.liste_type_json = params.liste_type_json;
	Preview.id_input_liste_val = '#contener-input-liste-val';
	Preview.json_liste_val = '';

	Preview.html = '';
	Preview.input = '';
	Preview.label = '';
	Preview.label_bottom = '';
	Preview.label_top = '';

	Preview.tabStructureElement = {
			ChoiceType : '<select class="form-control" id="select-preview"><option>valeur</option></select>',
	};

	/**
	 * Fonction permettant le chargement du preview
	 */
	Preview.Load = function()
	{
		$(Preview.id_global + " .preview").each(function(){
			Preview.ConstructHtml($(this));
		});

		Preview.Render();

	}

	/**
	 * Fonction liée à l'évènement 'au changement' du type de champ souhaité
	 * Préparation du HTML
	 */
	Preview.Event = function()
	{
		$(Preview.id_global + " .preview").change(function(){
			Preview.ConstructHtml($(this));
			Preview.Render();
		});

		// évènement à l'ajout d'une nouvelle valeur de réponse possible
		$(Preview.id_global +  ' #btn-add-input-list').click(function() {

			Preview.json_liste_val[Object.keys(Preview.json_liste_val).length] = {'value' : '', 'libelle' : ''};
			Preview.GestionElementListe();
			Preview.Render();
			Preview.subRepeatEvent();
			return false;
		});

		
		Preview.subRepeatEvent = function()
		{
			// évènement à la suppression d'une valeur de réponse possible
			$(Preview.id_global +  ' .btn-delete-element-list').click(function() {
		
				var id = $(this).data('id');
				delete Preview.json_liste_val[id];
				Preview.GestionElementListe();
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
				Preview.Render();
				Preview.subRepeatEvent();
				
			});
		}

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
			Preview.label = '<label for="exampleInputEmail1">' + val + '</label>';
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
			Preview.json_liste_val = JSON.parse(form_element.val());
			Preview.GestionElementListe();
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
		Preview.html = '<div class="form-group">' + Preview.label_top + Preview.label + Preview.input + Preview.label_bottom + '</div>';
		/*<label for="exampleInputEmail1">Email address</label>
		    <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
		    <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
		  </div>'*/

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
				}
				else
				{
					Preview.input = '<div class="alert alert-warning" role="alert">Elément ' + key + ' non défini</div>';
				}
				break;
			}
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

			options += '<option value="' + json[i].value + '">' + json[i].libelle + '</option>';
		}

		$(Preview.id_input_liste_val).html(html);

		$(Preview.id_global + ' #question_liste_valeur').val(JSON.stringify(json));
		Preview.input = Preview.tabStructureElement['ChoiceType'];
		Preview.input = Preview.input.replace('<option>valeur</option>', options);
	}
}





