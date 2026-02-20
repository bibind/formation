# Etude de cas finale – API Marketplace

## Objectifs
- Valider la capacite a construire une API Marketplace complete.
- Evaluer DTO, Providers, Processors, securite, tests.

## Contexte
Etude finale sur 1 demi-journee, individualisee ou en binome.

## Enonce
Construire les endpoints suivants via DTO :
- Produits (collection, creation, upload image).
- Commandes (creation + consultation).
- Categories (lecture).

## Contraintes
- DTO uniquement exposes.
- Providers et Processors personnalises.
- Voter pour edition produit avec admin override.
- Filtre custom owner.
- Optimisation N+1.
- Tests ApiTestCase (min 6 tests).

## Criteres de reussite
- API fonctionnelle, tests verts.
- Respect strict des regles metier.

## Pieges frequents
- Oublier isolation des donnees par user.

## Preuves audit possibles
- Depots code et rapport de tests.
