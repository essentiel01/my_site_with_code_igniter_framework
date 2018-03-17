$(document).ready(function () {
	//event click du boutton ajout de favoris pour les revues
	$('#addPostFavorite').on('click', addPostFavorite);

	//event click du boutton  ajout de favoris pour les chroniques
	$('#addChronicFavorite').on('click', addChronicFavorite);

	//event click du boutton commenter
	$('#addPostComment').on('click', addPostComment);

	//event click du boutton commenter
	$('#addChronicComment').on('click', addChronicComment);

	//permet d'afficher les commentaires d'un article et d'actualiser les données chaque seconde
	setInterval('getPostComments()', 1500);

	//permet d'afficher les commentaires d'une chronique et d'actualiser les données chaque seconde
	setInterval('getChronicComments()', 1500);

	//met le focus sur le champ commentaire lorsqu'on clique sur repondre
	$('main').on('click', '.reply', replyComment);

});
