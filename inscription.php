<?php

	ob_start();

	require("bibli_cuiteur.php");
	require("bibli_generale.php");

	$title = "Inscription";
	$style = "../styles/index3.css";

	$bd = bd_connection();

	html_debut($title, $style);

	//Affichage de la partie supérieur
	echo '<header>',
		'<p id="titre">Inscription </p>',
		'<img id="letrait" src="../images/trait.png" />',
		'<p id="expli">Pour vous inscrire il suffit de :</p>',
	'</header>';

	echo '<aside>',
	'</aside>';

	echo '<div id="connection">';

	if(!isset($_POST['btnValider'])) {
		_aff_form();
		$_POST['txtPseudo'] = $_POST['txtPasse'] = $_POST['txtVerif'] = '';
		$_POST['txtNom'] = $_POST['txtMail'] = '';
	}


	/**
	* La fonction qui va nous afficher le formulaire
	* sous forme de tableau
	*/

	function _aff_form() {

			echo '<form method=POST action="inscription.php">',
				'<table border=1 cellpadding=5>';
					echo from_ligne("<label>Choisir un pseudo</label>", "<input type=text name=txtPseudo size=20 />", "right", "");
					echo from_ligne("<label>Choisir un mot de passe</label>", "<input type=password name=txtPasse size=20 />", "right", "");
					echo from_ligne("<label>R&eacute;peter le mot de passe</label>", "<input type=password name=txtVerif size=20 />", "right", "");
					echo from_ligne("<label>Indiquer votre nom</label>", "<input type=text name=txtNom size=30/>", "right", "");
					echo from_ligne("<label>Donner une adresse email</label>", "<input type=text name=txtMail size=30/>", "right", "");



					echo from_ligne("" , "<input class=lesub type=submit name=btnValider value=Je&nbsp;m'inscris />", "", "right");

				echo '</table>',
			'</form>';

	}


	/**
	* La fonction locale new_user va faire les vérifications
	* pour savoir si tout a bien été fait pour l'inscription.
	* Si ce n'est pas le cas, on retourne les erreurs et l'utilisateur
	* devra recommencer son inscription.
	*
	*
	*
	* @return	array		$array()	les erreurs
	*/

	function _new_user() {

		global $bd;

		$pseudo = trim($_POST['txtPseudo']);
		$pass = trim($_POST['txtPasse']);
		$passVerif = trim($_POST['txtVerif']);
		$nom = trim($_POST['txtNom']);
		$mail = trim($_POST['txtMail']);



		$S = "SELECT COUNT(*) as count
			FROM users
			WHERE usPseudo ='$pseudo'";

		$R = mysqli_query($bd, $S) or bd_erreur($bd, $S);
		$D = mysqli_fetch_assoc($R);

		$count = htmlentities($D['count'], ENT_QUOTES, 'ISO-8859-1');

		$erreur = array();

		if ($count != 0)
			$erreur[] = "Le pseudo doit etre change";

		else if (strlen($pseudo) < 4 || strlen($pseudo) > 30) {
			$erreur[] = "Le pseudo " . $pseudo . " doit avoir de 4 à 30 caractères";
		}

		if ($pass == '')
			$erreur[] = "Le mot de passe est obligatoire";

		if ($pass != $passVerif)
			$erreur[] = "Le mot de passe est different dans les 2 zones";

		if ($nom == '')
			$erreur[] = "Le nom est obligatoire";

		if ((strpos($mail, "@") === false && strpos($mail, ".") === false) || ($mail == ''))
			$erreur[] = "L'adresse mail est obligatoire / L'adresse mail n'est pas valide";


			return $erreur;
	}

	//Si l'utilisateur a tenté de s'enregistrer
	if(isset($_POST['btnValider'])) {

		$bd = bd_connection();


		$pseudo = trim($_POST['txtPseudo']);
		$pass = trim($_POST['txtPasse']);
		$passVerif = trim($_POST['txtVerif']);
		$nom = trim($_POST['txtNom']);
		$mail = trim($_POST['txtMail']);


		$erreur = _new_user($_POST);

		//Si il n'y aucune erreur
		if (count($erreur) == 0) {
			$dateIns=date('Ymd');
			$dateNais=0*10000+0*100+0;

			$nom=mysqli_real_escape_string($bd, $nom);
			$pseudo=mysqli_real_escape_string($bd, $pseudo);
			$pass=mysqli_real_escape_string($bd, $pass);
			$mail=mysqli_real_escape_string($bd, $mail);
			$ville='';
			$photo=0;

			$S = "INSERT INTO users
				(usNom, usPseudo, usMail, usPasse, usDateNaissance, usDateInscription, usVille, usAvecPhoto)
					VALUES
				('$nom','$pseudo','$mail','$pass','$dateNais','$dateIns','$ville','$photo')";

			$r = mysqli_query($bd, $S) or bd_erreur($bd, $S);
			$ID = mysqli_insert_id($bd);

			//On lance la session
			session_start();

			$_SESSION['usID'] = $ID;
			$_SESSION['usNom'] = $nom;
			$_SESSION['usPseudo'] = $pseudo;
			$_SESSION['usAvecPhoto'] = $photo;


			header ('location: compte.php');
			exit();




		}

		//Si il y'a au moins une erreur on affiche
		else {
				$erreur = array();
				$erreur = _new_user($_POST);
				$taille = count($erreur);

				echo '<h3>Les erreurs suivantes ont ete detectees :</h3>';
					echo '<ul>';
						for ($i = 0 ; $i < $taille ; $i++)
							echo '<li>', $erreur[$i], '</li>';
					echo '</ul>';



					$_POST['txtPseudo'] = '';
					$_POST['txtPasse'] = '';
					$_POST['txtVerif'] = '';
					$_POST['txtNom'] = '';
					$_POST['txtMail'] = '';


				echo '</br>';
				echo _aff_form();

		}

	}

	echo '</div>';

	aff_footer();
	mysqli_close($bd);
	html_fin();


?>
