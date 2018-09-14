//objet JS pour un type de produit
TypeProduit = {};

TypeProduit.Launch = function(params) {
	
	//cible la div '#bloc_modal'
	TypeProduit.id_modal = params.id_modal;

	TypeProduit.id_content_modal = params.id_content_modal;

	TypeProduit.id_container_global = '#container-global';
	
	/**
	 * Méthode Ajax qui va charger l'element présent dans l'URL
	 */
	TypeProduit.Ajax = function(url, id_done, method = 'GET')
	{	
		$.ajax({
			method: method,
			url: url,
		})
		.done(function( html ) {
			$(id_done).html(html);
			
			$(TypeProduit.id_container_global).hideLoading();
		});
	}
	
	/**
	 * Evénement ajout d'un type de produit
	 */
	TypeProduit.EventAdd = function(id)
	{
		// Event sur le bouton edit d'un témoignage
		$(id).click(function() {
			//on passe l'url et l'id_done

			$(TypeProduit.id_container_global).showLoading();
			
			TypeProduit.Ajax($(this).attr('href'), TypeProduit.id_content_modal);
			return false;
		});
	}

	/**
	 * traitement du formulaire d'ajout d'un type de produit
	 */
	TypeProduit.EventAddSubmit = function(url)
	{
		$("form[name*='type_produit']").on( "submit", function( event ) {
			
			$('#ajax_type_produit_add').showLoading();

			$('#type_produit_save').prop('disabled', true).html('Chargement...');

			event.preventDefault();

			//envoi d'une requête POST en AJAX
			$.ajax({
				method: 'POST',
				url: url,
				data: $(this).serialize()
			})
			.done(function( reponse ) {

				$('#ajax_type_produit_add').hideLoading();

				if(reponse.statut === true)
				{
					//on cache la modale si le formulaire est valide
					$(TypeProduit.id_modal).modal('hide');
					$('#produit_type').append('<option value="' + reponse.id + '" selected="selected">' + reponse.nom + '</option>');
				}
				else
				{
					//on revient sur le formulaire s'il est incorrect
					$(TypeProduit.id_content_modal).html(reponse);
				}
			});
		});
	}
}