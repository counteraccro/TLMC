//objet JS pour le lien produit_etablissement
ProduitEtablissement = {};

ProduitEtablissement.Launch = function(params) {

	//url de la fiche produit
	ProduitEtablissement.url_ajax_see = params.url_ajax_see;
	//cible la div '#bloc_produit_etablissement'
	ProduitEtablissement.id_global = params.id_global;
	//cible la div '#bloc_produit_global'
	ProduitEtablissement.id_modal = params.id_modal;
	//cible la div '#bloc_modal'
	ProduitEtablissement.id_content_modal = params.id_content_modal;

	ProduitEtablissement.id_container_global = '#container-global';

	/**
	 * fonction prévue pour le chargement des établissements associées au produit, paramètres url et id
	 */
	ProduitEtablissement.LoadProduitEtablissement = function()
	{
		ProduitEtablissement.Ajax(ProduitEtablissement.url_ajax_see, ProduitEtablissement.id_global);
	}

	/**
	 * Méthode Ajax qui va charger l'element présent dans l'URL
	 */
	ProduitEtablissement.Ajax = function(url, id_done, method = 'GET')
	{	
		$.ajax({
			method: method,
			url: url,
		})
		.done(function( html ) {
			$(id_done).html(html)
			$(ProduitEtablissement.id_container_global).hideLoading();
		});
	}

	/**
	 * Evenement ajout d'un lien produit - établissement
	 */
	ProduitEtablissement.EventAdd = function(id)
	{
		// Event sur le bouton add d'un lien produit - établissement
		$(id).click(function() {
			//on passe l'url et l'id_done

			$(ProduitEtablissement.id_container_global).showLoading();

			ProduitEtablissement.Ajax($(this).attr('href'), ProduitEtablissement.id_content_modal);
			return false;
		});
	}

	/**
	 * traitement du formulaire d'ajout d'un lien produit - établissement
	 */
	ProduitEtablissement.EventAddSubmit = function(url)
	{
		$("form[name*='produit_etablissement']").on( "submit", function( event ) {

			$('#ajax_produit_etablissement_add').showLoading();

			$('#produit_etablissement_save').prop('disabled', true).html('loading...');

			event.preventDefault();

			//envoi d'une requête POST en AJAX
			$.ajax({
				method: 'POST',
				url: url,
				data: $(this).serialize()
			})
			.done(function( reponse ) {

				$('#ajax_produit_etablissement_add').hideLoading();

				if(reponse.statut === true)
				{
					//on cache la modale si le formulaire est valide
					$(ProduitEtablissement.id_modal).modal('hide');
					ProduitEtablissement.Ajax(ProduitEtablissement.url_ajax_see, ProduitEtablissement.id_global);
				}
				else
				{
					//on revient sur le formulaire s'il est incorrect
					$(ProduitEtablissement.id_content_modal).html(reponse);
				}
			});
		});
	}

	/**
	 * Evenement édition d'un lien produit - établissement
	 */
	ProduitEtablissement.EventEdit = function(id)
	{
		// Event sur le bouton edit d'un lien produit - établissement
		$(id).click(function() {
			//on passe l'url et l'id_done

			$(ProduitEtablissement.id_container_global).showLoading();

			ProduitEtablissement.Ajax($(this).attr('href'), ProduitEtablissement.id_content_modal);
			return false;
		});
	}

	/**
	 * traitement du formulaire d'édition d'un lien produit - établissement
	 */
	ProduitEtablissement.EventEditSubmit = function(url)
	{
		$("form[name*='produit_etablissement']").on( "submit", function( event ) {
			
			$('#ajax_produit_etablissement_edit').showLoading();

			$('#produit_etablissement_save').prop('disabled', true).html('loading...');

			event.preventDefault();

			//envoi d'une requête POST en AJAX
			$.ajax({
				method: 'POST',
				url: url,
				data: $(this).serialize()
			})
			.done(function( reponse ) {

				$('#ajax_produit_etablissement_edit').hideLoading();

				if(reponse.statut === true)
				{
					//on cache la modale si le formulaire est valide
					$(ProduitEtablissement.id_modal).modal('hide');
					ProduitEtablissement.Ajax(ProduitEtablissement.url_ajax_see, ProduitEtablissement.id_global);
				}
				else
				{
					//on revient sur le formulaire s'il est incorrect
					$(ProduitEtablissement.id_content_modal).html(reponse);
				}
			});
		});
	}
}