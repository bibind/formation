# Formation "API Platform Avance – Projet Marketplace"

[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![Symfony](https://img.shields.io/badge/Symfony-7.4-000000?logo=symfony&logoColor=white)](https://symfony.com/)

## Sommaire
- [Version francaise](#-version-francaise)
- [Presentation](#presentation)
- [Projet-marketplace](#projet-marketplace)
- [Architecture-du-depot](#architecture-du-depot)
- [Installation](#installation)
- [Tests--qualite](#tests--qualite)
- [Conformite-pedagogique](#conformite-pedagogique)
- [English-version](#-english-version)
- [Overview](#overview)
- [Marketplace-project](#marketplace-project)
- [Repository-architecture](#repository-architecture)
- [Setup](#setup)
- [Testing--quality](#testing--quality)
- [Training-compliance](#training-compliance)

---

# 🇫🇷 Version francaise

## Presentation
Formation avancee autour d'API Platform et Symfony, structuree pour un parcours intensif de 5 jours. Elle vise des developpeurs PHP/Symfony confirmes avec une bonne maitrise de Doctrine et du JWT.

- Public cible: developpeurs PHP/Symfony confirmes
- Objectifs: architecture API evolutive, securite avancee, performances, tests et qualite
- Positionnement: niveau avance, cas d'usage realiste

## Projet Marketplace
Projet fil rouge "Marketplace" avec des contraintes d'architecture strictes et une logique metier explicite.

- Ressources exposees via DTO (entites non exposees)
- Providers et Processors personnalises
- Securite: JWT, Voters, checks post-denormalization
- Upload image via VichUploader
- Tests API via ApiTestCase
- Documentation OpenAPI exportee (`project/docs/openapi.json`)

## Architecture du depot
- `deliverables/` : livrables pedagogiques (programme, ateliers, corriges, evaluations)
- `project/` : projet Symfony/API Platform et documentation technique
- `project/docs/` : documentation technique (architecture, endpoints, upload, OpenAPI)
- `project/tests/` : tests API et services

## Installation
```bash
make install
make test
make openapi
make ci
```

## Tests & Qualite
- ApiTestCase pour les scenarii metier
- JWT pour l'authentification
- Upload multipart valide et invalide
- Optimisation Doctrine (requete et pagination)

## Conformite pedagogique
- Objectifs Bloom et progression par ateliers
- Matrice de competences
- Livrables audit-ready (preuves, traces, OpenAPI)

---

# 🇬🇧 English Version

## Overview
An advanced training track built around Symfony and API Platform, delivered as a 5-day intensive program. It targets experienced PHP/Symfony developers with solid Doctrine and JWT knowledge.

- Audience: experienced PHP/Symfony developers
- Goals: scalable API architecture, advanced security, performance, testing, and quality
- Level: advanced, real-world project-driven

## Marketplace project
The core project is a Marketplace API with strict architectural rules and explicit business logic.

- DTO-based resources (entities are not exposed)
- Custom Providers and Processors
- Security: JWT, Voters, post-denormalization checks
- Image upload using VichUploader
- API tests with ApiTestCase
- OpenAPI export available at `project/docs/openapi.json`

## Repository architecture
- `deliverables/` : training deliverables (program, labs, solutions, assessments)
- `project/` : Symfony/API Platform project and technical docs
- `project/docs/` : architecture, endpoints, upload guide, OpenAPI export
- `project/tests/` : API tests and related services

## Setup
```bash
make install
make test
make openapi
make ci
```

## Testing & quality
- ApiTestCase covers key business flows
- JWT authentication
- Multipart upload validations
- Doctrine query and pagination tuning

## Training compliance
- Bloom objectives and progressive labs
- Skills matrix for assessment
- Audit-ready evidence (proofs, traces, OpenAPI)
