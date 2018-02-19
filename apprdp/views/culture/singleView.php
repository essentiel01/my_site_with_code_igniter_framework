<div class="container">
	<main class="mainWrapper">
		<?php foreach ($post as $row) : ?>
				<a href="#" class="btn btn-success btn-lg " role="button" aria-pressed="true">Ajouter à mes favoris</a>
				<p>Pays: <?= $row->countryName ?></p>
				<p>Rubrique: <?= $row->categoryName ?></p>
				<h2><?= $row->postTitle ?></h2>
				<p>Publié par:</p>
				<p><img width="8%" src=<?= '/myProjet/webroot/images/usersAvatar/' . $row->writerAvatar ?> alt="avatar"></p>
				<p><?= $row->writerFirstName." ".$row->writerLastName ?> le <?= $row->postPublishingDate ?></p>
				<p><?= $row->postContent ?></p>
				<h2>Sources de la revue</h2>
				<p><?= $row->postSource ?></p>
				<h3>Ajouter un commentaire</h3>
				<form class="" action="" method="post">
					<textarea name="name" rows="2" cols="70"></textarea>
					<input class="btn-primary" type="submit" value="Commenter">
				</form>
			<?php endforeach ?>
	</main><!--
	--><aside class="asideWrapper">

		<!--  slide -->
		<section class="slide">
			<h2>Slide</h2>
		</section>
		<!-- partenaires -->
		<section class="">
			<h2>Les médias partenaires</h2>
		</section>
		<!-- /partenaires -->
	</aside>
</div>