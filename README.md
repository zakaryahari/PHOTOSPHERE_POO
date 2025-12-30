# 📸 PhotoSphere - Galerie Photo Communautaire

[cite_start]**PhotoSphere** est une plateforme web élégante et performante conçue pour les photographes souhaitant partager, organiser et interagir autour de leur travail[cite: 1]. [cite_start]Développé pour la startup *PixelCraft Digital*, ce projet met l'accent sur un système de rôles avancé et une gestion rigoureuse du cycle de vie des médias[cite: 1].

---

## 🚀 Contexte du Projet

[cite_start]Les photographes (amateurs et professionnels) manquent souvent de plateformes simples mais puissantes pour exposer leurs portfolios sans la complexité des réseaux sociaux traditionnels[cite: 1]. 

### Objectifs techniques :
* **Architecture Orientée Objet** : Utilisation de l'héritage pour les différents types d'utilisateurs.
* **Sécurité** : Hachage des mots de passe avec **bcrypt** et validation stricte des fichiers.
* **Performance** : Optimisation des requêtes et mise en cache des résultats fréquents.

---

## 👥 Hiérarchie des Utilisateurs (RBAC)

Le système gère quatre niveaux d'accès distincts avec des fonctionnalités spécifiques :

| Rôle | Description | Fonctionnalités Clés |
| :--- | :--- | :--- |
| **BasicUser** | Photographe Amateur | Quota de 10 photos/mois, albums publics uniquement. |
| **ProUser** | Photographe Professionnel | Upload illimité, albums privés, statistiques avancées. |
| **Moderator** | Modérateur | Suppression de commentaires, suspension de comptes. |
| **Admin** | Administrateur | Gestion totale des utilisateurs et du système. |

---

## 🖼️ Gestion des Photos et Albums

### 1. Cycle de Vie des Photos
Chaque photo suit un processus précis pour garantir la qualité du contenu :
1.  **Brouillon** : Visible uniquement par le propriétaire après l'upload.
2.  **Publié** : Accessible publiquement ou en privé selon les règles définies.
3.  **Archivé** : Retiré de la vue publique mais conservé dans l'espace personnel.

### 2. Règles Métier
* **Validation technique** : Fichiers JPEG, PNG, ou GIF de moins de 10 Mo.
* **Contraintes d'Albums** : Minimum 1 photo, maximum 100 photos par album.
* **Interactions** : Un utilisateur ne peut pas commenter ses propres photos.

---

## 📊 Conception (UML)

### Diagramme de Use Cases
Ce diagramme détaille les actions possibles pour chaque acteur (Upload, Like, Modération).

![Diagramme Use Case](Conception/Use-case.png)


### Diagramme de Classes
L'architecture logicielle repose sur une classe mère `User` et une gestion de photos avec métadonnées techniques.

![Diagramme de Classes](Conception/class-diagram.png)


---

## 💻 Stack Technique

* **Langage** : PHP 8+ (POO)
* **Base de données** : MySQL (Contraintes d'intégrité référentielle)
* **Sécurité** : Bcrypt, Validation MIME réelle, Protection contre les injections SQL
* **Outils** : Git, Trello, PlantUML
