# Interpréteur d'équations

# Architecture des fichiers

L'équation à traiter est entrée dans `index.php` et est postée via formulaire php. La requête `POST` est traitée dans `index.php`.
Le traitement fait appel à `Equation.php` qui retourne un objet contenant les propriétés identifiées.

__Le travail est à effectuer dans `index.php` après la déclaration de la variable `$equation`!!__
