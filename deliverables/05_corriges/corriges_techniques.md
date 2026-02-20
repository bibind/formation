# Corriges techniques – API Platform Avance – Projet Marketplace

## Objectifs
- Fournir des corriges structures et exploitables.
- Illustrer DTO, Provider, Processor, Voter, Filter, Upload.

## Contexte
Ces corriges correspondent aux ateliers progressifs et servent de reference technique.

## Corrige 1 – DTO ProductOutput et ProductInput
### Extrait de code
```php
namespace App\Application\Dto;

final class ProductOutput
{
    public string $id;
    public string $name;
    public string $categoryName;
    public int $stock;
    public ?string $imageUrl;
}

final class ProductInput
{
    public string $name;
    public string $categoryId;
    public int $stock;
}
```

## Corrige 2 – Provider ProductCollectionProvider
### Extrait de code
```php
namespace App\Application\State\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

final class ProductCollectionProvider implements ProviderInterface
{
    public function __construct(private ProductRepository $repo) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): iterable
    {
        return $this->repo->fetchCollectionWithCategory($context);
    }
}
```

## Corrige 3 – Processor ProductCreateProcessor
### Extrait de code
```php
namespace App\Application\State\Processor;

use ApiPlatform\State\ProcessorInterface;

final class ProductCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private ProductFactory $factory,
        private EntityManagerInterface $em,
        private Security $security,
        private ValidatorInterface $validator,
    ) {}

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $user = $this->security->getUser();
        $product = $this->factory->fromInput($data, $user);
        $this->validator->validate($product);
        $this->em->persist($product);
        $this->em->flush();
        return $product;
    }
}
```

## Corrige 4 – Processor OrderCreateProcessor (stock)
### Extrait de code
```php
if ($product->getStock() < $line->quantity) {
    throw new DomainException('stock_insufficient');
}
$product->decrementStock($line->quantity);
```

## Corrige 5 – Voter ProductVoter
### Extrait de code
```php
final class ProductVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, ['PRODUCT_EDIT', 'PRODUCT_VIEW'], true) && $subject instanceof Product;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) { return false; }
        return $user->isAdmin() || $subject->getOwner() === $user;
    }
}
```

## Corrige 6 – Filtre custom OwnerFilter
### Extrait de code
```php
final class OwnerFilter extends AbstractFilter
{
    protected function filterProperty(string $property, $value, QueryBuilder $qb, QueryNameGeneratorInterface $qng, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        if ($property !== 'owner' || $value !== 'current') {
            return;
        }
        $qb->andWhere('o.owner = :owner')->setParameter('owner', $context['user']);
    }
}
```

## Corrige 7 – Upload VichUploader
### Extrait de code
```php
final class ProductImageUploadInput
{
    public ?UploadedFile $file = null;
}

#[Vich\Uploadable]
class Product
{
    #[Vich\UploadableField(mapping: 'product_images', fileNameProperty: 'imageName')]
    public ?File $imageFile = null;
}
```

## Implementation finale – OrderCreateProcessor
### Extrait de code
```php
$productsById = $this->productRepository->findByIdsForUpdate($productIds, LockMode::PESSIMISTIC_WRITE);
foreach ($data->lines as $line) {
    $product = $productsById[$line['productId']] ?? null;
    if (!$product instanceof Product) {
        throw new DomainException('product_not_found');
    }
    $product->decrementStock($line['quantity']);
}
$order = $this->factory->fromInput($data, $user, $productsById);
$order->validate();
$this->em->persist($order);
$this->em->flush();
```
### Exemple de payload
```json
{
  "lines": [
    { "productId": "prod_123", "quantity": 2 },
    { "productId": "prod_456", "quantity": 1 }
  ]
}
```

## Implementation finale – Upload image produit
### URL / Methode / Auth
- URL: `/api/products/{id}/image`
- Methode: `POST`
- Auth: JWT requis
- Roles: `ROLE_USER` + Voter `PRODUCT_EDIT`

### Extrait de code
```php
$file = $request->files->get('file');
if ($file === null) {
    throw new BadRequestHttpException('file_missing');
}
if ($file->getSize() !== null && $file->getSize() > 2 * 1024 * 1024) {
    throw new BadRequestHttpException('image_too_large');
}
$allowed = ['image/jpeg', 'image/png', 'image/webp'];
$clientMime = $file->getClientMimeType();
$mime = $file->getMimeType() ?? '';
if (!in_array((string) $clientMime, $allowed, true) && !in_array($mime, $allowed, true)) {
    throw new BadRequestHttpException('image_invalid_mime');
}
$product->setImageFile($file);
$this->em->flush();
```

### Contraintes et erreurs possibles
- MIME: `image/jpeg`, `image/png`, `image/webp`
- Taille max: 2 Mo
- 400: `file_missing`, `image_invalid_mime`, `image_too_large`
- 415: format non supporte (multipart requis)
- 422: id invalide / ressource introuvable

### Exemple curl multipart
```bash
curl -X POST \\
  -H "Authorization: Bearer <token>" \\
  -F "file=@/path/to/image.png" \\
  https://api.example.test/api/products/{id}/image
```

### Exemple reponse JSON (erreur)
```json
{
  "@context": "/api/contexts/Error",
  "@type": "Error",
  "title": "An error occurred",
  "detail": "image_invalid_mime",
  "status": 400
}
```

### Notes techniques
- `deserialize: false` et lecture du fichier via `Request::files`.
- VichUploader: mapping `product_images`, `imageName`, stockage `public/uploads/products`.

## Commandes utiles
```bash
composer require api symfony/validator symfony/security-bundle
composer require vich/uploader-bundle lexik/jwt-authentication-bundle
php bin/console doctrine:migrations:migrate
php bin/phpunit
```

## Definition of Done
- DTO exposes et verifies en GET/POST.
- Providers et Processors testables et couverts par tests.
- Securite validee (user vs admin).
- Filtres et performance verifies.
- Upload image fonctionnel.
- Tests d'upload et de commandes passants.

## Contraintes
- DTO uniquement expose.
- Processors et Providers personnalises.
- JWT et Voters obligatoires.

## Criteres de reussite
- Corriges executables avec ajustements minimes.

## Pieges frequents
- Utiliser un entity manager dans un Provider pour ecrire.

## Preuves audit possibles
- Captures de tests et build.
- Traces d'appels curl (order create, upload).
- Capture Swagger attendue: operation `POST /api/products/{id}/image` avec `multipart/form-data`, 204 en succes.
- Tests associes: `test_upload_image_ok`, `test_upload_image_fails_wrong_mime_or_too_large`.
