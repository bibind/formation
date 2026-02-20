# Upload image produit

## Contexte et objectif
Documenter l'endpoint d'upload image produit avec contraintes techniques, exemples concrets et preuves audit.

## Endpoint
- URL: `/api/products/{id}/image`
- Methode: `POST`
- Auth: JWT requis
- Roles: `ROLE_USER` + Voter `PRODUCT_EDIT` sur le produit cible

## Schema request multipart/form-data
- `file` (type: `binary`, requis): fichier image a associer au produit.

Exemple representation (OpenAPI):
```yaml
multipart/form-data:
  schema:
    type: object
    properties:
      file:
        type: string
        format: binary
    required: [file]
```

## Exemples curl
Succes:
```bash
curl -X POST \
  -H "Authorization: Bearer <token>" \
  -F "file=@/path/to/image.png" \
  https://api.example.test/api/products/{id}/image
```

Echec (MIME non supporte):
```bash
curl -X POST \
  -H "Authorization: Bearer <token>" \
  -F "file=@/path/to/bad.txt" \
  https://api.example.test/api/products/{id}/image
```

## Exemples reponses JSON
Succes (implementation actuelle):
- 204 No Content (corps vide)

Succes (200/201 si output active):
```json
{
  "id": "prod_123",
  "name": "Poster",
  "categoryName": "Posters",
  "stock": 3,
  "price": 5000,
  "imageUrl": "/uploads/products/abc123.png"
}
```

Erreur 400 (MIME invalide):
```json
{
  "@context": "/api/contexts/Error",
  "@type": "Error",
  "title": "An error occurred",
  "detail": "image_invalid_mime",
  "status": 400
}
```

Erreur 401 (JWT manquant ou invalide):
```json
{
  "code": 401,
  "message": "JWT Token not found"
}
```

Erreur 403 (Voter refuse l'edition):
```json
{
  "@context": "/api/contexts/Error",
  "@type": "Error",
  "title": "An error occurred",
  "detail": "product_edit_forbidden",
  "status": 403
}
```

Erreur 415 (mauvais Content-Type):
```json
{
  "@context": "/api/contexts/Error",
  "@type": "Error",
  "title": "An error occurred",
  "detail": "Unsupported Media Type",
  "status": 415
}
```

Erreur 422 (id invalide ou ressource introuvable):
```json
{
  "@context": "/api/contexts/ConstraintViolation",
  "@type": "ConstraintViolation",
  "title": "An error occurred",
  "status": 422
}
```

## Contraintes
- MIME autorises: `image/jpeg`, `image/png`, `image/webp`.
- Taille max: 2 Mo.
- Nommage: `Vich\\UploaderBundle\\Naming\\SmartUniqueNamer`.
- Stockage: `public/uploads/products` (URL ` /uploads/products`).

## Notes techniques
- `deserialize: false` pour lire le fichier via `Request::files`.
- VichUploader mapping `product_images` + champ `imageName`.
- Le setter `setImageName` est requis pour la persistence Vich.

## Checklist debug
- Verifier le `Content-Type` et la presence du champ `file`.
- Verifier les MIME autorises et la taille reelle du fichier.
- Confirmer le `Voter` `PRODUCT_EDIT` et le token JWT.
- Verifier `public/uploads/products` et les droits d'ecriture.
- Consulter `project/docs/openapi.json` pour la definition.

## Tests associes
- `test_upload_image_ok`
- `test_upload_image_fails_wrong_mime_or_too_large`

## Preuves audit
- Commandes:
  - `APP_ENV=test php /tmp/openapi_http_export.php`
  - `make test`
- Fichiers generes:
  - `project/docs/openapi.json`
  - `project/docs/upload.md`
