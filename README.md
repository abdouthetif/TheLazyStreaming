# TheLazyStreaming

Prérequis: Symfony et composer, Cmder ou autre, XAMPP ou autre

Installion :

Il faut commencer par lancer cmder et se placer dans le dossier dans lequel on souhaite récupèrer le projet.

Ensuite on exècute la commande :

git clone https://github.com/abdouthetif/TheLazyStreaming nom-du-dossier

Il faut ensuite exécuter la commande :

composer install.

Une fois terminé il faudra modifier le fichier .env : il faudra décommenter la ligne qui concerne la base de données en fonction de votre configuration.
Par exemple pour mysql ce sera cette ligne : 

DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"

Ensuite on peut lancer le server XAMPP

Il faut ensuite exécuter 3 commandes dans Cmder :

  1. Pour créer la base de donnée :
  
  symfony console doctrine:database:create
  
  2. Pour lancer la migration :
  
  symfony console doctrine:migrations:migrate
  
  3. Pour lancer les fixtures :
  
  symfony console doctrine:fixtures:load

S'il y a une erreur lors du lancement des fixtures il faudra :

  1. relancer une migration :
  
  symfony console make:migration
  
  2. relancer la migration :
  
  symfony console doctrine:migrations:migrate
  
  3. relancer les fixtures :
  
  symfony console doctrine:fixtures:load
