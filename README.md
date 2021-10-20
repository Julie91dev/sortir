# sortir
Projet réalisé pendant la formation à l'ENI - Php Symphony

### Installation

1. Naviguer vers votre dossier web, ici utilisation de WampServer

	`cd /Wamp64/www/`

2. Cloner le projet

	`git clone 'https://github.com/Julie91dev/sortir.git projet-sortir`

3. Naviguer dans le répertoire du projet

	`cd projet-sortir`

4. Installer les dépendances

	`composer install`

5.Configurer la base de données dans le fichier `.env`

6. créer la base de données

	`php bin/console doctrine:database:create`
	
	`php bin/console doctrine:schema:update --force`

7.Charger les données de test

	`php bin/console doctrine:fixtures:load`

8.Importer le fichier 'sortir-data.sql' pour enregistrer des lieux et villes


### Comptes

##### compte user
mail: user@test.com

mdp: motdepasse

##### compte admin

mail: admin@test.com

mdp: password
