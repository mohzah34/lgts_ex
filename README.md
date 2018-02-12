# lgts Code example


- Les fichiers php de base du framework se trouvent dans le dossier "Framework".

LGTS est un intranet développé en solo il y a deux ans. j'ai utilisé mon custom Framework sans ORM.
J'ai limité les fichiers au niveau de l'encodage des prestations.

L'application permet aux travailleurs de l'entreprise de venir encoder leurs prestations du mois.Via un tableau auto généré selon les jours du mois courant, chaque ligne correspondt à une date du mois.Il est possible de sélectionner le client, le nombre d'heures, début de la prestation et le nombre de titres remis, les titres manquants sont calculés automatiquement. Un double-clique sur la cellule de la colonne ouvre un select pour choisir la valeur. Une fois toutes les prestations encodées l'employée clôture le calendrier et passe au mois suivant.

- Une fois un client et le nombre d'heure séléctionné , un appel ajax est lancé pour encoder la prestation dans la DB. 

DEMO EN VIDEO :
https://www.useloom.com/share/a8d9c3a11e044aa4b52072c61b4f5cee

La vue de ce tableau dynamique se trouve dans le dossier views/view_prestations.php.
Le script JS/JQuery avec les appels ajax se trouve dans js/custom_datatable.js.
L'action se trouve dans le controller controllers/ControllerPrestation.php ( action : Prestations ).

Aujourd'hui ce projet passe en refonte complète. Ce mois-ci je migre l'application vers angular, car le code devient trop difficile à maintenir et l'ajout de fonctionnalité devient fastidieux. Mon mini-framework devient donc obselète vu l'évolution du projet.
