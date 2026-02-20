# Atelier 01 — DTO Product et Category + mapping

## 1. Enjeux (Pourquoi on le fait en production)
- Enjeux techniques: decoupler la couche API des entites Doctrine pour eviter les fuites de modele interne.
- Enjeux securite/maintenabilite: controler exactement les champs exposes et les champs ecrits.
- Risques si mal fait: exposition involontaire de donnees sensibles, evolution du modele plus couteuse.
- Temps estime: 15 min.

## 2. Objectifs pedagogiques (Bloom)
- Comprendre: distinguer entite Doctrine vs DTO expose.
- Appliquer: definir ProductOutput et CategoryOutput en ApiResource.
- Analyser: verifier la coherence des champs exposes selon les besoins metier.
- Evaluer: valider la serialisation et les formats via OpenAPI.
- Creer: mettre en place un mapping clair depuis l'entite vers le DTO.
- Temps estime: 15 min.

## 3. Pre-requis
- Ateliers precedents necessaires: aucun (atelier d'entree).
- Etat du code attendu: projet Symfony/API Platform initialise, endpoints docs dispo dans `project/docs/endpoints.md`.
- Temps estime: 5 min.

## 4. Contexte metier
- User story: en tant qu'utilisateur, je veux lister les produits et leur categorie sans exposer les entites internes.
- Regles metier: seules les infos utiles au catalogue sont exposees.
- Temps estime: 10 min.

## 5. Pratique guidee (demo formateur)
- Etape 1: creer les DTO `ProductOutput` et `CategoryOutput` dans `project/src/Application/Dto/`.
- Etape 2: declarer les ApiResource (GET collection) et pointer un Provider.
- Etape 3: mapper `categoryName` depuis l'entite `Category`.
- Temps estime: 45 min.

Extrait de code cible (DTO):
```php
final class ProductOutput
{
    public string $id;
    public string $name;
    public string $categoryName;
    public int $stock;
    public int $price;
}
```

Commandes utiles:
```bash
php project/bin/console doctrine:migrations:migrate
```

## 6. Application (travail stagiaire)
- Checklist:
  - Creer `ProductOutput` et `CategoryOutput` avec les champs exposes.
  - Configurer l'ApiResource GET collection.
  - Mettre en place le mapping depuis l'entite.
- Livrables attendus:
  - `project/src/Application/Dto/ProductOutput.php`
  - `project/src/Application/Dto/CategoryOutput.php`
- Definition of Done:
  - Les DTO sont exposes en GET collection.
  - Aucun champ d'entite interne n'est expose.
- Temps estime: 45 min.

## 7. Analyse / Debrief (questions + variations)
- Questions:
  - Pourquoi eviter d'exposer les entites directement?
  - Quels champs doivent rester internes?
- Variantes:
  - DTO distincts pour lecture vs ecriture.
- Recommandations:
  - Garder un DTO minimal et stable.
- Temps estime: 20 min.

## 8. Tests & validation
- Tests a ecrire (ApiTestCase):
  - Liste produits retourne les champs attendus.
- Scenarios:
  - Happy path: GET /api/products retourne `id`, `name`, `categoryName`.
  - Error path: aucun.
- Exemple curl:
```bash
curl -X GET https://api.example.test/api/products
```
- Exemple JSON (reponse):
```json
[
  {\"id\":\"prod_123\",\"name\":\"T-shirt\",\"categoryName\":\"Clothes\",\"stock\":12,\"price\":2500}
]
```
- Assertions attendues:
  - Code 200.
  - Champs exposes uniquement.
- Preuves audit:
  - OpenAPI exporte (`project/docs/openapi.json`).
  - Test correspondant dans `project/tests/Api/MarketplaceApiTest.php`.
- Temps estime: 25 min.

## 9. Pieges frequents & Debug tips
- Oublier `categoryName` et exposer `category` directement.
- Confondre DTO output et input.
- Renvoyer des entites dans le Provider.
- Debug: verifier les schemas OpenAPI.
- Debug: inspecter la sortie JSON brute.

## 10. Bonus (avance)
- Ajouter un champ `imageUrl` derive de `imageName`.
- Ajouter des groupes de serialisation si besoin.
