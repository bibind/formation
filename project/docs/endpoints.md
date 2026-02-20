# Endpoints API – Marketplace

## Objectifs
- Documenter les endpoints clefs et contraintes.
- Ajouter l'upload image avec multipart/form-data.

## Contexte
Ressources exposees via DTO (entites non exposees).

## Produits
### GET /api/products
- Collection avec filtres (name, category, owner=current).

### POST /api/products
- Creation d'un produit (owner courant).

Payload exemple:
```json
{
  "name": "T-shirt",
  "categoryId": "cat_123",
  "stock": 12,
  "price": 2500
}
```

### POST /api/products/{id}/image
- URL: `/api/products/{id}/image`
- Methode: `POST`
- Auth: JWT requis
- Roles: `ROLE_USER` (avec Voter `PRODUCT_EDIT` sur le produit)
- Upload image produit (multipart/form-data).
- Controle MIME: image/jpeg, image/png, image/webp.
- Taille max: 2 Mo.

Curl exemple:
```bash
curl -X POST \
  -H "Authorization: Bearer <token>" \
  -F "file=@/path/to/image.png" \
  https://api.example.test/api/products/{id}/image
```

Exemple reponse JSON (erreur 400):
```json
{
  "@context": "/api/contexts/Error",
  "@type": "Error",
  "title": "An error occurred",
  "detail": "image_invalid_mime",
  "status": 400
}
```

Erreurs possibles:
- 400: `file_missing`, `image_invalid_mime`, `image_too_large`
- 415: format non supporte (multipart requis)
- 422: id invalide ou entite non trouvable par l'operation

Notes techniques:
- `deserialize: false` pour lire le fichier depuis `Request::files`.
- VichUploader mappe `product_images` vers `public/uploads/products` et persiste `imageName`.

## Commandes
### POST /api/orders
- Creation de commande, stock decrement.

Payload exemple:
```json
{
  "lines": [
    { "productId": "prod_123", "quantity": 2 }
  ]
}
```

## Contraintes
- DTO uniquement exposes.
- securityPostDenormalize et Voters obligatoires.

## Criteres de reussite
- Endpoints conformes aux regles metier et tests passants.

## Pieges frequents
- Oublier la validation MIME des fichiers.

## Preuves audit possibles
- Documentation endpoints versionnee.
- Export OpenAPI: `project/docs/openapi.json`.
