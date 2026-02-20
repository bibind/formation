# README livrables

## Objectifs
- Centraliser les hypotheses et conventions de production des livrables.
- Assurer la coherence et la tracabilite des choix pedagogiques et techniques.

## Contexte
La formation "API Platform Avance – Projet Marketplace" couvre un projet fil rouge Marketplace pour des developpeurs Symfony confirmes.

## Hypotheses
- Les stagiaires connaissent Symfony, Doctrine, JWT, et les bases d'API Platform.
- L'environnement de demo utilise PHP 8.2, Symfony 6.4, API Platform 3.x, Doctrine ORM, VichUploader, LexikJWT.
- Les exemples de code sont fournis a titre pedagogique et peuvent necessiter ajustements minimes.
- Les ateliers sont concus pour un rythme moyen de groupe (35h sur 5 jours).
- La plateforme d'execution dispose d'une base de donnees PostgreSQL.

## Conventions
- Tous les fichiers sont en Markdown H1/H2/H3.
- Chaque livrable inclut objectifs, contexte, contraintes, criteres de reussite, pieges frequents, preuves audit possibles.
- Les ateliers indiquent un temps estime.
- Les corriges incluent Definition of Done, commandes utiles et extraits realistes.

## Contraintes
- Aucun expose d'entite Doctrine en ressource API.
- DTO, State Providers et State Processors obligatoires.
- Securite avancee (security, securityPostDenormalize, Voter) et JWT.
- Filtres standards + filtre custom.
- Optimisation N+1.
- Upload image produit via endpoint dedie.
- Tests ApiTestCase.
- Validation metier avancee.

## Criteres de reussite
- Tous les livrables respectent le plan demande et la structure.
- Alignement objectifs Bloom ↔ ateliers ↔ evaluation.
- Projet marketplace conforme aux contraintes metier.

## Pieges frequents
- Oublier d'isoler les donnees par utilisateur.
- Exposer une entite Doctrine en API.
- Melanger logique metier et logique d'infrastructure dans les Processors.

## Preuves audit possibles
- Arborescence complete des livrables.
- Trace d'alignement (objectifs, ateliers, evaluations).
- Fichiers corriges avec Definition of Done.
