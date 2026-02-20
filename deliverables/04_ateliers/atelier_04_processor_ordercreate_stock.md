# Atelier 04 — Processor OrderCreate + decrement stock

## 1. Enjeux (Pourquoi on le fait en production)
- Enjeux techniques: garantir la coherence stock/commande.
- Enjeux perf/securite: eviter la sur-vente et les etats incoherents.
- Risques si mal fait: stock negatif, commandes invalides.
- Temps estime: 15 min.

## 2. Objectifs pedagogiques (Bloom)
- Comprendre: orchestration de creation commande + lignes.
- Appliquer: implementer `OrderCreateProcessor`.
- Analyser: traiter les erreurs metier (stock insuffisant).
- Evaluer: valider via tests API.
- Temps estime: 15 min.

## 3. Pre-requis
- Atelier precedent: Atelier 03 (ProductCreate).
- Etat du code attendu: entites Order/OrderLine, DTO OrderInput/OrderOutput.
- Temps estime: 5 min.

## 4. Contexte metier
- User story: en tant qu'acheteur, je passe commande.
- Regles metier: stock ne peut pas passer sous zero, commande en draft avant validation.
- Temps estime: 10 min.

## 5. Pratique guidee (demo formateur)
- Etape 1: charger les produits des lignes.
- Etape 2: verifier stock et decrementer.
- Etape 3: persister commande + lignes.
- Temps estime: 60 min.

Extrait de code cible:
```php
if ($product->stock() < $lineQty) {
    throw new \DomainException('stock_insufficient');
}
$product->decrementStock($lineQty);
```

Commandes utiles:
```bash
php project/bin/console doctrine:migrations:migrate
```

## 6. Application (travail stagiaire)
- Checklist:
  - Implementer `OrderCreateProcessor`.
  - Gerer transaction ou logique atomique.
  - Retourner `OrderOutput`.
- Livrables attendus:
  - `project/src/Application/State/Processor/OrderCreateProcessor.php`
- Definition of Done:
  - POST /api/orders cree une commande.
  - Stock decremente si validation OK.
- Temps estime: 60 min.

## 7. Analyse / Debrief (questions + variations)
- Questions:
  - Quand utiliser un lock pessimiste?
  - Ou gerer la validation de statut?
- Variantes:
  - Decrement stock a la validation finale.
- Recommandations:
  - Garder la logique metier dans le Processor.
- Temps estime: 20 min.

## 8. Tests & validation
- Tests a ecrire (ApiTestCase):
  - `test_order_create_ok_decrements_stock_when_validated`
  - `test_order_create_fails_when_insufficient_stock`
- Scenarios:
  - Happy path: stock suffisant.
  - Error path: stock insuffisant -> 400.
- Exemple payload JSON:
```json
{
  "lines": [
    {"productId": "prod_123", "quantity": 2}
  ]
}
```
- Exemple curl:
```bash
curl -X POST \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{"lines":[{"productId":"prod_123","quantity":2}]}' \
  https://api.example.test/api/orders
```
- Assertions attendues:
  - 201 Created si stock suffisant.
  - 400 si stock insuffisant.
- Preuves audit:
  - Tests nommes ci-dessus OK.
  - Logs de stock decrement.
- Temps estime: 30 min.

## 9. Pieges frequents & Debug tips
- Oublier de verifier le stock avant decrement.
- Laisser un status autre que draft.
- Ne pas utiliser de transaction si necessaire.
- Debug: tracer les lignes de commande.
- Debug: verifier l'etat du stock apres test.

## 10. Bonus (avance)
- Ajouter une reservation de stock temporaire.
- Ajouter des evenements domaine (OrderValidated).
