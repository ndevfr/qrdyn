# V1.0.25
- Correction de l'exécution de code javascript dans le code html affiché
- Modification des fonctions sql_ (select, select_unique, insert, update, delete) pour utiliser real_escape_string

# V1.0.21
- Correction de l'enregistrement de code javascript dans les informations des QR-codes

# V1.0.20
- Correction du fichier install.php (bugs divers, passage en utf8, ajout de "SITE_RSC" dans config.php ; pour mettre à jour config.php à la main, rajouter une ligne "SITE_RSC" identique à "SITE_URL")
- Prise en charge du mode http (et non https) si l'URL du site ne le contient pas
- Correction de bugs divers dans "edit.php" et "manage.php" (notamment avec les redirections)
- Utilisation de la fonction removeLink dans le fichier ajax-delete.php
- Utilisation de la variable "SITE_RSC" pour les ressources css et js ainsi que les images

# V1.0.15
- Ajout du fichier css/custom.css pour permettre plus de personnalisation
- Correction de différents bugs signalés

# V1.0.10
- Ajout de la connexion par token (merci Arnaud Durand - mathix.org
- Correction du bug de mot de passe du compte utilisateur du script install.php