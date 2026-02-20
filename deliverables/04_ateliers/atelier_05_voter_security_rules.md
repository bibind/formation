# Atelier 05 — Voter ProductVoter + security rules

## 1. Enjeux (Pourquoi on le fait en production)
- Enjeux securite: restreindre l'edition aux proprietaires.
- Enjeux maintenabilite: centraliser la regle d'acces.
- Risques si mal fait: edition par un autre vendeur, fuite de donnees.
- Temps estime: 15 min.

## 2. Objectifs pedagogiques (Bloom)
- Comprendre: security vs securityPostDenormalize.
- Appliquer: implementer `ProductVoter`.
- Analyser: evaluer l'impact des roles admin.
- Evaluer: verifier les acces via tests.
- Temps estime: 15 min.

## 3. Pre-requis
- Atelier precedent: Atelier 04 (OrderCreate).
- Etat du code attendu: endpoints POST/PUT products operationnels.
- Temps estime: 5 min.

## 4. Contexte metier
- User story: un vendeur ne peut modifier que ses produits.
- Regles metier: admin override autorise.
- Temps estime: 10 min.

## 5. Pratique guidee (demo formateur)
- Etape 1: creer `ProductVoter`.
- Etape 2: brancher sur `PRODUCT_EDIT`.
- Etape 3: utiliser `security` et `securityPostDenormalize`.
- Temps estime: 45 min.

Extrait de code cible:
```php
return $this->security->isGranted('ROLE_ADMIN') || $product->owner() === $user;
```

Commandes utiles:
```bash
php project/bin/console cache:clear
```

## 6. Application (travail stagiaire)
- Checklist:
  - Implementer le Voter.
  - Appliquer la rule sur PUT /api/products/{id}.
  - Verifier le comportement admin.
- Livrables attendus:
  - `project/src/Application/Security/Voter/ProductVoter.php`
- Definition of Done:
  - Un vendeur ne modifie que ses produits.
  - Admin peut tout modifier.
- Temps estime: 45 min.

## 7. Analyse / Debrief (questions + variations)
- Questions:
  - Quand utiliser securityPostDenormalize?
  - Quelle difference avec un AccessControl global?
- Variantes:
  - Ajouter un Voter pour les commandes.
- Recommandations:
  - Utiliser les Voters pour la logique metier complexe.
- Temps estime: 20 min.

## 8. Tests & validation
- Tests a ecrire (ApiTestCase):
  - `test_seller_cannot_edit_other_seller_product`
- Scenarios:
  - Happy path: proprietaire modifie son produit.
  - Error path: autre vendeur -> 403.
- Exemple curl:
```bash
curl -X PUT \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{"name":"Hacked","categoryId":"cat_1","stock":5,"price":8000}' \
  https://api.example.test/api/products/prod_123
```
- Exemple JSON (erreur 403):
```json
{
  "@context": "/api/contexts/Error",
  "@type": "Error",
  "title": "An error occurred",
  "detail": "product_edit_forbidden",
  "status": 403
}
```
- Assertions attendues:
  - 403 pour un autre vendeur.
- Preuves audit:
  - Test `test_seller_cannot_edit_other_seller_product` OK.
- Temps estime: 25 min.

## 9. Pieges frequents & Debug tips
- Confondre security et securityPostDenormalize.
- Oublier admin override.
- Utiliser une comparaison d'objet non hydrate.
- Debug: verifier le token et l'utilisateur courant.
- Debug: tracer le Voter (log).

## 10. Bonus (avance)
- Ajouter un Voter pour la lecture de commande.
- Ajouter des roles par type de ressource.
