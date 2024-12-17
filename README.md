# Gestion Immobilière - Web App Symfony
A full Swiss property managment for manage your property

Cette application web permet de gérer des biens immobiliers en Suisse. Elle offre des fonctionnalités complètes pour la gestion des locataires, des finances, et des accès multi-utilisateurs, adaptées aux besoins des particuliers ou gestionnaires immobiliers.

---

## Fonctionnalités Principales

### Gestion des biens immobiliers
- Ajouter, modifier et consulter des propriétés (appartements, maisons, garages, etc.).
- Enregistrer des informations telles que l'adresse, le prix d'achat, et les taux hypothécaires.

### Gestion des locataires
- Suivi des locataires actuels et passés.
- Suivi des loyers, des coûts liés aux places de parc et des périodes de location.

### Gestion des finances
- Enregistrement des entrées financières (loyers, charges, impôts, etc.).
- Suivi des dépenses et revenus liés à chaque propriété.

### Gestion multi-utilisateurs
- Attribution d'accès à plusieurs utilisateurs pour la gestion d'une même propriété.
- Définition de rôles spécifiques (propriétaire, collaborateur).

### Autres fonctionnalités
- Gestion des documents (ex : contrats, factures).
- Enregistrement des actions via un système de logs.

---

## Pré-requis

- PHP 8.1 ou supérieur
- Symfony 6.3 ou supérieur
- Base de données : MySQL ou PostgreSQL
- Composer
- Node.js et npm (pour les assets)

---

## Installation

### Étapes pour démarrer le projet :

1. **Cloner le repository**
   ```bash
   git clone https://github.com/JulienOxi/property-managment.git
   cd gestion-immobiliere
   ```

2. **Installer les dépendances PHP**
   ```bash
   composer install
   ```

3. **Configurer les variables d'environnement**
   Copier le fichier `.env` et modifier les valeurs nécessaires :
   ```bash
   cp .env .env.local
   ```
   Exemple :
   ```env
   DATABASE_URL="mysql://username:password@127.0.0.1:3306/gestion_immo"
   ```

4. **Créer la base de données et appliquer les migrations**
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

5. **Installer les dépendances front-end**
   ```bash
   npm install
   npm run build
   ```

6. **Lancer le serveur Symfony**
   ```bash
   symfony serve
   ```

L'application sera disponible sur [http://localhost:8000](http://localhost:8000).

---

## Entités Principales

### User
- Gestion des utilisateurs avec des rôles (propriétaire, collaborateur).

### Property
- Informations sur les biens (type, adresse, prix, etc.).

### Tenant
- Suivi des locataires.

### FinancialEntry
- Gestion des finances (loyers, charges, impôts).

### AccessControl
- Gestion des permissions multi-utilisateurs sur chaque propriété.

---

## Contribution

1. Forkez le repository
2. Créez une branche pour votre feature :
   ```bash
   git checkout -b feature/nom-de-la-feature
   ```
3. Soumettez une pull request

---

## License

Ce projet est sous licence MIT. Vous êtes libre de l'utiliser et de le modifier selon vos besoins.
