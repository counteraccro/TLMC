//objet JS pour un type d'événement
TypeEvenement = {};

TypeEvenement.Launch = function(params) {
	
	//cible la div '#bloc_modal'
	TypeEvenement.id_modal = params.id_modal;

	TypeEvenement.id_content_modal = params.id_content_modal;

	TypeEvenement.id_container_global = '#container-global';
	
	/**
	 * Méthode Ajax qui va charger l'element présent dans l'URL
	 */
	TypeEvenement.Ajax = function(url, id_done, method = 'GET')
	{	
		$.ajax({
			method: method,
			url: url,
		})
		.done(function( html ) {
			$(id_done).html(html);
			
			$(TypeEvenement.id_container_global).hideLoading();
		});
	}
	
	/**
	 * Evénement ajout d'un type d'événement
	 */
	TypeEvenement.EventAdd = function(id)
	{
		// Event sur le bouton edit d'un témoignage
		$(id).click(function() {
			//on passe l'url et l'id_done

			$(TypeEvenement.id_container_global).showLoading();
			
			TypeEvenement.Ajax($(this).attr('href'), TypeEvenement.id_content_modal);
			return false;
		});
	}

	/**
	 * traitement du formulaire d'ajout d'un type d'événement
	 */
	TypeEvenement.EventAddSubmit = function(url)
	{
		$("form[name*='type_evenement']").on( "submit", function( event ) {
			
			$('#ajax_type_evenement_add').showLoading();

			$('#type_evenement_save').prop('disabled', true).html('Chargement...');

			event.preventDefault();

			//envoi d'une requête POST en AJAX
			$.ajax({
				method: 'POST',
				url: url,
				data: $(this).serialize()
			})
			.done(function( reponse ) {

				$('#ajax_type_evenement_add').hideLoading();

				if(reponse.statut === true)
				{
					//on cache la modale si le formulaire est valide
					$(TypeEvenement.id_modal).modal('hide');
					$('#evenement_type').append('<option value="' + reponse.id + '" selected="selected">' + reponse.nom + '</option>');
				}
				else
				{
					//on revient sur le formulaire s'il est incorrect
					$(TypeEvenement.id_content_modal).html(reponse);
				}
			});
		});
	}
}