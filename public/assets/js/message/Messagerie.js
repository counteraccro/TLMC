Messagerie = {};

Messagerie.Launch = function(params){
	
	Messagerie.urlRef = params.urlRef;
	Messagerie.urlReception = params.urlReception;
	Messagerie.urlBrouillons = params.urlBrouillons;
	Messagerie.urlCorbeille = params.urlCorbeille;
	Messagerie.urlEnvoyes = params.urlEnvoyes;
	
	Messagerie.element_navPills = $('#messagerie-block #v-pills-messagerie .nav-link');
	Messagerie.globale_content_pills = $('#messagerie-block #v-pills-tabContent');
	Messagerie.element_navPills_content_reception = $('#messagerie-block #v-pills-reception');
	Messagerie.element_navPills_content_brouillons = $('#messagerie-block #v-pills-brouillons');
	Messagerie.element_navPills_content_corbeille = $('#messagerie-block #v-pills-corbeille');
	Messagerie.element_navPills_content_envoyes = $('#messagerie-block #v-pills-envoyes');
	
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
		
		switch (id) {
		case 'reception':
			url = Messagerie.urlReception;
			content = Messagerie.element_navPills_content_reception;
			break;
		case 'brouillons':
			url = Messagerie.urlBrouillons;
			content = Messagerie.element_navPills_content_brouillons;
			break;
		case 'corbeille':
			url = Messagerie.urlCorbeille;
			content = Messagerie.element_navPills_content_corbeille;
			break;
		case 'envoyes':
			url = Messagerie.urlEnvoyes;
			content = Messagerie.element_navPills_content_envoyes;
			break;
		default:
			alert('Element inconnu');
			return false;
			break;
		}
		
		Messagerie.globale_content_pills.showLoading();
		
		$.ajax({
			method: 'GET',
			url: url,
		})
		.done(function( html ) {
			content.html(html);
			Messagerie.globale_content_pills.hideLoading();
		});
	}
}