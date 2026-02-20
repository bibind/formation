# Architecture projet – API Marketplace

## Objectifs
- Definir l'architecture en couches (Domain, Application, Infrastructure).
- Garantir l'usage de DTO, Providers, Processors, Voters.

## Contexte
Architecture Symfony decouplee pour API Platform avancee avec regles metier fortes.

## Couches
### Domain
- Entites internes : User, Product, Category, Order, OrderLine.
- Regles metier pures (stock, ownership, validation).

### Application
- DTO d'entree/sortie.
- State Providers et Processors.
- Services applicatifs (factories, mappers).
- Voters pour authorisation.

### Infrastructure
- Doctrine repositories.
- Integrations (VichUploader, JWT, persistance).

## Flux principal
1. Requete API sur DTO.
2. Provider (lecture) ou Processor (ecriture).
3. Mapping vers Domain, validation, persistence.
4. Retour DTO.

## Contraintes
- DTO comme ressources publiques uniques.
- Isolation des donnees par utilisateur.
- Admin override.

## Criteres de reussite
- Separation nette Domain/Application/Infrastructure.
- Aucun acces direct aux entites via l'API.

## Extrait de mapping (schema)
```text
DTO Input -> Processor -> Domain Entity -> Repository -> DTO Output
```

## Pieges frequents
- Mettre de la logique metier dans les controllers.

## Preuves audit possibles
- Diagramme simple et classes de reference.
