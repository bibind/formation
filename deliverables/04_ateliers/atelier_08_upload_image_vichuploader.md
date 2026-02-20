# Atelier 08 — Upload image produit via VichUploader

## 1. Enjeux (Pourquoi on le fait en production)
- Enjeux fonctionnels: associer une image aux produits.
- Enjeux securite: controler MIME et taille.
- Risques si mal fait: injection de fichiers, stockage non controle.
- Temps estime: 15 min.

## 2. Objectifs pedagogiques (Bloom)
- Comprendre: principe VichUploader et endpoint dedie.
- Appliquer: implementer l'upload multipart.
- Analyser: gerer les erreurs de validation.
- Evaluer: valider via tests et OpenAPI.
- Temps estime: 15 min.

## 3. Pre-requis
- Atelier precedent: Atelier 07 (optimisation N+1).
- Etat du code attendu: ProductOutput + endpoint POST /api/products/{id}/image.
- Temps estime: 5 min.

## 4. Contexte metier
- User story: un vendeur ajoute ou remplace une image produit.
- Regles metier: taille max 2 Mo, MIME limite.
- Temps estime: 10 min.

## 5. Pratique guidee (demo formateur)
- Etape 1: DTO upload et operation multipart.
- Etape 2: Processor `ProductImageUploadProcessor`.
- Etape 3: VichUploader mapping `product_images`.
- Temps estime: 60 min.

Extrait de code cible:
```php
$product->setImageFile($file);
$this->em->flush();
```

Commandes utiles:
```bash
php project/bin/console cache:clear
```

## 6. Application (travail stagiaire)
- Checklist:
  - Valider MIME et taille.
  - Activer `deserialize: false`.
  - Verifier le stockage `public/uploads/products`.
- Livrables attendus:
  - `project/src/Application/State/Processor/ProductImageUploadProcessor.php`
  - `project/config/packages/vich_uploader.yaml`
- Definition of Done:
  - Upload OK en multipart.
  - Erreurs 400 sur fichier invalide.
- Temps estime: 60 min.

## 7. Analyse / Debrief (questions + variations)
- Questions:
  - Pourquoi un endpoint dedie?
  - Comment gerer les remplacements d'image?
- Variantes:
  - Stockage sur S3.
- Recommandations:
  - Centraliser la validation MIME dans le Processor.
- Temps estime: 20 min.

## 8. Tests & validation
- Tests a ecrire (ApiTestCase):
  - `test_upload_image_ok`
  - `test_upload_image_fails_wrong_mime_or_too_large`
- Scenarios:
  - Happy path: PNG valide.
  - Error path: MIME non supporte.
- Exemple curl:
```bash
curl -X POST \
  -H "Authorization: Bearer <token>" \
  -F "file=@/path/to/image.png" \
  https://api.example.test/api/products/{id}/image
```
- Exemple JSON (erreur 400):
```json
{
  "@context": "/api/contexts/Error",
  "@type": "Error",
  "title": "An error occurred",
  "detail": "image_invalid_mime",
  "status": 400
}
```
- Assertions attendues:
  - 204 ou 200 selon config.
  - 400 sur MIME invalide.
- Preuves audit:
  - `project/docs/openapi.json` contient l'endpoint upload.
  - Tests associes OK.
- Temps estime: 30 min.

## 9. Pieges frequents & Debug tips
- Oublier `deserialize: false`.
- Absence de `setImageName` sur l'entite.
- Namer VichUploader non configure.
- Debug: verifier `public/uploads/products`.
- Debug: verifier la taille reelle du fichier.

## 10. Bonus (avance)
- Ajouter une suppression d'image.
- Ajouter des variations d'image (thumbnails).
