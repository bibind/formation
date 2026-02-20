# Atelier 03 — Processor ProductCreate

## 1. Enjeux (Pourquoi on le fait en production)
- Enjeux techniques: centraliser la creation produit dans un Processor.
- Enjeux securite: forcer owner = user courant.
- Risques si mal fait: elevation de privileges, incoherence metier.
- Temps estime: 15 min.

## 2. Objectifs pedagogiques (Bloom)
- Comprendre: le cycle de traitement API Platform.
- Appliquer: implementer un Processor de creation.
- Analyser: gerer les erreurs metier et la validation.
- Evaluer: verifier la creation via tests API.
- Temps estime: 15 min.

## 3. Pre-requis
- Atelier precedent: Atelier 02 (Provider collection).
- Etat du code attendu: DTO ProductInput et ProductOutput operation POST en place.
- Temps estime: 5 min.

## 4. Contexte metier
- User story: en tant que vendeur, je cree un produit rattache a ma categorie.
- Regles metier: owner = user courant, stock >= 0, price >= 0.
- Temps estime: 10 min.

## 5. Pratique guidee (demo formateur)
- Etape 1: recuperer l'utilisateur courant via Security.
- Etape 2: resoudre la categorie par id.
- Etape 3: valider et persister.
- Temps estime: 45 min.

Extrait de code cible:
```php
$product = $this->productFactory->create($input, $user, $category);
$this->em->persist($product);
$this->em->flush();
```

Commandes utiles:
```bash
php project/bin/console doctrine:migrations:migrate
```

## 6. Application (travail stagiaire)
- Checklist:
  - Implementer `ProductCreateProcessor`.
  - Gerer validation metier (stock/prix).
  - Retourner un `ProductOutput`.
- Livrables attendus:
  - `project/src/Application/State/Processor/ProductCreateProcessor.php`
- Definition of Done:
  - POST /api/products cree un produit avec owner = user courant.
  - Erreurs metier retournees en 400.
- Temps estime: 45 min.

## 7. Analyse / Debrief (questions + variations)
- Questions:
  - Pourquoi ne pas accepter owner depuis le payload?
  - Ou gerer les DomainException?
- Variantes:
  - Validation via Validator Symfony.
- Recommandations:
  - Garder une logique metier unique dans le Processor.
- Temps estime: 20 min.

## 8. Tests & validation
- Tests a ecrire (ApiTestCase):
  - `test_seller_can_create_product`.
- Scenarios:
  - Happy path: creation produit valide.
  - Error path: categorie inexistante ou stock negatif.
- Exemple payload JSON:
```json
{
  "name": "Blue T-shirt",
  "categoryId": "cat_123",
  "stock": 10,
  "price": 2500
}
```
- Exemple curl:
```bash
curl -X POST \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{"name":"Blue T-shirt","categoryId":"cat_123","stock":10,"price":2500}' \
  https://api.example.test/api/products
```
- Assertions attendues:
  - 201 Created.
  - Owner = user courant.
- Preuves audit:
  - Test `test_seller_can_create_product` OK.
  - OpenAPI exporte (`project/docs/openapi.json`).
- Temps estime: 25 min.

## 9. Pieges frequents & Debug tips
- Utiliser un owner provenant du payload.
- Oublier la validation du stock/prix.
- Retourner une entite au lieu du DTO.
- Debug: tracer la conversion DTO -> entite.
- Debug: verifier les erreurs 400/422.

## 10. Bonus (avance)
- Ajouter un controle de duplication par nom.
- Ajouter une politique de creation par role.
