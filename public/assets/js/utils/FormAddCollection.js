FormAddCollection = {};

FormAddCollection.Launch = function(params) {

	FormAddCollection.container = $(params.id_container);
	FormAddCollection.id_btn_add = params.id_btn_add;
	FormAddCollection.id_label_fieldset = params.id_label_fieldset;
	FormAddCollection.btn_delete = params.btn_delete;

	// On définit un compteur unique pour nommer les champs qu'on va ajouter dynamiquement
	FormAddCollection.index = FormAddCollection.container.find(':input').length;

	FormAddCollection.Event = function() {

		$(FormAddCollection.id_btn_add).click(function(e) {
			FormAddCollection.AddElement();

			e.preventDefault();
			return false;
		});

		if (FormAddCollection.index == 0) {
			FormAddCollection.AddElement();
		} else {
			// S'il existe déjà des catégories, on ajoute un lien de suppression pour chacune d'entre elles
			FormAddCollection.container.children('div').each(function() {
				FormAddCollection.addDeleteLink($(this));
			});

		}
	}

	FormAddCollection.AddElement = function() {

		$(FormAddCollection.id_label_fieldset).hide();

		var template = FormAddCollection.container.attr('data-prototype');

		// On crée un objet jquery qui contient ce template
		var prototype = $(template);

		// On ajoute au prototype un lien pour pouvoir supprimer la catégorie
		FormAddCollection.AddDeleteLink(prototype);

		// On ajoute le prototype modifié à la fin de la balise <div>
		FormAddCollection.container.append(prototype);

		// Enfin, on incrémente le compteur pour que le prochain ajout se fasse avec un autre numéro
		FormAddCollection.index++;
	}

	FormAddCollection.AddDeleteLink = function(prototype) {
		// Création du lien
		var deleteLink = $(FormAddCollection.btn_delete);

		// Ajout du lien
		prototype.append(deleteLink);

		// Ajout du listener sur le clic du lien pour effectivement supprimer la catégorie
		deleteLink.click(function(e) {

			$(FormAddCollection.id_label_fieldset).show();

			prototype.remove();
			e.preventDefault(); // évite qu'un # apparaisse dans l'URL
			return false;
		});
	}
}