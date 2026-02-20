# Programme complet – API Platform Avance – Projet Marketplace

## Objectifs
- Concevoir une API decouplee via DTO comme ressources publiques.
- Mettre en oeuvre Providers et Processors personnalises avec regles metier.
- Appliquer une securite avancee (JWT, Voters, security, securityPostDenormalize).
- Optimiser les performances (N+1, filtres, pagination).
- Industrialiser la qualite (validation, tests ApiTestCase).

## Contexte
Formation intensive de 5 jours (35h) pour developpeurs Symfony confirmes. Projet fil rouge : Marketplace avec commandes, stock, upload image, isolation des donnees par utilisateur et override admin.

## Public cible
- Developpeurs PHP/Symfony confirmes.
- Maitrise Doctrine ORM et JWT.

## Prerequis techniques
- PHP 8.2, Symfony 6.4, API Platform 3.x.
- Doctrine ORM, PostgreSQL.
- PHPUnit, ApiTestCase.

## Structure globale (5 jours)
- Jour 1 : Socle API Platform avance + DTO + State Providers.
- Jour 2 : State Processors, validation metier, isolation des donnees.
- Jour 3 : Securite avancee (JWT, Voters) + filtres.
- Jour 4 : Performance (N+1), upload VichUploader, tests API.
- Jour 5 : Industrialisation, audit qualite, etude de cas finale.

## Contraintes
- DTO comme ressources API (aucune entite exposee).
- Providers et Processors personnalises.
- JWT, security, securityPostDenormalize, Voter.
- Filtres standards + un filtre custom.
- Optimisation N+1.
- Upload via VichUploader, endpoint dedie.
- Tests ApiTestCase.
- Validation metier avancee.

## Criteres de reussite
- API conforme aux regles metier (stock, isolation, admin override).
- Exposition exclusivement via DTO.
- Couverture de tests pour les endpoints principaux.
- Dossiers de livrables coherents et complets.

## Extrait de specification technique (exemple)
```yaml
# api_resources.yaml (extrait)
App\Application\Dto\ProductOutput:
  operations:
    ApiPlatform\Metadata\GetCollection:
      provider: App\Application\State\Provider\ProductCollectionProvider
      security: "is_granted('ROLE_USER')"
    ApiPlatform\Metadata\Post:
      processor: App\Application\State\Processor\ProductCreateProcessor
      securityPostDenormalize: "is_granted('PRODUCT_CREATE', object)"
```

## Pieges frequents
- Confondre DTO d'entree et de sortie.
- Oublier la verification de droits dans securityPostDenormalize.
- Laisser des relations Doctrine exposees en serialization.

## Preuves audit possibles
- Programme complet signe et versionne.
- Mapping objectifs ↔ ateliers ↔ evaluation.
- Extraits de code et tests associes.
