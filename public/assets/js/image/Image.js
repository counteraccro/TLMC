Image = {};

Image.Launch = function(){
	
	Image.message_erreur = "Le fichier n'est pas au bon format. Formats de fichier acceptés: jpg, jpeg, png, gif";
	
	Image.UpdateInput = function(id, label, texte_aide){
		$(id).change(function() {
			if(($(this)[0].files.length > 0)){
				var file = $(this)[0].files[0].name;
				$('.custom-file label[for="'+ $(this).attr('id') +'"]').html(file);
			} else {
				var file = '';
				$('.custom-file label[for="'+ $(this).attr('id') +'"]').html(label);
			}
			
			
			if(!file.match(/\.(jpg|jpeg|png)$/i) && file != ''){
				$(this).attr('class', 'custom-file-input is-invalid');
				$(id + '_help').attr('class', 'form-text text-danger').html(Image.message_erreur);
			} else {
				$(this).attr('class', 'custom-file-input');	
				$(id + '_help').attr('class', 'form-text text-muted').html(texte_aide);
			}
		});
	}
}