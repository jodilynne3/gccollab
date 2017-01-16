<?php

$french = array(

	'gcRegister:occupation' => "Occupation",
	'gcRegister:occupation:academia' => "Milieu universitaire",
	'gcRegister:occupation:student' => "Étudiant",
	'gcRegister:occupation:federal' => "Gouvernement fédéral",
	'gcRegister:occupation:provincial' => "Gouvernement provincial/territorial",
	'gcRegister:occupation:municipal' => "Administration municipale",
	'gcRegister:occupation:international' => "Gouvernement international/étranger",
	'gcRegister:occupation:community' => "Collectivité/Sans but lucratif",
	'gcRegister:occupation:business' => "Entreprise",
	'gcRegister:occupation:media' => "Média",
	'gcRegister:occupation:other' => "Autres renseignements",

	'gcRegister:occupation:university' => "Université ou collège",
	'gcRegister:occupation:department' => "Ministères / organismes",
	'gcRegister:occupation:province' => "Province ou territoire",

	// labels
	'gcRegister:form' => "Formulaire d'inscription",
	'gcRegister:email_initial' => 'Adresse de courriel',
	'gcRegister:email_secondary' => 'Confirmation du courriel',
	'gcRegister:username' => 'Nom d\'utilisateur (généré automatiquement)',
	'gcRegister:password_initial' => 'Mot de passe',
	'gcRegister:password_secondary' => 'Confirmation du mot de passe',
	'gcRegister:display_name' => 'Nom à afficher',
	'gcRegister:display_name_notice' => "Veuillez écrire votre prénom et votre nom de famille, tel que l’on vous connaît au travail. Conformément aux Conditions d’utilisation, le nom affiché doit correspondre à votre vrai nom. Il n’est pas permis d’utiliser un pseudonyme.",
	'gcRegister:please_enter_email' => 'Veuillez inscrire votre adresse de courriel',
	'gcRegister:department_name' => 'Inscrire le nom de votre ministère',
	'gcRegister:register' => 'S\'inscrire',
	'gcRegister:custom' => 'Veuillez indiquer le nom de l\'employeur',
				
	// error messages on the form			// error messages on the form			
	'gcRegister:failedMySQLconnection' => 'Impossible de se connecter à la base de données',
	'gcRegister:invalid_email' => '<a href="#email_initial">Adresse de courriel non valide</a>',
	'gcRegister:invalid_email2' => 'Adresse de courriel non valide',
	'gcRegister:empty_field' => 'champ vide',
	'gcRegister:mismatch' => 'Erreur de correspondance',
	'gcRegister:make_selection' => 'Veuillez faire une sélection',
				
	// notice			// avis			
	'gcRegister:email_notice' => '<h2 class="h2"></h2>',
				
	'gcRegister:terms_and_conditions' => 'J\'ai lu, j\'ai compris et j\accepte les <a href="/termes">Conditions d\'utilisation</a>.',
	'gcRegister:validation_notice' => '<b>Remarque :</b> Vous ne pourrez pas ouvrir une session sur Gcconnex avant d\'avoir reçu un courriel de validation.',
	'gcRegister:tutorials_notice' => '<a href="http://www.gcpedia.gc.ca/wiki/Tutorials_on_GC2.0_Tools_/_Tutoriels_sur_les_outils_GC2.0/GCconnex">Tutoriels de GCconnex</a>',
				
	// error messages that pop up			// messages d'erreur qui apparaissen
	'gcRegister:toc_error' => '<a href="#toc2">Vous devez accepter les condtions d\'utilisation</a>',
	'gcRegister:email_in_use' => 'Cette adresse de courriel a déjà déjà été enregistrée',
	'gcRegister:password_mismatch' => '<a href="#password">Les mots de passe ne sont pas identiques</a>',
	'gcRegister:password_too_short' => '<a href="#password">Le mot de passe doit avoir au moins 6 caractères</a>',
	'gcRegister:email_mismatch' => '<a href="#email_initial">Les adresses de courriel ne sont pas identitiques</a>',
	'gcRegister:display_name_is_empty' => '<a href="#name">Le champ « Nom à afficher » ne peut pas être laissé vide</a>',

    'gcRegister:department' => 'Organisation',
	'gcRegister:university' => 'Université',
	'gcRegister:college' => 'Collège',

	'gcRegister:alberta' => 'Alberta',
	'gcRegister:british-columbia' => 'Colombie-Britannique',
	'gcRegister:manitoba' => 'Manitoba',
	'gcRegister:new-brunswick' => 'Nouveau-Brunswick',
	'gcRegister:newfoundland' => 'Terre-Neuve-et-Labrador',
	'gcRegister:northwest-territories' => 'Territoires du Nord-Ouest',
	'gcRegister:nova-scotia' => 'Nouvelle-Écosse',
	'gcRegister:nunavut' => 'Nunavut',
	'gcRegister:ontario' => 'Ontario',
	'gcRegister:pei' => 'Île-du-Prince-Édouard',
	'gcRegister:quebec' => 'Québec',
	'gcRegister:saskatchewan' => 'Saskatchewan',
	'gcRegister:yukon' => 'Yukon',
);
 
add_translation("fr", $french);
