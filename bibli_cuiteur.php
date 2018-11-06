<?php
	//Param�tre pour la connection � la base de donn�e
	require('bib_params.php');

	/**
	* Permet de rendre une date claire
	*
	* Fonction qui prend en paramètre un integer
	* et qui retourne la date dans un format plus jolie
	* exemple : "20140224" donnera "24 Fevrier 2014"
	*
	* @param	integer		$data 	Date au format AAAAMMDD

	*
	* @return 	string 	$date	Valeur du paramètre ou FALSE
	*/

	function amj_clair($data) {
		$annee = (int) substr($data, 0, 4);
		$mois = (int) substr($data, 4, 2);
		$jour = (int) substr($data, 6, 2);

		$ma = array('Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre');

		$date = $jour. ' '. $ma[$mois-1]. ' '. $annee;
		return $date;
	}

	/**
	* Ecrit les premières lignes nécessaires pour le code html
	*
	* Cette fonction va nous permettre de ne pas
	* écrire à chaque fois les lignes de code html
	* en pouvant choisir le titre et le chemin du fichier CSS
	*
	* @param	string 		$title		Titre de la page affiché en haut
	* @param 	string		$style		Chemin vers le fichier CSS
	*
	*/

	function html_debut($title, $style) {
		echo '<!DOCTYPE html>
		<html>
			<head>
				<meta charset="iso-8859-1">
				<title>', $title, ' </title>
				<link rel="stylesheet" href="',$style,'" type="text/css">
				<link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon">
			</head>

			<body>
				<div id="bcPage">';
	}

	/**
	* Ecrit les dernières lignes de code html
	*
	* Cette fonction va nous permettre de ne pas
	* écrire les lignes de code html à chaque fois
	*
	*
	*/

	function html_fin() {

		echo '
				</div>
			</body>
		</html>';
	}

	/**
	 * Connexion à la base de données.
	 *
	 * @return resource	connecteur à la base de données
	 */

	function bd_connection() {
		$bd = mysqli_connect(BD_SERVEUR, BD_USER, BD_PASS, BD_NOM);

		if ($bd !== FALSE) {
			return $bd;     // Sortie connexion OK
	  	}

		// Erreur de connexion
		// Collecte des informations facilitant le debugage
		$msg = '<h4>Erreur de connexion base MySQL</h4>'
		.'<div style="margin: 20px auto; width: 350px;">'
			.'BD_SERVEUR : '.BD_SERVEUR
			.'<br>BD_USER : '.BD_USER
			.'<br>BD_PASS : '.BD_PASS
			.'<br>BD_NOM : '.BD_NOM
			.'<p>Erreur MySQL numéro : '.mysqli_connect_errno($bd)
			.'<br>'.mysqli_connect_error($bd)
		.'</div>';

		bd_ErreurExit($msg);
	}

	/**
	* Affichage des blablas d'un utilisateur qui s'est loggé
	*
	* La fonction ici affiche les blablas en prenant en compte
	* le nombre de blabla à afficher.
	*
	* @param 	resource	$r			Requête pour afficher les blablas
	* @param 	integer		$nb			Le nombre de blablas à afficher
	* @param 	integer 	$user_id		L'ID de l'utilisateur actuel
	* @param 	resource	$bd			Connecteur sur la bd ouverte
	*
	*
	* @return	integer					Retourne si la requête est vide
	*/

	function aff_blablas($r, $nb, $user_id, $bd){
		$i = 1;
		echo '<ul id="bcMessages">';
		while (($enr = mysqli_fetch_assoc($r)) &&  ($i<=$nb)) {

				$usPseudo=htmlentities($enr['usPseudo'],ENT_QUOTES,'ISO-8859-1');
				$temps=calcule_temps(htmlentities($enr['blDate'],ENT_QUOTES,'ISO-8859-1'), htmlentities($enr['blHeure'],ENT_QUOTES,'ISO-8859-1'));
				$id=htmlentities($enr['usID'],ENT_QUOTES,'ISO-8859-1');
				$blbl=check_tag_dist(htmlentities($enr['blTexte'],ENT_QUOTES,'ISO-8859-1'), $bd);

				$photo = htmlentities($enr['usAvecPhoto'],ENT_QUOTES,'ISO-8859-1');
				if ($photo == 1) {
					echo '<li><img src="../upload/', $id,'.jpg" class="imgAuteur">';
				}
				else {
					echo '<li><img src="../images/anonyme.jpg" class="imgAuteur">';
				}

				aff_user($id, htmlentities($enr['usPseudo'],ENT_QUOTES,'ISO-8859-1'), htmlentities($enr['usNom'],ENT_QUOTES,'ISO-8859-1'), "bold");

				echo '<br>',$blbl,
				'<p class="nom_finMessage">Il y a  ', $temps;
				if($user_id==$id){
					echo ' <a href="?supprimer=', htmlentities($enr['blID'],ENT_QUOTES,'ISO-8859-1'), '">Supprimer</a><p></li>';
				}else{
					echo '<a href=?repondre=',$usPseudo,'>R&eacute;pondre</a> <a href=?recuiter=',htmlentities($enr['blID'],ENT_QUOTES,'ISO-8859-1'),'>Recuiter</a><p></li>';
				}
			$i++;
		}
		if(empty($enr)){
			return 0;
		}else{
			return 1;
		}
	}

	/**
	 * Gestion d'une erreur de requête base de données.
	 *
	 * @param	resource	$bd		Connecteur sur la bd ouverte
	 * @param	string		$sql	requête SQL provoquant l'erreur
	 */

	function bd_erreur($bd, $sql) {
		$errNum = mysqli_errno($bd);
		$errTxt = mysqli_error($bd);

		// Collecte des informations facilitant le debugage
		$msg = '<h4>Erreur de requête</h4>'
			."<pre><b>Erreur mysql :</b> $errNum"
			."<br> $errTxt"
			."<br><br><b>Requête :</b><br> $sql"
			.'<br><br><b>Pile des appels de fonction</b>';

		// Récupération de la pile des appels de fonction
		$msg .= '<table border="1" cellspacing="0" cellpadding="2">'
			.'<tr><td>Fonction</td><td>Appelée ligne</td>'
			.'<td>Fichier</td></tr>';

		$appels = debug_backtrace();
		for ($i = 0, $iMax = count($appels); $i < $iMax; $i++) {
			$msg .= '<tr align="center"><td>'
				.$appels[$i]['function'].'</td><td>'
				.$appels[$i]['line'].'</td><td>'
				.$appels[$i]['file'].'</td></tr>';
		}

		$msg .= '</table></pre>';

		bd_erreurExit($msg);
	}

	/**
	 * Arrêt du script si erreur base de données.
	 * Affichage d'un message d'erreur si on est en phase de
	 * développement, sinon stockage dans un fichier log.
	 *
	 * @param string	$msg	Message affiché ou stocké.
	 */

	function bd_erreurExit($msg) {
		ob_end_clean();		// Supression de tout ce qui
						// a pu être déja généré

		echo '<!DOCTYPE html><html><head><meta charset="ISO-8859-1"><title>',
				'Erreur base de données</title></head><body>',
				$msg,
				'</body></html>';
		exit();
	}

	/**
	* Affichage de toute la partie gauche quand il y'a eu un log
	*
	* Cette fonction affiche la partie gauche qui comprend
	* la partie Utilisateur, la partie Tendances,
	* ainsi que la partie Suggestions
	*
	* @param	resource	$bd			Connecteur sur la bd ouverte
	* @param 	integer		$id			L'ID de l'utilisateur actuel
	* @param 	string 		$pseudo		Pseudo de l'utilisateur actuel
	* @param 	string 		$nom		Nom de l'utilisateur actuel
	* @param	integer		$nbTen		Nombre de tendances à afficher
	* @param	integer		$nbSug		Nombre de suggestions à afficher
	*
	*/


	function aff_aside_log($bd, $id, $pseudo, $nom, $photo, $nbTen=4, $nbSug=2 ){

	/********************************************************************************************************************
	*								       		Utilisateur								         					  	*
	*********************************************************************************************************************/
		echo '<aside><h3>Utilisateur</h3><ul>';
		$sql = "SELECT usNom, COUNT(*) AS nbBlbl
			FROM users, blablas
			WHERE usID=$id
			AND usId=blIDAuteur";
		$r = mysqli_query($bd, $sql) or bd_erreur($bd, $sql);
		$enr = mysqli_fetch_assoc($r);

		//Photo avec son nom.
		echo '<li>';
				if($photo == 1) {
					echo '<img src="../upload/', $id,'.jpg" >';
				}
				else {
					echo '<img src="../images/anonyme.jpg" >';
				}
				aff_user($id, $pseudo, $nom, "bold");
		echo '</li>';

		$nombre = htmlentities($enr['nbBlbl'],ENT_QUOTES,'ISO-8859-1');

		//Blabla de l'utilisateur
		echo '<li>';
			aff_lien("blablas", $id, $pseudo, $nom, $nombre, "blablas", "");
		echo '</li>';

		$sql="SELECT COUNT(*) AS nbAbment
			FROM estabonne
			WHERE estabonne.eaIDUser=$id";
		$r = mysqli_query($bd, $sql) or bd_erreur($bd, $sql);
		$enr = mysqli_fetch_assoc($r);

		$nombre = htmlentities($enr['nbAbment'],ENT_QUOTES,'ISO-8859-1');

		//Abonnements de l'utilisateur
		echo '<li>';
			aff_lien("abonnements", $id, $pseudo, $nom, $nombre, "abonnements", "");
		echo '</li>';


		$sql="SELECT COUNT(*) AS nbAbne
			FROM estabonne
			WHERE estabonne.eaIDAbonne=$id";
		$r = mysqli_query($bd, $sql) or bd_erreur($bd, $sql);
		$enr = mysqli_fetch_assoc($r);

		$nombre = htmlentities($enr['nbAbne'],ENT_QUOTES,'ISO-8859-1');

		//Abonnés de l'utilisateur
		echo '<li>';
			aff_lien("abonnes", $id, $pseudo, $nom, $nombre, "abonn&eacute;s", "");
		echo '</li></ul>';


	/********************************************************************************************************************
	*								       		Tendence et Suggesstion													*
	*********************************************************************************************************************/

	$sql='SELECT taID, COUNT(taIDBlabla) AS nbTags
		FROM tags
		GROUP BY taID
		ORDER BY nbTags DESC';

	$r = mysqli_query($bd, $sql) or bd_erreur($bd, $sql);
	aff_tendence($r, $nbTen);

	$sql="SELECT DISTINCT usID, usPseudo, usNom, usAvecPhoto
		FROM users, estabonne
		WHERE usID = eaIDAbonne
		AND eaIDUser <> '$id'
		AND eaIDAbonne <> '$id'
		AND eaIDUser = ANY
			(SELECT DISTINCT eaIDAbonne
			FROM estabonne
			WHERE eaIDUser='$id')";
	$r = mysqli_query($bd, $sql) or bd_erreur($bd, $sql);
	aff_sugg($r, $id, $pseudo, $nom, $nbSug);

	echo '</aside>';
}

	/**
	*Cette fonction � l'aide preg_match_all remplace tous les tags et mentions avec les liens.
	*Pour les mentions on cherche id et pseudo et nom pour pouvoir acceder � sa page directe
	*
	* @param	string 		$str			le texte blablas
	* @param 	resource		$bd			Connecteur sur la bd ouverte
	*
	* @return 	string					le texte  blablas  avec les lien necessaire
	*/

	function check_tag_dist($str, $bd){
		$tags=tag($str);
		$size=count($tags);
		for ($z = 0; $z < $size; $z++){
				$new_str= "<a href=tendances.php?tend=" . substr($tags[$z][0], 1) . " title=Voir>".$tags[$z][0]."</a>";
				$str=str_replace($tags[$z][0], $new_str, $str);
		}

		$mentions=dist($str);
		$nb=count($mentions);
	   	for($i=0; $i<$nb; $i++){
	   		for($j=0; $j<count($mentions[$i]); $j++){

				$lepseudo = substr($mentions[$i][0], 1);
				$sql="SELECT usID, usNom
							FROM users
							WHERE usPseudo='$lepseudo'";
				$r = mysqli_query($bd, $sql) or bd_erreur($bd, $sql);
				$enr = mysqli_fetch_assoc($r);
				$nom = htmlentities($enr['usNom'],ENT_QUOTES,'ISO-8859-1');
				$ID = htmlentities($enr['usID'],ENT_QUOTES,'ISO-8859-1');

				$id_encrypted = encrypt_url($ID);
				$pseudo_encrypted = encrypt_url($lepseudo);
				$nom_encrypted = encrypt_url($nom);
				$new_str= "<a href=utilisateur.php?profil=".$id_encrypted."&amp;pseudo=".$pseudo_encrypted."&amp;nom=".$nom_encrypted.">".$mentions[$i][$j]."</a> ";
				$str=str_replace($mentions[$i][0], $new_str, $str);

				//echo $mentions[$i][$j];
			}
		}
		return $str;
	}

	/**
	*Cette fonction � l'aide preg_match_all choisi tous les tags.
	*#\D on dit que apr�s # il faut pas avoir les valeurs numeriques,
	*&*#*[0-9]*; pour chopisir les charactere speciaux, w* 0 ou plusieur char
	*
	* @param	string 		$str			le texte blablas
	*@return	array			$matches		les tags
	*/

	function tag($str){
		preg_match_all('/#\D(\w*&*#*[0-9]*;*)*\w*/', $str, $matches, PREG_SET_ORDER);
		return $matches;
	}

	/**
	*Cette fonction � l'aide preg_match_all choisi tous les utilisateurs
	*le mot commence @, (.+?) 1 ou plusieur char
	*
	* @param	string 		$str			le texte blablas
	*@return	array			$matches		les utilisateurs mentionn�s
	*/

	function dist($str){
		preg_match_all('/@\w*/', $str, $matches, PREG_SET_ORDER);
		return $matches;
	}

	/**
	* Vérification d'une session. Redirection sur la page
	* d'inscription si la session n'est pas ouverte.
	*
	*/

	function verifie_session(){
		if($_SESSION['id']==0 || $_SESSION['pseudo']==NULL){
			header ('location: inscription.php');
			exit();
		}
	}

	/**
	* Calcule du temps
	*
	* On calcule le temps quand blablas �tait ajout�.
	* Cette fonction prend date et heure d'ajout et trouve la difference en secondes avec le temps actuel � l'aide strtotime
	*return la  valeur en ann�es, mois ou jours etc.
	*
	* @param	string		$date		date quand blablas �tait ajout�
	* @param 	string		$time		heure quand blablas �tait ajout�
	*
	* @return 	string					Le temps à afficher dans le blabla
	*/


	function calcule_temps($date, $time){
		date_default_timezone_set('Europe/Paris');
		$date = date_create_from_format('Ymd', $date);
		$now=date('Y-m-d H:i:s');
		$date=date_format($date, 'Y-m-d').' '.$time;
		$diff = abs(strtotime($now) - strtotime($date));

		$years=floor($diff/ (60*60*24*365));
		if($years>0){
			return  $years.'  ann&eacute;e';
		}
		$months=floor($diff/(60*60*24*30));
		if($months>0){
			return  $months.'  mois';;
		}

		$days=floor($diff/(60*60*24));
		if($days>0){
			return  $days.'  jours';;
		}

		$hours=floor($diff/(60*60));
		if($hours>0){
			return  'environ '.$hours.'  heures';
		}

		$mins=floor($diff/60);
		if($mins>0){
			return  'environ '.$mins.'  minutes';
		}
		$secs=floor($diff%60);
		return  'environ '.$secs.'  minutes';
	}

	/**
	* Fonction qui va afficher le header sur
	* quasiment toutes les pages du site
	*
	*/

	function header_log(){
		echo '<header>
			<a id="btnDeconnexion" href="deconnexion.php" title="Se d&eacute;connecter de cuiteur"></a>
			<a id="btnHome" href="cuiteur.php" title="Ma page d\'accueil"></a>
			<a id="btnCherche" href="recherche.php" title="Rechercher des personnes &agrave suivre"></a>
			<a id="btnConfig" href="compte.php" title="Modifier mes informations personnelles"></a>';
	}

	/**
	* Fonction qui va afficher le fotter sur
	* toutes les pages du site avec les faux liens
	*
	*/

	function aff_footer(){
		echo '<footer>
				<ul id="bcPied">
					<li><a href="../../index.html">A propos</a></li>
					<li><a href="../../index.html">Publicit&eacute;</a></li>
					<li><a href="../../index.html">Patati</a></li>
					<li><a href="../../index.html">Aide</a></li>
					<li><a href="../../index.html">Patata</a></li>
					<li><a href="../../index.html">Stages</a></li>
					<li><a href="../../index.html">Emplois</a></li>
					<li><a href="../../index.html">Confidentialit&eacute;</a></li>
				</ul>
			</footer>';

	}

	/**
	* Affichage des suggestions dans le aside
	*
	* On va afficher toutes les suggestions disponible
	* pour un utilisateur donné avec le nombre max à
	* afficher
	*
	* @param 	resource	$r			Requête pour afficher les suggestions
	* @param 	integer		$id			L'ID de l'utilisateur actuel
	* @param 	string		$pseudo		Le pseudo de l'utilisateur actuel
	* @param 	string		$nom		Le nom de l'utilisateur actuel
	* @param 	integer		$nb			Le nombre de suggestion à afficher
	*
	*/

	function aff_sugg($r, $id, $pseudo, $nom, $nb){
		$i = 1;
		echo '<h3>Suggestions</h3><ul>';

		while (($enr = mysqli_fetch_assoc($r)) && $i<=$nb ) {
				if (htmlentities($enr['usAvecPhoto'],ENT_QUOTES,'ISO-8859-1') == 1) {
					echo '<li><img src="../upload/', htmlentities($enr['usID'],ENT_QUOTES,'ISO-8859-1'), '.jpg">';
				}
				else {
					echo '<li><img src="../images/anonyme.jpg">';
				}
					aff_user(htmlentities($enr['usID'],ENT_QUOTES,'ISO-8859-1'), htmlentities($enr['usPseudo'],ENT_QUOTES,'ISO-8859-1'), htmlentities($enr['usNom'],ENT_QUOTES,'ISO-8859-1'), "bold");

				echo '</li>	';
			$i++;
		}

		echo '<li>';
		aff_lien("suggestions", $id, $pseudo, $nom, "", "Plus de suggestions");
		//<a href="suggestions.php?profil=',$id,'&amp;pseudo=',$pseudo,'&amp;nom=',$nom,'" title="Voir les suggestions">Plus de suggestions</a>
		echo '</li></ul>';
	}

	/**
	* Affichage des suggestions dans le aside
	*
	* On va afficher toutes les suggestions disponible
	* pour un utilisateur donné avec le nombre max à
	* afficher
	*
	* @param 	resource	$r			Requête pour afficher les tendances
	* @param 	integer		$nb			Le nombre de tendances à afficher
	*
	*/

	function aff_tendence($r, $nb){
		$i = 1;
		echo '<h3>Tendances</h3><ul>';

		while (($enr = mysqli_fetch_assoc($r)) && $i<=$nb ) {
			$tend = htmlentities($enr['taID'],ENT_QUOTES,'ISO-8859-1');
			echo '<li><a href="tendances.php?tend=', $tend,'" title="Voir">#', $tend, '</a></li>';
			$i++;
		}

		echo '<li><a href="tendances.php" title="Voir les messages">Toutes les tendances</a></li></ul>';
	}

	/**
	* Affichage d'un lien vers un autre utilisateur
	*
	* L'affiche du lien le renverra vers utilisateur.php
	* avec la méthode GET. Le paramètre $bold permet de
	* savoir si le lien-pseudo de l'utilisateur doit
	* être afficher en gras
	*
	* @param 	integer		$ID			L'ID de l'utilisateur actuel
	* @param 	string		$pseudo		Le pseudo de l'utilisateur actuel
	* @param 	string		$nom		Le nom de l'utilisateur actuel
	* @param 	string		$bold		Valeur possible : "bold" ou ""
	*
	*/

	function aff_user($ID, $pseudo, $nom, $bold) {
		$pseudo_normal = $pseudo;
		$nom_normal = $nom;

		$ID = encrypt_url($ID);
		$pseudo = encrypt_url($pseudo);
		$nom = encrypt_url($nom);
		echo '<a style="font-weight:',$bold,';" href="utilisateur.php?profil=',$ID,'&amp;pseudo=',$pseudo,'&amp;nom=',$nom,'" title="Voir&nbsp;le&nbsp;CV">', $pseudo_normal , '</a> <span>', $nom_normal , '</span>';
	}

	/**
	* Affichage des liens blablas - mentions - abonnés - abonnements
	*
	* L'affiche des liens pourra être automatiser grâce à la
	* fonction et pourra fonctionner pour n'importe quelle
	* utilisateur
	*
	* @param 	string		$href				L'url de la page web. Exemple : "utilisateur" ou "abonnes"
	* @param 	integer		$profil_id			L'ID de l'utilisateur. Peut être différent du courant
	* @param 	string		$profil_pseudo		Le pseudo de l'utilisateur. Peut être différent du courant
	* @param 	string		$profil_nom			Le nom de l'utilisateur. Peut être différent du courant
	* @param 	integer		$nombre				Nombre de blablas ou de mentions etc...
	* @param 	string		$lachose			Identique à $href sauf pour abonnes car il y'a un accent "abonn&eacute;s"
	* @param 	string		$tiret				Permet de mettre un tiret entre les liens, absent pour le dernier lien.
	*
	*/

	function aff_lien($href, $profil_id, $profil_pseudo, $profil_nom, $nombre, $lachose, $tiret="") {
		$profil_id = encrypt_url($profil_id);
		$profil_pseudo = encrypt_url($profil_pseudo);
		$profil_nom = encrypt_url($profil_nom);
		echo '<a href="',$href,'.php?profil=', $profil_id, '&amp;pseudo=',$profil_pseudo,'&amp;nom=',$profil_nom,'" title="Voir">', $nombre , ' ',$lachose, '</a> ', $tiret, ' ';
	}

	/**
	 * Permet de savoir si l'utilisateur courant est abonnés
	 * à l'utilisateur demandé
	 *
	 * @param	resource	$bd				Connecteur sur la bd ouverte
	 * @param	integer		$profil_id		ID du profil demandé si l'utilisateur est abonnés avec
	 * @param 	integer		$id				ID du profil de l'utilisateur qui s'est loggé
	 */

	function deja_abonne($bd, $profil_id, $id) {

		//REQUETE DEJA ABONNER
		$sql = "SELECT count(*) as nb
				FROM users, estabonne
				WHERE eaIDUser = $id
				AND eaIDAbonne = $profil_id";
		$r = mysqli_query($bd, $sql) or bd_erreur($bd, $sql);
		$enr = mysqli_fetch_assoc($r);
		$nb = htmlentities($enr['nb'],ENT_QUOTES,'ISO-8859-1');

		if ($nb == 0)
			return "S'abonner";
		else
			return "Se&nbsp;d&eacute;sabonner";
	}

	/**
	 * Permet de connaitre le nombre de blablas
	 * d'un utilisateur lambda
	 *
	 * @param	resource	$bd				Connecteur sur la bd ouverte
	 * @param	integer		$profil_id		ID du profil de n'importe quelle utilisateur
	 *
	 * @return	integer		$blabla			Nombre de blablas
	 */

	function blabla_nb($bd, $profil_id) {
		//REQUETE NOMBRE BLABLAS
		$sql = "SELECT COUNT(*) AS nbBlbl
				FROM users, blablas
				WHERE usID='$profil_id'
				AND usID=blIDAuteur";
		$r = mysqli_query($bd, $sql) or bd_erreur($bd, $sql);
		$enr = mysqli_fetch_assoc($r);
		$blabla = htmlentities($enr['nbBlbl'],ENT_QUOTES,'ISO-8859-1');

		return $blabla;
	}

	/**
	 * Permet de connaitre le nombre de mentions
	 * d'un utilisateur lambda
	 *
	 * @param	resource	$bd				Connecteur sur la bd ouverte
	 * @param	integer		$profil_id		ID du profil de n'importe quelle utilisateur
	 *
	 * @return	integer		$mentions		Nombre de mentions
	 */

	function mention_nb($bd, $profil_id) {
		//REQUETE NOMBRE MENTIONS
		$sql = "SELECT COUNT(*) AS nbMention
				FROM users, mentions
				WHERE usID='$profil_id'
				AND usID=meIDUser";
		$r = mysqli_query($bd, $sql) or bd_erreur($bd, $sql);
		$enr = mysqli_fetch_assoc($r);
		$mentions = htmlentities($enr['nbMention'],ENT_QUOTES,'ISO-8859-1');

		return $mentions;
	}

	/**
	 * Permet de connaitre le nombre de abonnés
	 * d'un utilisateur lambda
	 *
	 * @param	resource	$bd				Connecteur sur la bd ouverte
	 * @param	integer		$profil_id		ID du profil de n'importe quelle utilisateur
	 *
	 * @return	integer		$abonnes		Nombre d'abonn�s
	 */

	function abonne_nb($bd, $profil_id) {
		//REQUETE NOMBRE ABONNES
		$sql="SELECT COUNT(*) AS nbAbne
			FROM estabonne
			WHERE estabonne.eaIDAbonne='$profil_id'";
		$r = mysqli_query($bd, $sql) or bd_erreur($bd, $sql);
		$enr = mysqli_fetch_assoc($r);
		$abonnes = htmlentities($enr['nbAbne'],ENT_QUOTES,'ISO-8859-1');

		return $abonnes;
	}

	/**
	 * Permet de connaitre le nombre d'abonnements
	 * d'un utilisateur lambda
	 *
	 * @param	resource	$bd					Connecteur sur la bd ouverte
	 * @param	integer		$profil_id			ID du profil de n'importe quelle utilisateur
	 *
	 * @return	integer		$abonnements		Nombre d'abonnements
	 */

	function abonnement_nb($bd, $profil_id) {
		//REQUETE NOMBRE ABONNEMENTS
		$sql="SELECT COUNT(*) AS nbAbment
			FROM estabonne
			WHERE estabonne.eaIDUser='$profil_id'";
		$r = mysqli_query($bd, $sql) or bd_erreur($bd, $sql);
		$enr = mysqli_fetch_assoc($r);
		$abonnements = htmlentities($enr['nbAbment'],ENT_QUOTES,'ISO-8859-1');

		return $abonnements;
	}

	/**
	 * Permet de savoir si l'utilisateur veut qu'on affiche
	 * sa photo
	 *
	 * @param	resource	$bd					Connecteur sur la bd ouverte
	 * @param	integer		$profil_id			ID du profil de n'importe quelle utilisateur
	 *
	 * @return	integer		$usAvecPhoto		0 ou 1 pour afficher la photo
	 */

	function photo($bd, $profil_id) {
		//REQUETE NOMBRE ABONNEMENTS
		$sql="SELECT usAvecPhoto
			FROM users
			WHERE usID='$profil_id'";
		$r = mysqli_query($bd, $sql) or bd_erreur($bd, $sql);
		$enr = mysqli_fetch_assoc($r);
		$usAvecPhoto = htmlentities($enr['usAvecPhoto'],ENT_QUOTES,'ISO-8859-1');

		return $usAvecPhoto;
	}

	/**
	 * Cette fonction va faire un cryptage du string donn�
	 * en param�tre
	 *
	 * @param	string		$string			La chaine de caract�re � crypter
	 *
	 * @return	string		$result			La chaine de caract�re d�crypter
	 *
	 */

	function encrypt_url($string) {
		$key = "mrrobot"; //key to encrypt and decrypts.
		$result = '';
		$test = "";

		for($i=0; $i<strlen($string); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key))-1, 1);
			$char = chr(ord($char)+ord($keychar));

			$test[$char]= ord($char)+ord($keychar);
			$result.=$char;
		}

		return urlencode(base64_encode($result));
	}

	/**
	 * Cette fonction va faire un d�cryptage du string donn�
	 * en param�tre
	 *
	 * @param	string		$string			La chaine de caract�re d�crypter
	 *
	 * @return	string		$result			La chaine de caract�re crypter
	 *
	 */

	function decrypt_url($string) {
		$key = "mrrobot"; //key to encrypt and decrypts.
		$result = '';
		$string = base64_decode(urldecode($string));

		for($i=0; $i<strlen($string); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key))-1, 1);
			$char = chr(ord($char)-ord($keychar));
			$result.=$char;
	   }
	   return $result;
	}



?>
