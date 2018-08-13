FormAddCollection = {};

FormAddCollection.Launch = function(params) {
	
	//id du conteneur
	FormAddCollection.container = $(params.id_container);
	//id du bouton d'ajout
	FormAddCollection.id_btn_add = params.id_btn_add;
	//id du titre du formulaire imbriqué
	FormAddCollection.id_label_fieldset = params.id_label_fieldset;
	//id du bouton de suppression
	FormAddCollection.btn_delete = params.btn_delete;

	// On définit un compteur unique pour nommer les champs qu'on va ajouter dynamiquement
	FormAddCollection.index = FormAddCollection.container.find(':input').length;

	FormAddCollection.Event = function() {
		
		// Ajout d'un élément au clic
		$(FormAddCollection.id_btn_add).click(function(e) {
			FormAddCollection.AddElement();

			e.preventDefault(); // évite qu'un # apparaisse dans l'URL
			return false;
		});

		if (FormAddCollection.index == 0) {
			FormAddCollection.AddElement();
		} else {
			// S'il existe déjà des éléments, on ajoute un lien de suppression pour chacun d'entre eux
			FormAddCollection.container.children('div').each(function() {
				FormAddCollection.addDeleteLink($(this));
			});
		}
	}

	//ajout d'un élément dans le conteneur
	FormAddCollection.AddElement = function() {
		
		$(FormAddCollection.id_label_fieldset).hide();

		var template = FormAddCollection.container.attr('data-prototype').replace(/__name__/g, FormAddCollection.index);

		// On crée un objet jquery qui contient ce template
		var prototype = $(template);

		// On ajoute au prototype un lien pour pouvoir supprimer l'élément
		FormAddCollection.AddDeleteLink(prototype);

		// On ajoute le prototype modifié à la fin de la balise <div>
		FormAddCollection.container.append(prototype);

		// Enfin, on incrémente le compteur pour que le prochain ajout se fasse avec un autre numéro
		FormAddCollection.index++;
	}
	
	//création d'un lien de suppression d'un élément appartenant au conteneur
	FormAddCollection.AddDeleteLink = function(prototype) {
		// Création du lien
		var deleteLink = $(FormAddCollection.btn_delete);

		// Ajout du lien
		prototype.append(deleteLink);

		// Ajout du listener sur le clic du lien pour effectivement supprimer l'élément
		deleteLink.click(function(e) {

			$(FormAddCollection.id_label_fieldset).show();

			prototype.remove();
			e.preventDefault();
			return false;
		});
	}
}