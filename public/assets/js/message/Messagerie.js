Messagerie = {};

Messagerie.Launch = function(params){
	
	Messagerie.urlRef = params.urlRef;
	Messagerie.urlReception = params.urlReception;
	Messagerie.urlBrouillons = params.urlBrouillons;
	Messagerie.urlCorbeille = params.urlCorbeille;
	Messagerie.urlEnvoyes = params.urlEnvoyes;
	Messagerie.urlPreview = params.urlPreview;
	
	Messagerie.element_navPills = $('#messagerie-block #v-pills-messagerie .nav-link');
	Messagerie.globale_content_pills = $('#messagerie-block #v-pills-tabContent');
	Messagerie.element_navPills_content_reception = $('#messagerie-block #v-pills-reception');
	Messagerie.element_navPills_content_brouillons = $('#messagerie-block #v-pills-brouillons');
	Messagerie.element_navPills_content_corbeille = $('#messagerie-block #v-pills-corbeille');
	Messagerie.element_navPills_content_envoyes = $('#messagerie-block #v-pills-envoyes');
	Messagerie.element_navPills_content_preview = $('#messagerie-block #v-pills-preview');
	
	Messagerie.preview_content_pills = $('#messagerie-block #v-pills-tabPreview');
	
	/**
	 * Evenements globale à la page index
	 */
	Messagerie.Event = function() {
		
		Messagerie.element_navPills.click(function() {
			Messagerie.LoadElement($(this).attr('id'));
		})
		
	}
	
	/**
	 * Charge un element en fonction de son id (pills)
	 */
	Messagerie.LoadElement = function(id)
	{
		var url = '#';
		var content = '';
		var preview = '';
		
		switch (id) {
		case 'reception':
			url = Messagerie.urlReception;
			content = Messagerie.element_navPills_content_reception;
			preview = Messagerie.element_navPills_content_preview;
			break;
		case 'brouillons':
			url = Messagerie.urlBrouillons;
			content = Messagerie.element_navPills_content_brouillons;
			preview = Messagerie.element_navPills_content_preview;
			break;
		case 'corbeille':
			url = Messagerie.urlCorbeille;
			content = Messagerie.element_navPills_content_corbeille;
			preview = Messagerie.element_navPills_content_preview;
			break;
		case 'envoyes':
			url = Messagerie.urlEnvoyes;
			content = Messagerie.element_navPills_content_envoyes;
			preview = Messagerie.element_navPills_content_preview;
			break;
		case 'preview':
			url = Messagerie.urlPreview;
			content = Messagerie.element_navPills_content_preview;
			preview = Messagerie.element_navPills_content_preview;
			break;

			
		default:
			alert('Element inconnu');
			return false;
			break;
		}
		
		Messagerie.globale_content_pills.showLoading();
		Messagerie.preview_content_pills.showLoading();
		
		$.ajax({
			method: 'GET',
			url: url,
		})
		.done(function( html ) {
			content.html(html);
			preview.html(html);
			Messagerie.globale_content_pills.hideLoading();
			Messagerie.preview_content_pills.hideLoading();
		});
	}
}