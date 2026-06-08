Projet de Spécialité - Site WebApp de Blintest - PHP Symfony

Objectifs : 

- Système d'authentification des utilisateurs
- Système de difficultés et de catégories des questions
- Chaque "Guess" aura un type d'indice (vidéo, image, texte, sons), ses réponses possibles, et une difficultés
- Possibilité à un utilisateur de proposer une nouvelle Question à ajouter
- Un utilisateur peut jouer seul et gagner des points sur son compte dans différente catégorie
- Un utilisateur peut créer une partie de groupe en Local pour jouer à plusieurs (le + rapide gagne)

Données Utilisateurs : 

L'utilisateur possède : 
- un nom + prénom
- un pseudo
- une adresse email
- un mot de passe

Les fonctionnalités : 

- Formulaire d'inscription / connexion avec base de donnée MariaDB
- Connexion utilisateur permanente sur l'ensemble de l'application web
- Création de 3 modes de jeu :
- - ORIGINAL : 10/20/30/50 Questions / Temps 15s par questions / Choix des catégories (multiple) / Choix des difficultés (multiple)
  - GROUPE : 10/20/30/50 Questions / Temps 15s par question / Choix des catégories (multiple) / Choix des difficultés (multiple) / Création d'équipe pour la partie
  - AVENTURE : Toutes les questions du jeu / Temps illimité / Toutes les difficultés / Points gagné en jouant
- Système de jeu :
  Affichage des indices / boite d'entrée réponse utilisateur / Système minuteur / Affichage de la réponse / Vérification des réponses et attribution des points / Fin de jeu et affichage des résultats
