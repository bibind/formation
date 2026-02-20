# Ateliers progressifs – API Platform Avance – Projet Marketplace

## Objectifs
- Mettre en oeuvre les contraintes techniques du projet Marketplace.
- Construire une progression du simple au complexe.

## Contexte
Ateliers realises sur 5 jours, alignes avec les objectifs Bloom et la planification demi-journee.

## Atelier 1 – DTO Product/Category + mapping
### Objectifs
- Definir les DTO de sortie et d'entree.
- Configurer la serialization.
### Contexte
Premiere exposition de ressources DTO sans entites.
### Contraintes
- Interdire toute exposition directe d'entite Doctrine.
### Criteres de reussite
- DTO ProductOutput et CategoryOutput exposes en GET.
### Temps estime
- 2h
### Extrait de code
```php
final class ProductOutput
{
    public string $id;
    public string $name;
    public string $categoryName;
}
```
### Pieges frequents
- Oublier le mapping du nom de categorie.
### Preuves audit possibles
- DTO committe + config api_resources.

## Atelier 2 – Provider collection Product
### Objectifs
- Implementer un Provider de collection.
- Ajouter pagination et tri.
### Contexte
Lecture de collection avec DTO.
### Contraintes
- Utiliser Doctrine avec joins pour limiter N+1.
### Criteres de reussite
- Collection ProductOutput listee avec pagination.
### Temps estime
- 2h
### Extrait de code
```php
$qb = $this->productRepository->createQueryBuilder('p')
    ->leftJoin('p.category', 'c')
    ->addSelect('c');
```
### Pieges frequents
- Oublier addSelect et generer N+1.
### Preuves audit possibles
- Test de collection et logs SQL.

## Atelier 3 – Processor ProductCreate
### Objectifs
- Creer un Processor de creation.
- Valider les contraintes metier.
### Contexte
Creation d'un produit par utilisateur.
### Contraintes
- Isolation des donnees par utilisateur.
### Criteres de reussite
- Produit cree avec owner = user courant.
### Temps estime
- 2h
### Extrait de code
```php
$product->setOwner($user);
$this->validator->validate($product);
```
### Pieges frequents
- Ne pas gerer les erreurs de validation.
### Preuves audit possibles
- Scenario test ApiTestCase.

## Atelier 4 – Processor OrderCreate + decrement stock
### Objectifs
- Creer une commande et decrementer le stock.
- Gerer les transactions.
### Contexte
Processus metier critique.
### Contraintes
- Stock ne peut pas devenir negatif.
### Criteres de reussite
- Stock decrementé et commande validee.
### Temps estime
- 3h
### Extrait de code
```php
if ($product->getStock() < $line->quantity) {
    throw new DomainException('stock_insufficient');
}
$product->decrementStock($line->quantity);
```
### Pieges frequents
- Absence de transaction ou lock optimiste.
### Preuves audit possibles
- Test concurrentiel simple et logs.

## Atelier 5 – Voter ProductVoter + security rules
### Objectifs
- Ajouter un Voter.
- Appliquer security et securityPostDenormalize.
### Contexte
Controle d'acces fin.
### Contraintes
- Admin override autorise.
### Criteres de reussite
- Un user ne modifie que ses produits.
### Temps estime
- 2h
### Extrait de code
```php
return $this->security->isGranted('ROLE_ADMIN') || $product->getOwner() === $user;
```
### Pieges frequents
- Confondre security vs securityPostDenormalize.
### Preuves audit possibles
- Tests droits user/admin.

## Atelier 6 – Filtres standards + filtre custom owner
### Objectifs
- Activer Search/Order filters.
- Creer un filtre custom "owner".
### Contexte
Recherche et securisation des collections.
### Contraintes
- Filtre custom applique l'isolation.
### Criteres de reussite
- Collection filtree par owner et search.
### Temps estime
- 2h
### Extrait de code
```php
$queryBuilder->andWhere('p.owner = :owner')->setParameter('owner', $user);
```
### Pieges frequents
- Laisser passer un owner fourni par le client.
### Preuves audit possibles
- Documentation des filtres.

## Atelier 7 – Optimisation N+1
### Objectifs
- Identifier et corriger N+1.
- Ajuster providers et repositories.
### Contexte
Optimiser la lecture des commandes.
### Contraintes
- Utiliser fetch joins et pagination.
### Criteres de reussite
- Reduction du nombre de requetes.
### Temps estime
- 2h
### Extrait de code
```php
$qb->leftJoin('o.lines', 'l')->addSelect('l');
```
### Pieges frequents
- Charger trop d'associations sans besoin.
### Preuves audit possibles
- Profiling SQL avant/apres.

## Atelier 8 – Upload image produit (VichUploader)
### Objectifs
- Implementer l'upload via endpoint dedie.
- Gerer validation et securite.
### Contexte
Upload d'image de produit.
### Contraintes
- Endpoint dedie, DTO upload.
### Criteres de reussite
- Image stockee et lien dans ProductOutput.
### Temps estime
- 2h
### Extrait de code
```php
#[Vich\UploadableField(mapping: 'product_images', fileNameProperty: 'imageName')]
public ?File $imageFile = null;
```
### Pieges frequents
- Oublier la validation de type MIME.
### Preuves audit possibles
- Test d'upload et fichier stocke.

## Atelier 9 – Tests ApiTestCase
### Objectifs
- Couvrir les endpoints principaux.
- Automatiser les scenarii.
### Contexte
Qualite logicielle.
### Contraintes
- Utiliser ApiTestCase et fixtures.
### Criteres de reussite
- Tests verts pour collection, create, upload, order.
### Temps estime
- 2h
### Extrait de code
```php
$this->assertResponseStatusCodeSame(201);
```
### Pieges frequents
- Oublier l'auth JWT dans les tests.
### Preuves audit possibles
- Rapport de tests.

## Atelier 10 – Integration finale
### Objectifs
- Mettre en oeuvre le projet complet.
- Passer l'evaluation finale.
### Contexte
Etude de cas finale.
### Contraintes
- Respect strict des regles metier et securite.
### Criteres de reussite
- API Marketplace operationnelle et testee.
### Temps estime
- 3h
### Extrait de code
```php
# Validation finale via tests end-to-end
```
### Pieges frequents
- Focaliser sur une feature au detriment des tests.
### Preuves audit possibles
- Demo fonctionnelle et commits.
