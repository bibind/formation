# Plan detaille 5 jours – API Platform Avance – Projet Marketplace

## Objectifs
- Decomposer la progression pedagogique sur 5 jours.
- Garantir la couverture des contraintes techniques et metier.

## Contexte
Chaque jour combine apports techniques, ateliers progressifs et consolidation via corriges et evaluation.

## Jour 1 – Architecture API Platform avancee et DTO
### Objectifs
- Comprendre le role des DTO comme ressources publiques.
- Mettre en place le mapping DTO ↔ Entites.
- Implementer un premier State Provider.
### Contenu
- Design des ressources DTO (Product, Category).
- Premier Provider de collection.
- Validation basique.
### Livrables
- DTO de sortie, Provider collection.

## Jour 2 – State Processors et regles metier
### Objectifs
- Implementer Processors pour creation/modification.
- Appliquer les regles metier (stock, isolation).
### Contenu
- Processor create Product.
- Processor create Order avec OrderLine.
- Validation avancee.
### Livrables
- Processors et services metier.

## Jour 3 – Securite avancee et filtres
### Objectifs
- Mettre en place JWT et Voters.
- Appliquer security et securityPostDenormalize.
- Ajouter filtres standard + custom.
### Contenu
- Voter ProductVoter.
- Filtre Search/Order + filtre custom "owner".
### Livrables
- Voter, rules d'acces, filtres.

## Jour 4 – Performance, upload, tests
### Objectifs
- Optimiser N+1 avec eager loading.
- Upload via VichUploader.
- Tests API ApiTestCase.
### Contenu
- Provider avec joins et pagination.
- Endpoint upload image produit.
- Tests de scenarii.
### Livrables
- Endpoint upload, tests.

## Jour 5 – Industrialisation et etude de cas
### Objectifs
- Stabiliser l'architecture.
- Passer une evaluation finale.
- Produire l'etude de cas.
### Contenu
- Revue des normes et Definition of Done.
- Etude de cas finale.
- QCM sortie.
### Livrables
- Etude de cas, evaluation finale.

## Contraintes
- DTO uniquement, providers/processors personnalises.
- JWT, Voters, filtres, N+1, upload, tests ApiTestCase.

## Criteres de reussite
- Chaque jour produit un livrable concret.
- Progression logique vers l'etude de cas finale.

## Pieges frequents
- Faire un processor sans transaction.
- Oublier le controle des droits sur les operations.

## Preuves audit possibles
- Planning detaille versionne.
- Traces d'ateliers par jour.
