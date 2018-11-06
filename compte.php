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


	$title = "Param&egravetre";
	$style = "../styles/general.css";

	html_debut($title, $style);


	//Affichage de la partie supérieur
	header_log();

		echo '<p id="titre">Param&egravetres de mon compte </p>',
		'<img id="letrait" src="../images/trait.png" />',
		'<p id="expli">Cette page vous permet de modifier les informations relatives &agrave votre compte.</p>',
	'</header>';

	//Affichage de la partie gauche
	aff_aside_log($bd, $id, $pseudo, $nom, $photo, 4, 2);

	echo '<div id="param">';




		//INFORMATIONS PERSONNELLES
		echo '<p id="petitre">Informations personnelles</br></p>';

		if (! isset($_POST['btnValider1'])) {
			// On n'est dans un premier affichage de la page. On
			// intialise les zones de saisie.
			$nbErr1 = 0;



			$_POST['txtNom'] = '';
			$_POST['selNais_a'] = 1990;
			$_POST['selNais_m'] = $_POST['selNais_j'] = 1;
			$_POST['txtVille'] = $_POST['txtBio'] = '';

		} else {
			// On est dans la phase de soumission du formulaire on en
			// fait la vérification. Si aucune erreur n'est détectée,
			// cette fonction redirige la page sur le script protégé.
			$erreurs1 = _modification1();
			$nbErr1 = count($erreurs1);
		}


		if ($nbErr1 > 0) {
			echo '<strong>Les erreurs suivantes ont &eacute;t&eacute; d&eacute;tect&eacute;es</strong>';
			for ($i = 0; $i < $nbErr1; $i++) {
				echo '<br>', $erreurs1[$i];
			}
		}

		$sql="SELECT usVille, usBio , usMail, usWeb
			FROM users
			WHERE usID='$id'";
			$r = mysqli_query($bd, $sql) or bd_erreur($bd, $sql);
			$enr = mysqli_fetch_assoc($r);
		$ville = htmlentities($enr['usVille'],ENT_QUOTES,'ISO-8859-1');
		$bio = htmlentities($enr['usBio'],ENT_QUOTES,'ISO-8859-1');
		$mail = htmlentities($enr['usMail'],ENT_QUOTES,'ISO-8859-1');
		$web =htmlentities($enr['usWeb'],ENT_QUOTES,'ISO-8859-1');

		echo '<form method=POST action="compte.php">';
			echo '<table border=1 cellpadding=5>';
				echo from_ligne("<label>Nom</label>", "<input type=text name=txtNom value='$nom' size=37 />", "right", "");
				echo from_ligne("<label>Date de naissance</label>",  form_date('selNais', $jour, $mois, $annee), "right", "");
				echo from_ligne("<label>Ville</label>", "<input type=text name=txtVille value='$ville' size=37 />", "right", "");
				echo from_ligne("<label>Mini-bio</label>", "<textarea id=txtBio class=text name=txtBio rows=12 cols=53>$bio</textarea>", "right", "");

				echo from_ligne("" , "<input class=lesub type=submit name=btnValider1 value=Valider />", "", "right");
			echo '</table>';
		echo '</form>';






		//INFORMATIONS SUR VOTRE COMPTE CUITEUR
		echo '<p id="petitre">Informations sur votre compte Cuiteur</br></p>';


		if (! isset($_POST['btnValider2'])) {
			// On n'est dans un premier affichage de la page. On
			// intialise les zones de saisie.
			$nbErr2 = 0;



			 $_POST['txtMail'] = $_POST['txtWeb'] = '';

		} else {
			// On est dans la phase de soumission du formulaire on en
			// fait la vérification. Si aucune erreur n'est détectée,
			// cette fonction redirige la page sur le script protégé.
			$erreurs2 = _modification2();
			$nbErr2 = count($erreurs2);
		}


		if ($nbErr2 > 0) {
			echo '<strong>Les erreurs suivantes ont &eacute;t&eacute; d&eacute;tect&eacute;es</strong>';
			for ($i = 0; $i < $nbErr2; $i++) {
				echo '<br>', $erreurs2[$i];
			}
		}


		echo '<form method=POST action="compte.php">';
			echo '<table border=1 cellpadding=5>';
				echo from_ligne("<label>Adresse mail</label>", "<input type=text name=txtMail value='$mail' size=38 />", "right", "");
				echo from_ligne("<label>Site web</label>", "<input type=text name=txtWeb value='$web' size=38 />", "right", "");

				echo from_ligne("" , "<input class=lesub type=submit name=btnValider2 value=Valider />", "", "right");
			echo '</table>';
		echo '</form>';










		//PAARAMETRES DE VOTRE COMPTE CUITEUR
		echo '<p id="petitre">Param&egravetres de votre compte Cuiteur</br></p>';

		if (! isset($_POST['btnValider3'])) {
			// On n'est dans un premier affichage de la page. On
			// intialise les zones de saisie.
			$nbErr3 = 0;



			 $_POST['txtPasse'] = $_POST['txtVerif'] = '';

		} else {
			// On est dans la phase de soumission du formulaire on en
			// fait la vérification. Si aucune erreur n'est détectée,
			// cette fonction redirige la page sur le script protégé.
			$erreurs3 = _modification3();
			$nbErr3 = count($erreurs3);
		}


		if ($nbErr3 > 0) {
			echo '<strong>Les erreurs suivantes ont &eacute;t&eacute; d&eacute;tect&eacute;es</strong>';
			for ($i = 0; $i < $nbErr3; $i++) {
				echo '<br>', $erreurs3[$i];
			}
		}


		echo '<form method=POST action="compte.php" enctype="multipart/form-data">';
			echo '<table border=1 cellpadding=5>';
				echo from_ligne("<label>Changer le mot de passe</label>", "<input type=password name=txtPasse size=20 />", "right", "");
				echo from_ligne("<label>R&eacutepeter le mot de passe</label>", "<input type=password name=txtVerif size=20 />", "right", "");

				if ($photo == 1) {
					echo from_ligne("<label>Votre photo actuelle</label>", "<img src=../upload/$id.jpg>
													<p style=font-size:12px;>Images JPG carr&eacutee (mini 50x50px)</p>
													<input type=file name=leFichier id=leFichier>", "right", "");

					echo from_ligne("<label>Uiliser votre photo</label>", "<input type=radio name=choixPic id=radOui checked value=1>
																				<label for=radOui>oui</label>

																				<input type=radio name=choixPic id=radNon value=0>
																				<label for=radNon>non</label>", "right", "");
				}
				else {
					echo from_ligne("<label>Votre photo actuelle</label>", "<img src=../images/anonyme.jpg>
													<p style=font-size:12px;>Images JPG carr&eacutee (mini 50x50px)</p>
													<input type=file name=leFichier id=leFichier>", "right", "");

					echo from_ligne("<label>Uiliser votre photo</label>", "<input type=radio name=choixPic id=radOui value=1>
																				<label for=radOui>oui</label>

																				<input type=radio name=choixPic id=radNon checked value=0>
																				<label for=radNon>non</label>", "right", "");
				}



				echo from_ligne("" , "<input class=lesub type=submit name=btnValider3 value=Valider />", "", "right");
			echo '</table>';
		echo '</form>';

	echo '</div>';




	/**
	* Fonction pour les modifications de la première partie de compte.php
	*
	* Ici on va vérifier si le nom est changé. On pourra éventuellement
	* mettre à jour la date de naissance et vérifie si elle est
	* conforme à une date.
	*
	*
	* @return 	array		$erreurs		Retour des erreurs éventuelles
	*/




	function _modification1 () {
		global $id;
		global $bd;
		//-----------------------------------------------------
		// Vérification des zones
		//-----------------------------------------------------
		$erreurs = array();

		$txtVille = trim($_POST['txtVille']);
		$txtBio = trim($_POST['txtBio']);


		// Vérification du nom
		$txtNom = trim($_POST['txtNom']);
		if ($txtNom == '') {
			$erreurs[] = 'Le nom est obligatoire';
		}


		// Vérification de la date
		$selJour = (int) $_POST['selNais_j'];
		$selMois = (int) $_POST['selNais_m'];
		$selAnnee = (int) $_POST['selNais_a'];
		if (!checkdate($selMois, $selJour, $selAnnee)) {
			$erreurs[] = 'La date de naissance n\'est pas valide';
		}

		// Si il y a des erreurs, la fonction renvoie le tableau d'erreurs
		if (count($erreurs) > 0) {
			return $erreurs;		// RETURN : des erreurs ont été détectées
		}


		//-----------------------------------------------------
		// Insertion d'un nouvel enregistrement dans la base de données
		//-----------------------------------------------------
		$txtNom = mysqli_real_escape_string($bd, $txtNom);
		$usDateNaissance = ($selAnnee * 10000) + ($selMois * 100) + $selJour;
		$txtVille = mysqli_real_escape_string($bd, $txtVille);
		$txtBio = mysqli_real_escape_string($bd, $txtBio);


		$S = "UPDATE users SET
					usNom = '$txtNom',
					usDateNaissance = $usDateNaissance,
					usVille = '$txtVille',
					usBio = '$txtBio'
				WHERE usID = $id";




		mysqli_query($bd, $S) or bd_erreur($bd, $S);


		//-----------------------------------------------------
		// Ouverture de la session et redirection vers la page protegée
		//-----------------------------------------------------


		$_SESSION['usNom'] = $txtNom;
		$_SESSION['usVille'] = $txtVille;
		$_SESSION['usBio'] = $txtBio;
		$_SESSION['usDateNaissance'] = $usDateNaissance;


		header ('location: compte.php');

	}

	/**
	* Fonction pour les modifications de la partie du milieu de compte.php
	*
	* Ici on vérifie si l'adresse mail est changé et si elle correspond bien
	* à une adresse mail composé d'un '@' et d'un '.', on vérifie aussi que
	* si un site web a été rentré celui ci soit également conforme à un site
	* web de base.
	*
	*
	* @return 	array		$erreurs		Retour des erreurs éventuelles
	*/

	function _modification2 () {
		global $id;
		global $bd;
		//-----------------------------------------------------
		// Vérification des zones
		//-----------------------------------------------------
		$erreurs = array();

		$txtMail = trim($_POST['txtMail']);
		$txtWeb = trim($_POST['txtWeb']);


		// Vérification du mail
		if ($txtMail == '') {
			$erreurs[] = 'L\'adresse mail est obligatoire';
		}
		elseif (strpos($txtMail, '@') === FALSE || strpos($txtMail, '.') === FALSE) {
			$erreurs[] = 'L\'adresse mail n\'est pas valide';
		}



		if ($txtWeb != '') {
			if (strpos($txtWeb, '.') === FALSE || strncmp($txtWeb, 'http://', 7)) {
				$erreurs[] = 'Le site web n\'est pas valide';
			}
		}


		// Si il y a des erreurs, la fonction renvoie le tableau d'erreurs
		if (count($erreurs) > 0) {
			return $erreurs;		// RETURN : des erreurs ont été détectées
		}


		//-----------------------------------------------------
		// Insertion d'un nouvel enregistrement dans la base de données
		//-----------------------------------------------------
		$txtMail = mysqli_real_escape_string($bd, $txtMail);
		$txtWeb = mysqli_real_escape_string($bd, $txtWeb);


		$S = "UPDATE users SET
					usMail = '$txtMail',
					usWeb = '$txtWeb'
				WHERE usID = $id";




		mysqli_query($bd, $S) or bd_erreur($bd, $S);


		//-----------------------------------------------------
		// Ouverture de la session et redirection vers la page protegée
		//-----------------------------------------------------


		$_SESSION['usMail'] = $txtMail;
		$_SESSION['usWeb'] = $txtWeb;



		header ('location: compte.php');

	}

	/**
	* Fonction pour les modifications de la dernière partie de compte.php
	*
	* Ici on va vérifier si un mot de passe veut être changé. Si il veut
	* être changé on le changera en vérifiant la similarité des deux password rentrés.
	* On pourra aussi vérifier l'image, la changer, décider de la montrer ou non.
	*
	*
	* @return 	array		$erreurs		Retour des erreurs éventuelles
	*/

	function _modification3 () {
		global $id;
		global $bd;
		//-----------------------------------------------------
		// Vérification des zones
		//-----------------------------------------------------
		$erreurs = array();

		$txtPasse = trim($_POST['txtPasse']);
		$txtVerif = trim($_POST['txtVerif']);



		// Vérification du passe
		if ($txtPasse != '' && $txtVerif != '' && $txtPasse != $txtVerif) {
			$erreurs[] = 'Le mot de passe est diff&eacute;rent dans les 2 zones';
		}
		if ($_POST['choixPic'] < 0  || $_POST['choixPic'] > 1)  {
			header('location: deconnexion.php');
		}
		//Si l'utilisateur veut utiliser sa photo alors qu'il en a même pas cet idiot
		if ($_POST['choixPic'] == 1 && !file_exists(realpath('.').'/../upload/'.$_SESSION['usID'].'.jpg')) {
			$erreurs[] = 'Il faut avoir upload une image pour l\'utiliser';
		}

		$usAvecPhoto = (int) $_POST['choixPic'];

		$extention = array(".jpg");

		//Verification de l'image
		if (is_uploaded_file($_FILES['leFichier']['tmp_name'])) {
			$tmp_name = $_FILES['leFichier']['tmp_name'];
			$nom = $_FILES['leFichier']['name'];
			$file_ext = substr($nom, strrpos($nom, '.'));

			$img=getimagesize($_FILES['leFichier']['tmp_name']);
			$x = $img[0];
			$y = $img[1];


			//Si la photo n'est pas une .jpg
			if (!in_array($file_ext, $extention)) {
				$erreurs[] = 'L\'extention n\est pas la bonne (.jpg seulement)';
			}
			//Si la photo n'est pas un carré
			if ($x != $y) {
				$erreurs[] ='Le format n\'est pas carr&eacute;';
			}

			$Dest = realpath('.').'/../upload/'.$_SESSION['usID'].'.jpg';
			$final_name = "$id.jpg";
			//$leupload = move_uploaded_file($_FILES['leFichier']['tmp_name'], $move . $_FILES["leFichier"]['name']);

			$new_x = 50;
			$new_y = 50;

			$destinatio = imagecreatetruecolor($new_x, $new_y);
			$source = imagecreatefromstring(file_get_contents($_FILES['leFichier']['tmp_name']));

			imagecopyresampled($source, $source, 0, 0, 0, 0, $new_x, $new_y, $x, $y);

			if($_FILES['leFichier']['error'] === 0
				&& is_uploaded_file($_FILES['leFichier']['tmp_name'])
				&& @move_uploaded_file($_FILES['leFichier']['tmp_name'], $Dest)) {


			}

		}

		// Si il y a des erreurs, la fonction renvoie le tableau d'erreurs
		if (count($erreurs) > 0) {
			return $erreurs;		// RETURN : des erreurs ont été détectées
		}


		//-----------------------------------------------------
		// Insertion d'un nouvel enregistrement dans la base de données
		//-----------------------------------------------------
		$txtPasse = mysqli_real_escape_string($bd, $txtPasse);

		if ($txtPasse != '') {
			$S = "UPDATE users SET
						usPasse = '$txtPasse',
						usAvecPhoto = '$usAvecPhoto'
					WHERE usID = $id";
		}
		else {
			$S = "UPDATE users SET
						usAvecPhoto = '$usAvecPhoto'
					WHERE usID = $id";
		}


		mysqli_query($bd, $S) or bd_erreur($bd, $S);

		$_SESSION['usAvecPhoto'] = $usAvecPhoto;

		header('location: compte.php');

	}




	aff_footer();
	mysqli_close($bd);
	html_fin();
	ob_end_flush();
?>
