# ISI BURGER - Documentation du Projet

## Présentation du Projet

ISI BURGER est une application web de gestion de commandes de burgers pour un restaurant.

## Fonctionnalités Principales

### 1. Gestion des Utilisateurs
- Inscription et connexion des clients
- Authentification de l'administrateur
- Gestion des profils utilisateurs

### 2. Catalogue de Produits
- Affichage des burgers disponibles
- Recherche de produits
- Consultation des détails d'un burger (prix, description, disponibilité)

### 3. Gestion du Panier
- Ajout de produits au panier
- Modification des quantités
- Suppression d'articles
- Validation du panier

### 4. Gestion des Commandes
- Création de commandes
- Suivi de l'état des commandes (en attente, en préparation, prête, payée, annulée)
- Historique des commandes pour les clients
- Gestion des commandes pour les gestionnaires

### 5. Paiements
- Enregistrement des paiements (espèces ou carte)
- Génération de reçus de paiement

### 6. Statistiques et Rapports
- Tableau de bord avec statistiques générales
- Statistiques journalières (commandes en cours, validées, recettes)
- Statistiques des ventes
- Statistiques des produits
- Statistiques des clients
- Graphiques (Chart.js) pour visualiser les données

### 7. Notifications et Automatisation
- Email de confirmation après chaque commande
- Notification aux gestionnaires pour les nouvelles commandes
- Facture en PDF envoyée au client lorsque la commande est prête


Pour traiter les notifications en arrière-plan :

php artisan queue:work --tries=3 --timeout=60

