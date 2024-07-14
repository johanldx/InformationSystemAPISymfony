# Partiel de Système d'information

API de Gestion des commandes liés pour un projet de fin de première année à l'ESGI.

Dépendances
-----------

Ces APis utilisent symfony/http-client et phpmailer/phpmailer.

# Documentation des API
## Facture API
### Routes
- **GET /facture/** : Retourne la liste de toutes les factures.
  - Réponse : JSON array des factures.
- **GET /facture/{id}** : Retourne les détails d'une facture spécifique.
  - Réponse : JSON de la facture ou un message d'erreur si la facture n'est pas trouvée.
- **POST /facture/** : Crée une nouvelle facture.
  - Corps de la requête : JSON avec les champs `amount`, `due_date`, et `customer_email`.
  - Réponse : JSON de la nouvelle facture créée ou un message d'erreur en cas de champs manquants.
- **PUT /facture/{id}** : Met à jour une facture spécifique.
  - Corps de la requête : JSON avec les champs `amount`, `due_date`, et `customer_email`.
  - Réponse : JSON de la facture mise à jour ou un message d'erreur si la facture n'est pas trouvée.
- **DELETE /facture/{id}** : Supprime une facture spécifique.
  - Réponse : Message de confirmation ou d'erreur si la facture n'est pas trouvée.

### Exemple de requête POST
```json
{
  "amount": 100,
  "due_date": "2024-12-31",
  "customer_email": "client@example.com"
}
```
## Commande API
### Routes
- **GET /commande/** : Retourne la liste de toutes les commandes.
  - Réponse : JSON array des commandes.
- **GET /commande/{id}** : Retourne les détails d'une commande spécifique.
  - Réponse : JSON de la commande ou un message d'erreur si la commande n'est pas trouvée.
- **POST /commande/** : Crée une nouvelle commande.
  - Corps de la requête : JSON avec les champs `product_id`, `customer_email`, `total_price` et `quantity`.
  - Réponse : JSON de la nouvelle commande créée ou un message d'erreur en cas de champs manquants.
- **PUT /commande/{id}** : Met à jour une commande spécifique.
  - Corps de la requête : JSON avec les champs `product_id`, `customer_email`, `total_price` et `quantity`.
  - Réponse : JSON de la commande mise à jour ou un message d'erreur si la commande n'est pas trouvée.
- **DELETE /commande/{id}** : Supprime une commande spécifique.
  - Réponse : Message de confirmation ou d'erreur si la commande n'est pas trouvée.
### Exemple de requête POST
```json
{
  "product_id": 53,
  "customer_email": "client@example.com",
  "quantity": 23,
  "total_price": 1200
}
```
## Notification API
### Routes
- **GET /notification/** : Retourne la liste de toutes les notifications.
  - Réponse : JSON array des notifications.
- **GET /notification/{id}** : Retourne les détails d'une notification spécifique.
  - Réponse : JSON de la notification ou un message d'erreur si la notification n'est pas trouvée.
- **POST /notification/** : Crée une nouvelle notification.
  - Corps de la requête : JSON avec les champs `email_recipient`, `message`, et `sujet`.
  - Réponse : JSON de la nouvelle notification créée ou un message d'erreur en cas de champs manquants.
### Exemple de requête POST
```json
{
  "email_recipient": "client@example.com",
  "message": "Votre commande a été expédiée.",
  "sujet": "Confirmation d'expédition"
}
```
