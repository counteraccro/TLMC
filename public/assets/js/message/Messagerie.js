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

	Messagerie.global_content_bloc_message = $('#messagerie-block #bloc_view_message');
	Messagerie.popin = $('#messagerie-block #modal_message_global #bloc_modal');



	/**
	 * Evenement global à la page index
	 */
	Messagerie.Event = function() {

		Messagerie.element_navPills.click(function() {
			Messagerie.LoadElement($(this).attr('id'));
		});
	}

	/**
	 * Evènement sur liste des messages (reception, brouillons, envoyes, corbeille etc..)
	 */
	Messagerie.EventListeMessage = function(id_global) {

		$(id_global + ' .list-group a.list-group-item').click(function() {

			var lu = true;
			if($(this).hasClass('no-read'))
			{
				lu = false;
			}

			// Touche ctrl apuyée
			if(event.ctrlKey)
			{
				$(id_global + ' .list-group a.list-group-item').each(function() {
					if($(this).hasClass('active')) {
						$(this).children('span').removeClass().addClass('oi oi-check');
					}
				});

				if($(this).hasClass('active'))
				{
					$(this).removeClass('active');
					if(lu)
					{
						$(this).children('span').removeClass('oi-check').addClass('oi-envelope-open');
					}
					else
					{
						$(this).children('span').removeClass('oi-check').addClass('oi-envelope-closed');
					}
				}
				else
				{
					$(this).addClass('active');
					$(this).children('span').removeClass().addClass('oi oi-check');
				}

				$(id_global + ' #select-all').children('span').removeClass().addClass('oi oi-arrow-thick-top');
				$(id_global + ' #select-all').tooltip('hide').attr('data-original-title', 'Tout désélectionner');
			}
			// Simple click
			else
			{
				$(id_global + ' .list-group a.list-group-item').each(function() {
					$(this).removeClass('active');
					if($(this).hasClass('no-read'))
					{
						$(this).children('span').removeClass('oi-check').addClass('oi-envelope-closed');
					}
					else
					{
						$(this).children('span').removeClass('oi-check').addClass('oi-envelope-open');
					}

				});

				$(id_global + ' #select-all').children('span').removeClass().addClass('oi oi-arrow-thick-bottom');
				$(id_global + ' #select-all').tooltip('hide').attr('data-original-title', 'Tout sélectionner');

				$(this).addClass('active');
				$(this).children('span').removeClass().addClass('oi oi-envelope-open');
				$(this).children('div[class="small-body"]').css( "font-weight", "normal" );
				$(this).removeClass('no-read');
				Messagerie.LoadMessage($(this).attr('href'));
			}
			return false;
		});

		/**
		 * Evenement au clic sur le bouton "tout selectionner/tout dé-selectionner
		 **/
		$(id_global + ' #select-all').click(function() {

			$(id_global + ' .list-group a.list-group-item').each(function() {

				var lu = true;
				if($(this).hasClass('no-read'))
				{
					lu = false;
				}

				if ($(id_global + ' #select-all').children('span').hasClass('oi oi-arrow-thick-bottom'))  
				{

					$(this).addClass('active');
					$(this).children('span').removeClass().addClass('oi oi-check');

				}
				else
				{

					$(this).removeClass('active');
					if(lu)
					{
						$(this).children('span').removeClass('oi-check').addClass('oi-envelope-open');
					}
					else
					{
						$(this).children('span').removeClass('oi-check').addClass('oi-envelope-closed');
					}
				}

			});

			if ($(id_global + ' #select-all').children('span').hasClass('oi oi-arrow-thick-bottom'))  
			{
				$(id_global + ' #select-all').children('span').removeClass().addClass('oi oi-arrow-thick-top');
				$(id_global + ' #select-all').tooltip('hide').attr('data-original-title', 'Tout désélectionner').tooltip('show');
			}
			else
			{
				$(id_global + ' #select-all').children('span').removeClass().addClass('oi oi-arrow-thick-bottom');
				$(id_global + ' #select-all').tooltip('hide').attr('data-original-title', 'Tout sélectionner').tooltip('show');
			}

		});

		/**
		 * 
		 */
		$(id_global + ' .msg-read-no-read').click(function() {

			var tabId = [];
			role = $(this).data('role')

			$(id_global + ' .list-group a.list-group-item').each(function() {
				if($(this).hasClass('active'))
				{
					tabId.push($(this).data('id'));
				}
			});

			var isRead = 0;
			if($(this).attr('id') == 'message-read')
			{
				isRead = 1;
			}


			Messagerie.globale_content_pills.showLoading();
			$.ajax({
				method: 'POST',
				url: $(this).attr('href'),
				data : {data : tabId, 'isRead' : isRead},
			})
			.done(function( json ) {
				Messagerie.globale_content_pills.hideLoading();
				if(json.statut == 1)
				{
					Messagerie.LoadElement(role);
				}
			});

			return false;

		});

		/**
		 * Evenement au clic sur le bouton "Corbeille"
		 **/
		$(id_global + ' #delete-all').click(function() {

			$(id_global + ' #delete-all').tooltip('hide')

			var tabId = [];
			var role = $(this).data('role')
			var corbeille = $(this).data('corbeille');

			$(id_global + ' .list-group a.list-group-item').each(function() {
				if($(this).hasClass('active'))
				{
					tabId.push($(this).data('id'));
				}
			});

			$.ajax({
				method: 'POST',
				url: $(this).attr('href'),
				data : {data : tabId, 'corbeille' : corbeille},
			})
			.done(function( json ) {
				Messagerie.globale_content_pills.hideLoading();
				if(json.statut == 1)
				{
					Messagerie.LoadElement(role);
				}
			});

			return false;
		});

		$(id_global + ' #new-message').click(function() {	
			$(this).tooltip('hide');
			$.ajax({
				method: 'GET',
				url: $(this).attr('href'),
			})
			.done(function( html ) {
				Messagerie.popin.html(html);
			});
			return false;
		});


	}

	Messagerie.EventPopin = function()
	{
		$('#ajax_new_message #form_message').submit(function() {


			$.ajax({
				method: 'POST',
				url: $(this).attr('action'),
				data : $(this).serialize(),
			})
			.done(function( html ) {

			});

			return false;
		});
	}

	Messagerie.AutoComplete = function(url_json)
	{
		url_json = url_json.substring(1,url_json.length);

		$( function() {
			function split( val ) {
				return val.split( /,\s*/ );
			}
			function extractLast( term ) {
				return split( term ).pop();
			}

			var json = {};
			var tabIdSet = [];

			$( "#destinataire")
			// don't navigate away from the field on tab when selecting an item
			.on( "keydown", function( event ) {
				if ( event.keyCode === $.ui.keyCode.TAB &&
						$( this ).autocomplete( "instance" ).menu.active ) {
					event.preventDefault();
				}
			})
			.autocomplete({
				source: function( request, response ) {

					var j = $.getJSON( url_json, {
						term: extractLast( request.term )
					}, response )
					.done(function(data) {
						json = data;
					});

					console.log(j);
				},
				search: function() {
					// custom minLength
					var term = extractLast( this.value );
					if ( term.length < 2 ) {
						return false;
					}
				},
				focus: function() {
					// prevent value inserted on focus
					return false;
				},
				select: function( event, ui ) {
					var terms = split( this.value );	          
					// remove the current input
					terms.pop();

					// add the selected item
					terms.push(ui.item.value);

					// add placeholder to get the comma-and-space at the end
					terms.push( "" );

					this.value = terms.join( ", " );

					return false;
				}
			})
			.change(function(event, ui) {
				
				var terms = split( this.value );
				var tabId = [];
				for (var id in json) 
				{
					//console.log(prop + ' ' + json[prop]);
					for (var t in terms)
					{
						console.log(t + ' ' + terms[t]);
						if(terms[t] == json[id])
						{
							tabId[id] = json[id];
							tabIdSet[id] = json[id];
							
						}
					}
				}
				
				for (var id in tabIdSet) 
				{
					for (var t in terms)
					{
						console.log(t + ' ' + terms[t]);
						if(terms[t] == tabIdSet[id])
						{
							tabId[id] = tabIdSet[id];
						}
					}
				}
				
				var ids = '';
				for (var id in tabId) 
				{
					ids += id + '-';
				}
				ids = ids.substring(0,ids.length-1);
				$('#destinataire_hidden').val(ids);
			})
		} );
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
			alert('Element inconnu ' + id);
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

	/**
	 * Appel ajax du message selectionné (ouverture et fermeture pop-in)
	 */
	Messagerie.LoadMessage = function(url)
	{
		Messagerie.global_content_bloc_message.showLoading();

		$.ajax({
			method: 'GET',
			url: url,
		})
		.done(function( html ) {
			Messagerie.global_content_bloc_message.html(html);
			Messagerie.global_content_bloc_message.hideLoading();
		});
	}
}