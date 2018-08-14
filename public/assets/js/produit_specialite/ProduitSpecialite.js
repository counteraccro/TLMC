//objet JS pour le lien produit_specialite
ProduitSpecialite = {};

ProduitSpecialite.Launch = function(params) {

	//url de la fiche événement
	ProduitSpecialite.url_ajax_see = params.url_ajax_see;
	//cible la div '#bloc_evenement_specialite'
	ProduitSpecialite.id_global = params.id_global;
	//cible la div '#bloc_evenement_global'
	ProduitSpecialite.id_modal = params.id_modal;
	//cible la div '#bloc_modal'
	ProduitSpecialite.id_content_modal = params.id_content_modal;

	ProduitSpecialite.id_container_global = '#container-global';

	/**
	 * fonction prévue pour le chargement des liens du produit, paramètres url et id
	 */
	ProduitSpecialite.LoadProduitLiens = function()
	{
		ProduitSpecialite.Ajax(ProduitSpecialite.url_ajax_see, ProduitSpecialite.id_global);
	}
	
	/**
	 * fonction prévue pour le chargement des spécialités associées à l'événement, paramètres url et id
	 */
	ProduitSpecialite.LoadProduitSpecialite = function()
	{
		ProduitSpecialite.Ajax(ProduitSpecialite.url_ajax_see, ProduitSpecialite.id_global);
	}

	/**
	 * Méthode Ajax qui va charger l'element présent dans l'URL
	 */
	ProduitSpecialite.Ajax = function(url, id_done, method = 'GET')
	{	
		$.ajax({
			method: method,
			url: url,
		})
		.done(function( html ) {
			$(id_done).html(html)
			$(ProduitSpecialite.id_container_global).hideLoading();
		});
	}

	/**
	 * Evenement ajout d'un lien produit - spécialité
	 */
	ProduitSpecialite.EventAdd = function(id)
	{
		// Event sur le bouton add d'un lien produit - spécialité
		$(id).click(function() {
			//on passe l'url et l'id_done

			$(ProduitSpecialite.id_container_global).showLoading();

			ProduitSpecialite.Ajax($(this).attr('href'), ProduitSpecialite.id_content_modal);
			return false;
		});
	}

	/**
	 * traitement du formulaire d'ajout d'un lien produit - spécialité
	 */
	ProduitSpecialite.EventAddSubmit = function(url)
	{
		$("form[name*='produit_specialite']").on( "submit", function( event ) {

			$('#ajax_produit_specialite_add').showLoading();

			$('#produit_specialite_save').prop('disabled', true).html('loading...');

			event.preventDefault();

			//envoi d'une requête POST en AJAX
			$.ajax({
				method: 'POST',
				url: url,
				data: $(this).serialize()
			})
			.done(function( reponse ) {

				$('#ajax_produit_specialite_add').hideLoading();

				if(reponse.statut === true)
				{
					//on cache la modale si le formulaire est valide
					$(ProduitSpecialite.id_modal).modal('hide');
					ProduitSpecialite.Ajax(ProduitSpecialite.url_ajax_see, ProduitSpecialite.id_global);
				}
				else
				{
					//on revient sur le formulaire s'il est incorrect
					$(ProduitSpecialite.id_content_modal).html(reponse);
				}
			});
		});
	}

	/**
	 * Evenement édition d'un lien produit - spécialité
	 */
	ProduitSpecialite.EventEdit = function(id)
	{
		// Event sur le bouton edit d'un lien produit - spécialité
		$(id).click(function() {
			//on passe l'url et l'id_done

			$(ProduitSpecialite.id_container_global).showLoading();

			ProduitSpecialite.Ajax($(this).attr('href'), ProduitSpecialite.id_content_modal);
			return false;
		});
	}

	/**
	 * traitement du formulaire d'édition d'un lien produit - spécialité
	 */
	ProduitSpecialite.EventEditSubmit = function(url)
	{
		$("form[name*='produit_specialite']").on( "submit", function( event ) {
			
			$('#ajax_produit_specialite_edit').showLoading();

			$('#produit_specialite_save').prop('disabled', true).html('loading...');

			event.preventDefault();

			//envoi d'une requête POST en AJAX
			$.ajax({
				method: 'POST',
				url: url,
				data: $(this).serialize()
			})
			.done(function( reponse ) {

				$('#ajax_produit_specialite_edit').hideLoading();

				if(reponse.statut === true)
				{
					//on cache la modale si le formulaire est valide
					$(ProduitSpecialite.id_modal).modal('hide');
					ProduitSpecialite.Ajax(ProduitSpecialite.url_ajax_see, ProduitSpecialite.id_global);
				}
				else
				{
					//on revient sur le formulaire s'il est incorrect
					$(ProduitSpecialite.id_content_modal).html(reponse);
				}
			});
		});
	}
}