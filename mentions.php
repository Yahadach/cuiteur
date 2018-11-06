<?php

	ob_start();

	require("bibli_cuiteur.php");
	require("bibli_generale.php");

	$bd = bd_connection();

	session_start();

	if(!isset($_SESSION['usID'])) {
		header('location: ../index.php');
		exit();
	}

	$id = $_SESSION['usID'];
	$pseudo = $_SESSION['usPseudo'];
	$nom = $_SESSION['usNom'];
	$photo = $_SESSION['usAvecPhoto'];

	$jour = 0;
	$mois = 0;
	$annee = 0;

	$profil_id = decrypt_url($_GET['profil']);
	$profil_pseudo = decrypt_url($_GET['pseudo']);
	$profil_nom = decrypt_url($_GET['nom']);



	$title = "Mentions";
	$style = "../styles/general.css";

	html_debut($title, $style);


	$blabla = blabla_nb($bd, $profil_id);
	$mentions = mention_nb($bd, $profil_id);
	$abonnes = abonne_nb($bd, $profil_id);
	$abonnements = abonnement_nb($bd, $profil_id);
	$photo_u = photo($bd, $profil_id);



	//Affichage de la partie supérieur
	header_log();

	echo "<p id=titre>Les mentions de $profil_pseudo </p>",
		'<img id="letrait" src="../images/trait.png" />';
		if ($photo_u == 1) {
			echo '<p id="profil"><img style="margin-top:-5px;" src="../upload/', $profil_id, '.jpg" class="imgAuteur">', aff_user($profil_id, $profil_pseudo, $profil_nom, "");
		}
		else {
			echo '<p id="profil"><img style="margin-top:-5px;" src="../images/anonyme.jpg" class="imgAuteur">', aff_user($profil_id, $profil_pseudo, $profil_nom, "");
		}
		echo '</br>';
			aff_lien("blablas", $profil_id, $profil_pseudo, $profil_nom, $blabla, "blablas", "-");
			aff_lien("mentions", $profil_id, $profil_pseudo, $profil_nom, $mentions, "mentions", "-");
			aff_lien("abonnes", $profil_id, $profil_pseudo, $profil_nom, $abonnes, "abonn&eacute;s", "-");
			aff_lien("abonnements", $profil_id, $profil_pseudo, $profil_nom, $abonnements, "abonnements");
		echo '</p> ',
	'</header>';


	//Affichage de la partie supérieur
	aff_aside_log($bd, $id, $pseudo, $nom, $photo, 4, 2);


	echo '<div id="mention">',
		'</br></br>';

			$sql="SELECT usID, usPseudo, usNom, usAvecPhoto, blTexte, blDate, blHeure, blID
					FROM blablas, users, mentions
					WHERE meIDUser='$profil_id'
					AND meIDBlabla=blID
					AND blIDAuteur = usID";

		$r = mysqli_query($bd, $sql) or bd_erreur($bd, $sql);

		$id_encrypted = encrypt_url($profil_id);
		$pseudo_encrypted = encrypt_url($profil_pseudo);
		$nom_encrypted = encrypt_url($profil_nom);


		$nb_blablas=4;
		if (isset($_GET['plusBlbl'])){
			$nb_blablas=$_GET['plusBlbl'];
		}
		$aff_plus_blbl=aff_blablas($r, $nb_blablas, $id, $bd);
		if($aff_plus_blbl===1){
			echo '<li id="bcPlus">
				<a href="mentions.php?profil=', $id_encrypted, '&amp;pseudo=',$pseudo_encrypted,'&amp;nom=', $nom_encrypted, '&amp;plusBlbl=', $nb_blablas+4, '" title="Plus de blablas"><strong>Plus de blablas</strong></a>
			</li>';
		}
	echo '</ul>';



	echo '</div>';


	aff_footer();
	mysqli_close($bd);
	html_fin();
	ob_end_flush();

?>
