# FinalInformationSystemProject
  API de Gestion des Factures

API de Gestion des Factures
===========================

Cette API permet de gérer les factures. Elle inclut des endpoints pour créer, lire, mettre à jour et supprimer des factures, ainsi que pour notifier un service externe lors de la création d'une facture.

Endpoints
---------

### 1\. Lister toutes les factures

*   **URL**: `/facture/`
*   **Méthode HTTP**: `GET`
*   **Description**: Récupère la liste de toutes les factures.
*   **Réponse**:
    *   **Code 200**: Succès. Retourne un tableau JSON de toutes les factures.

```json
[
  {
    "id": 1,
    "amount": 100,
    "due_date": "2024-07-11T00:00:00+00:00",
    "customer_email": "johan.ledoux25@gmail.com"
  },
]
    
```
    
    

### 2\. Créer une nouvelle facture

*   **URL**: `/facture/`
*   **Méthode HTTP**: `POST`
*   **Description**: Crée une nouvelle facture et notifie un service externe.
*   **Corps de la requête** (JSON):

```json
{
  "amount": 100,
  "due_date": "2024-07-11",
  "customer_email": "johan.ledoux25@gmail.com"
}
```
                
                
    
*   **Réponse**:
    *   **Code 201**: Facture créée avec succès.
    *   **Code 400**: Champs requis manquants.
    *   **Code 503**: Service de notification non disponible.
    *   **Code 500**: Erreur lors de la notification du service externe.

### 3\. Récupérer une facture par ID

*   **URL**: `/facture/{id}`
*   **Méthode HTTP**: `GET`
*   **Description**: Récupère une facture par son ID.
*   **Paramètre URL**:
    *   `id` : ID de la facture à récupérer.
*   **Réponse**:
    *   **Code 200**: Succès. Retourne la facture.
    *   **Code 404**: Facture non trouvée.

```json
{
  "id": 1,
  "amount": 100,
  "due_date": "2024-07-11",
  "customer_email": "johan.ledoux25@gmail.com"
}
``` 
    

### 4\. Mettre à jour une facture

*   **URL**: `/facture/{id}`
*   **Méthode HTTP**: `PUT`
*   **Description**: Met à jour une facture existante.
*   **Paramètre URL**:
    *   `id` : ID de la facture à mettre à jour.
*   **Corps de la requête** (JSON):
    
```json
{
  "id": 1,
  "amount": 100,
  "due_date": "2024-07-11",
  "customer_email": "johan.ledoux25@gmail.com"
}
```                
                
    
*   **Réponse**:
    *   **Code 200**: Facture mise à jour avec succès.
    *   **Code 400**: Champs requis manquants.
    *   **Code 404**: Facture non trouvée.

### 5\. Supprimer une facture

*   **URL**: `/facture/{id}`
*   **Méthode HTTP**: `DELETE`
*   **Description**: Supprime une facture existante.
*   **Paramètre URL**:
    *   `id` : ID de la facture à supprimer.
*   **Réponse**:
    *   **Code 204**: Facture supprimée avec succès.
    *   **Code 404**: Facture non trouvée.

Gestion des Erreurs
-------------------

*   **Erreur 400**: Champs requis manquants.
*   **Erreur 404**: Facture non trouvée.
*   **Erreur 500**: Erreur interne lors de la notification du service externe.
*   **Erreur 503**: Service de notification non disponible.

Dépendances
-----------

Cette API utilise le composant HTTP Client de Symfony pour faire des appels à une API externe.