<?php
//
// Paramètres de l'application
//
/* Comme le fichier des paramètres doit être (normalement)
 * appelé par tous les scripts, nous pouvons y placer toutes
 * les initialisations qui devraient être faites.  
 * Nous pourrions y mettre ob_start et session_start() par
 * exemple. Elles n'y sont pas pour que vous ne perdiez pas
 * de vue la bufferisation des sorties. 
 */
//ob_start();
//session_start();

/* Une des façons les plus simples de définir des paramètres
 * est de définir des constantes car elles sont "superglobales"
 */

// Phase de développement (TRUE) ou de production (FALSE)
// Permet d'afficher des messages de débuggage (TRUE)
define('IS_DEV', TRUE);

// Paramètres base de données
define('BD_SERVEUR', 'localhost');
define('BD_USER', 'cuiteur_user');
define('BD_PASS', 'cuiteur_pass');
define('BD_NOM', 'cuiteur');
?>

