//objet JS pour le Témoignage
Temoignage = {};

Temoignage.Launch = function(params) {
	
	//url de la fiche des infos du témoignage ou de la liste des témoignages
	Temoignage.url_ajax_see = params.url_ajax_see;
	//cible la div où sont donnés les informations sur le témoignages ou la liste des témoignages d'un produit ou d'un événement
	Temoignage.id_global = params.id_global;
	//cible la div '#bloc_modal'
	Temoignage.id_modal = params.id_modal;

	Temoignage.id_content_modal = params.id_content_modal;

	Temoignage.id_container_global = '#container-global';

	/**
	 * Méthode Ajax qui va charger l'element présent dans l'URL
	 */
	Temoignage.Ajax = function(url, id_done, method = 'GET')
	{	
		$.ajax({
			method: method,
			url: url,
		})
		.done(function( html ) {
			$(id_done).html(html);
			
			$(Temoignage.id_container_global).hideLoading();
			$(Temoignage.id_global).hideLoading();
		});
	}
	
	/**
	 * Fonction prévue pour le chargement des témoignages du membre, d'un produit ou d'un événement, paramètres url et id
	 */
	Temoignage.LoadTemoignage = function()
	{
		Temoignage.Ajax(Temoignage.url_ajax_see, Temoignage.id_global);
	}
	
	/**
	 * Evénement ajout d'un témoignage
	 */
	Temoignage.EventAdd = function(id)
	{
		// Event sur le bouton edit d'un témoignage
		$(id).click(function() {
			//on passe l'url et l'id_done

			$(Temoignage.id_container_global).showLoading();
			
			Temoignage.Ajax($(this).attr('href'), Temoignage.id_content_modal);
			return false;
		});
	}

	/**
	 * traitement du formulaire d'ajout d'un témoignage
	 */
	Temoignage.EventAddSubmit = function(url)
	{
		$("form[name*='temoignage']").on( "submit", function( event ) {
			
			$('#ajax_temoignage_add').showLoading();

			$('#temoignage_save').prop('disabled', true).html('Chargement...');

			event.preventDefault();

			//envoi d'une requête POST en AJAX
			$.ajax({
				method: 'POST',
				url: url,
				data: $(this).serialize()
			})
			.done(function( reponse ) {

				$('#ajax_temoignage_add').hideLoading();

				if(reponse.statut === true)
				{
					//on cache la modale si le formulaire est valide
					$(Temoignage.id_modal).modal('hide');
					Temoignage.Ajax(Temoignage.url_ajax_see, Temoignage.id_global);
				}
				else
				{
					//on revient sur le formulaire s'il est incorrect
					$(Temoignage.id_content_modal).html(reponse);
				}
			});
		});
	}
	
	/**
	 * Evenement édition d'un témoignage
	 */
	Temoignage.EventEdit = function(id)
	{
		// Event sur le bouton edit d'un témoignage
		$(id).click(function() {
			//on passe l'url et l'id_done

			$(Temoignage.id_container_global).showLoading();
			
			Temoignage.Ajax($(this).attr('href'), Temoignage.id_content_modal);
			return false;
		});
	}

	/**
	 * traitement du formulaire d'édition d'un témoignage
	 */
	Temoignage.EventEditSubmit = function(url)
	{
		$("form[name*='temoignage']").on( "submit", function( event ) {
			
			$('#ajax_temoignage_edit').showLoading();

			$('#temoignage_save').prop('disabled', true).html('Chargement...');

			event.preventDefault();

			//envoi d'une requête POST en AJAX
			$.ajax({
				method: 'POST',
				url: url,
				data: $(this).serialize()
			})
			.done(function( reponse ) {

				$('#ajax_temoignage_edit').hideLoading();

				if(reponse.statut === true)
				{
					//on cache la modale si le formulaire est valide
					$(Temoignage.id_modal).modal('hide');
					Temoignage.Ajax(Temoignage.url_ajax_see + '/' + reponse.page, Temoignage.id_global);
				}
				else
				{
					//on revient sur le formulaire s'il est incorrect
					$(Temoignage.id_content_modal).html(reponse);
				}
			});
		});
	}
	
	/**
	 * désactivation d'un témoignage au click
	 */
	Temoignage.EventDelete = function(id, url)
	{
		$(id).click(function(){
			$(Temoignage.id_global).showLoading();
			event.preventDefault();

			//envoi d'une requête POST en AJAX
			$.ajax({
				method: 'POST',
				url: $(this).attr('href')
			})
			.done(function( reponse ) {

				if(reponse.statut === true)
				{
					Temoignage.Ajax(Temoignage.url_ajax_see + '/' + reponse.page, Temoignage.id_global);
					$(Temoignage.id_global).hideLoading();
				}
			});
		});
	}

	Temoignage.UpdateField = function(type)
	{
		var id_div = '#temoignage_row_';
		if (type != 'evenement' && type != 'produit'){
			$( id_div + 'produit').hide();
			
			$('#temoignage_type').change(function(){
				if($(this).val() == 1){
					$(id_div + '#temoignage_row_produit').show();
					
					$(id_div + 'evenement').hide();
					$(id_div + 'evenement #temoignage_evenement').val(0);
					$(id_div + 'evenement #temoignage_famille').val(0);
				} else {
					$(id_div + 'evenement').show();
				
					$(id_div + '#produit').hide();
					$(id_div + 'produit #temoignage_produit').val(0);
				}
			});
		} else if (type == 'evenement'){
    		$(id_div + type + ' #temoignage_evenement').attr('required', 'required');
    		$(id_div + type + ' #temoignage_famille').attr('required', 'required');
    		$(id_div + 'produit').hide();
		} else if (type == 'produit'){
			$(id_div + type + ' #temoignage_produit').attr('required', 'required');
			$( id_div + 'evenement').hide();
		}
	}
}