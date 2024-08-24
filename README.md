# Etat_civil-PHP


## **IMPORTANT**

Utiliser les identifiants suivant pour se connecter :

* username: admin
* password: admin123

Exécuter

```
composer install
```

pour installer les dépendances utilisées lors de la génération du PDF de l'acte.



## **MOINS IMPORTANT:**

### 1. **Architecture Matérielle**

#### **Développement Local**

- **Développeur**: Utilise une machine locale pour le développement avec VS Code comme IDE et le serveur local  XAMPP.

#### **Serveur de Test**

- **Serveur de Test**: Environnement de staging pour tester l'application avant sa mise en production. Peut être une instance de serveur dans un cloud (par exemple, AWS EC2) ou un serveur physique.
- **Base de Données de Test**: Base de données distincte pour tester les fonctionnalités sans affecter les données de production.

#### **Serveur de Production**

- **Serveur Web**: Héberge l'application en production sur un serveur physique.
- **Base de Données de Production**: Base de données principale où les données de l'application sont stockées.

### 2. **Architecture Logicielle**

#### **Développement**

- **IDE/Éditeur de Code**: Visual Studio Code.
- **Environnement de Développement Local**: Utilisation de XAMPP l'environnement de production..

#### **Production**

- **Serveur Web**: Apache pour servir l'application PHP.
- **Base de Données**: MySQL pour stocker les données.

### 3. **Schéma d'Architecture**

Voici un schéma pour illustrer cette architecture :

```
                              +-------------------------+
                              |    Utilisateurs         |
                              +-----------+-------------+
                                          |
                                          |
                              +-----------v-------------+
                              |     Serveur Web         |
                              |  (Apache)         |
                              +-----------+-------------+
                                          |
                                          |
      +---------------+      +------------v-------------+
      |  Serveur de    |      |      Serveur de          |
      |  Test          |      |      Production          |
      +---------------+      +--------------------------+
            |                           |
            |                           |
  +---------v----------+         +--------v---------+
  |  Base de Données   |         |   Base de Données|
  |  de Test           |         |   de Production  |
  +--------------------+         +------------------+
```

### 4. **Sécurité**

- **Authentification et Autorisation**: Systèmes de gestion des utilisateurs avec des sessions pour sécuriser l'accès et de gestion des roles (administrateur/superviseur/analyste).

### 5. **Déploiement et Maintenance**
