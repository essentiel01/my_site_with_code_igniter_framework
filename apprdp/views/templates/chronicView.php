<div class="content">
	<!--breadcrumb --> 
	<nav class="breadcrumb-nav" aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Accueil</a></li>
			<li class="breadcrumb-item"><a href="<?= base_url($chronic->categoryName) ?>"><?= $chronic->categoryName ?></a></li>
			<li class="breadcrumb-item active" aria-current="page">
			<?= $chronic->chronicTitle ?>
			</li>
		</ol>
	</nav>
	<main class="main main-chronic">
			<!-- Button trigger favorisModal -->
			<div class="favoris">
				<button type="button" class="btn btn-lg btn-sm btn-favoris" data-toggle="modal" data-target="#favorisModal<?= $chronic->chronicId ?>">
					Favoris
				</button>
			</div>

			<!-- favorisModal -->
			<div class="modal fade fModal" id="favorisModal<?= $chronic->chronicId ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header header">
							<h5 class="modal-title" id="exampleModalLabel">Ajouter aux favoris</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body body">
							<p>Pays: <?= $chronic->countryName ?></p>
							<p>Rubrique: <?= $chronic->categoryName ?></p>
							<h2><?= $chronic->chronicTitle ?></h2>
						</div>
						<div class="modal-footer footer">
							<button type="button" id="modalCancel" class="btn btn-secondary btn-lg btn-sm" data-dismiss="modal">Annuler</button>
							<a  id="addChronicFavorite" class="btn btn-success btn-lg  btn-sm" role="button" aria-pressed="true" href="<?= base_url('connexion/formulaire') ?>" data-chronicId="<?= $chronic->chronicId ?>" data-userId="<?php if(isset($_SESSION['userData']->userId)) {echo $_SESSION['userData']->userId;} ?>">Ajouter</a>
						</div>
					</div>
				</div>
			</div>
			<!-- /modal -->

			<div class="singleChronic">
				<!-- icone favoris -->
				<h1><?= $chronic->chronicTitle ?>
				<?php if (isset($favoritesList)) {
					foreach ($favoritesList as $row1):
						if ($chronic->chronicId == $row1->chronicId) : ?>
							<i class="fa fa-heart coeur"></i>
							<?php endIf; ?>
							<?php endforeach;
					} ?>
				</h1>
				<p><?= strtoupper($chronic->countryName) ?> - <?= $chronic->categoryName ?></p>

				<div class="auteur">
					<p><strong>Par</strong></p>
					<p><img width="8%" src=<?= base_url('webroot/images/usersAvatar/' . $chronic->userAvatar) ?> alt="avatar"></p>
					<p><?= $chronic->userFirstName .' '. $chronic->userLastName ?> <strong>le</strong> <?= $chronic->chronicDate ?></p>
				</div>
				<p class="chronicContent"><?= $chronic->chronicContent ?></p>

			</div>
			<!-- formulaire de commentaire -->
			<form class="comment" action="" method="post">
				<div class="">
					<label for="comment">Ajouter un commentaire</label>
				</div>
				<textarea id="comment" name="comment" rows="3" cols="70"></textarea>
				<input type="hidden" id="parentCommentId" name="parentCommentId" value="0">
				<div class="btn-addChronicComment">
					<a id="addChronicComment" class="btn btn-lg btn-primary" href="<?= base_url('connexion/formulaire') ?>" data-chronicId="<?= $chronic->chronicId ?>" data-userId="<?php if(isset($_SESSION['userData']->userId)) {echo $_SESSION['userData']->userId;} ?>">Valider</a>
				</div>
			</form>
			<!-- liste des commentaires -->
			<div id="displayChronicComments" class="displayComments">

			</div>
			<div id="moreComments" class="moreComments"><a id="moreChronicComments" href="#">Plus de commentaires</a></div>
	</main>
</div>
