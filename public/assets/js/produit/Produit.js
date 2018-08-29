Produit = {};

Produit.Launch = function(params){

	//url de la fiche questionnaire
	Produit.url_ajax_see = params.url_ajax_see;
	//cible la div '#bloc_questionnaire'
	Produit.id_global = params.id_global;
	//cible la div '#bloc_modal'
	Produit.id_modal = params.id_modal;
	
	Produit.id_content_modal = params.id_content_modal;
	
	Produit.id_container_global = '#container-global';

	/**
	 * fonction prévue pour le chargement des familles du Produit, paramètres url et id
	 */
	Produit.LoadProduit = function()
	{
		Produit.Ajax(Produit.url_ajax_see, Produit.id_global);
	}
	
	/**
	 * Méthode Ajax qui va charger l'element présent dans l'URL
	 */
	Produit.Ajax = function(url, id_done, method = 'GET')
	{		
		$.ajax({
			method: method,
			url: url,
		})
		.done(function( html ) {
			$(id_done).html(html)
			$(Produit.id_container_global).hideLoading();
		});
		
	}

	/**
	 * Evenement global
	 */
	Produit.EventEdit = function(id)
	{
		// Event sur le bouton edit d'un Produit
		$(id).click(function() {
			//on passe l'url et l'id_done
			
			$(Produit.id_container_global).showLoading();
			Produit.Ajax($(this).attr('href'), Produit.id_content_modal);
			return false;
		});
	}
	
	/**
	 * Fonction intervenant au moment de la soumission du formulaire Ajax d'édition
	 */
	Produit.EventEditSubmit = function(url)
	{
		$("form[name*='produit']").on( "submit", function( event ) {
			
			$('#ajax_produit_edit').showLoading();
			
			$('#produit_save').prop('disabled', true).html('Chargement...');
			
			event.preventDefault();

			$.ajax({
				method: 'POST',
				url: url,
				data: $(this).serialize()
			})
			.done(function( reponse ) {
	
				$('#ajax_produit_edit').hideLoading();
				
				if(reponse.statut === true)
				{
					$(Produit.id_modal).modal('hide');
					Produit.Ajax(Produit.url_ajax_see, Produit.id_global);
				}
				else
				{
					$(Produit.id_content_modal).html(reponse);
				}
			});
		});
	}
	
	/**
	 * désactivation d'un produit au click
	 */
	Produit.EventDelete = function(id, url)
	{
		$(id).click(function(){
			$(Produit.id_global).showLoading();
			event.preventDefault();

			//envoi d'une requête POST en AJAX
			$.ajax({
				method: 'POST',
				url: $(this).attr('href')
			})
			.done(function( reponse ) {

				if(reponse.statut === true)
				{
					$(Produit.id_global).hideLoading();
					Produit.Ajax(Produit.url_ajax_see, Produit.id_global);
				}
			});
		});
	}
}