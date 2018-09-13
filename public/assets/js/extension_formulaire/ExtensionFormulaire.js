ExtensionFormulaire = {};

ExtensionFormulaire.Launch = function(params) {
	
	//id du conteneur
	ExtensionFormulaire.container = $(params.id_container);
	//id du bouton d'ajout
	ExtensionFormulaire.id_btn_add = params.id_btn_add;
	//id du titre du formulaire imbriqué
	ExtensionFormulaire.id_label_fieldset = params.id_label_fieldset;
	//id du bouton de suppression
	ExtensionFormulaire.btn_delete = params.btn_delete;

	// On définit un compteur unique pour nommer les champs qu'on va ajouter dynamiquement
	ExtensionFormulaire.index = ExtensionFormulaire.container.find(':input').length;

	ExtensionFormulaire.Event = function() {
		
		// Ajout d'un élément au clic
		$(ExtensionFormulaire.id_btn_add).click(function(e) {
			ExtensionFormulaire.AddElement();

			e.preventDefault(); // évite qu'un # apparaisse dans l'URL
			return false;
		});

		if (ExtensionFormulaire.index != 0) {
			// S'il existe déjà des éléments, on ajoute un lien de suppression pour chacun d'entre eux
			ExtensionFormulaire.container.children('div').each(function() {
				ExtensionFormulaire.addDeleteLink($(this));
			});
		}
	}

	//ajout d'un élément dans le conteneur
	ExtensionFormulaire.AddElement = function() {
		
		$(ExtensionFormulaire.id_label_fieldset).hide();

		var template = ExtensionFormulaire.container.attr('data-prototype').replace(/__name__/g, ExtensionFormulaire.index);

		// On crée un objet jquery qui contient ce template
		var prototype = $(template);

		// On ajoute au prototype un lien pour pouvoir supprimer l'élément
		ExtensionFormulaire.AddDeleteLink(prototype);

		// On ajoute le prototype modifié à la fin de la balise <div>
		ExtensionFormulaire.container.append(prototype);

		// Enfin, on incrémente le compteur pour que le prochain ajout se fasse avec un autre numéro
		ExtensionFormulaire.index++;
	}
	
	//création d'un lien de suppression d'un élément appartenant au conteneur
	ExtensionFormulaire.AddDeleteLink = function(prototype) {
		// Création du lien
		var deleteLink = $(ExtensionFormulaire.btn_delete);

		// Ajout du lien
		prototype.append(deleteLink);

		// Ajout du listener sur le clic du lien pour effectivement supprimer l'élément
		deleteLink.click(function(e) {

			$(ExtensionFormulaire.id_label_fieldset).show();

			prototype.remove();
			e.preventDefault();
			return false;
		});
	}
}