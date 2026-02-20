# Atelier 06 — Filtres standards + filtre custom owner

## 1. Enjeux (Pourquoi on le fait en production)
- Enjeux techniques: recherche efficace et securisee.
- Enjeux securite: isolation des donnees par utilisateur.
- Risques si mal fait: fuite de donnees entre vendeurs.
- Temps estime: 15 min.

## 2. Objectifs pedagogiques (Bloom)
- Comprendre: filtres API Platform standards.
- Appliquer: creer un filtre custom `owner`.
- Analyser: effet des filtres sur les requetes.
- Evaluer: valider les resultats attendus.
- Temps estime: 15 min.

## 3. Pre-requis
- Atelier precedent: Atelier 05 (Voter securite).
- Etat du code attendu: Provider products operationnel.
- Temps estime: 5 min.

## 4. Contexte metier
- User story: un vendeur ne voit que ses produits.
- Regles metier: le client ne fournit pas `owner` dans le payload.
- Temps estime: 10 min.

## 5. Pratique guidee (demo formateur)
- Etape 1: activer les filtres standards (Search/Order).
- Etape 2: creer un filtre custom pour forcer owner = user courant.
- Etape 3: documenter les parametres dans OpenAPI.
- Temps estime: 45 min.

Extrait de code cible:
```php
$queryBuilder
    ->andWhere('p.owner = :owner')
    ->setParameter('owner', $user);
```

Commandes utiles:
```bash
php project/bin/console cache:clear
```

## 6. Application (travail stagiaire)
- Checklist:
  - Configurer filtres standards sur ProductOutput.
  - Implementer filtre custom owner.
  - Verifier que `owner` n'est pas fourni par le client.
- Livrables attendus:
  - `project/src/Infrastructure/Filter/OwnerFilter.php` (ou equivalent)
- Definition of Done:
  - Filtrage par owner applique automatiquement.
  - Parametres documentes dans OpenAPI.
- Temps estime: 45 min.

## 7. Analyse / Debrief (questions + variations)
- Questions:
  - Pourquoi ne pas exposer `owner` en filtre public?
  - Quel impact sur les indexes SQL?
- Variantes:
  - Filtre custom sur categorie.
- Recommandations:
  - Garder l'isolation serveur-side.
- Temps estime: 20 min.

## 8. Tests & validation
- Tests a ecrire (ApiTestCase):
  - Listes filtrees par owner.
- Scenarios:
  - Happy path: vendeur voit ses produits.
  - Error path: pas d'acces aux produits d'un autre vendeur.
- Exemple curl:
```bash
curl -X GET \
  -H "Authorization: Bearer <token>" \
  "https://api.example.test/api/products?name=shirt"
```
- Exemple JSON (reponse):
```json
[
  { "id": "prod_789", "name": "Shirt", "categoryName": "Clothes", "stock": 5, "price": 3000 }
]
```
- Assertions attendues:
  - Collection limitee a l'owner.
- Preuves audit:
  - OpenAPI exporte (`project/docs/openapi.json`).
  - Logs SQL avec parametre owner.
- Temps estime: 25 min.

## 9. Pieges frequents & Debug tips
- Filtre owner base sur un parametre client.
- Oublier d'ajouter le filtre sur l'ApiResource.
- Filtre qui casse la pagination.
- Debug: inspecter la requete SQL.
- Debug: verifier l'utilisateur courant.

## 10. Bonus (avance)
- Ajouter un filtre custom multi-roles.
- Ajouter un filtre de date pour commandes.
