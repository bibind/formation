# Atelier 10 — Integration finale

## 1. Enjeux (Pourquoi on le fait en production)
- Enjeux techniques: valider l'ensemble du flux Marketplace.
- Enjeux qualite: garantir la coherence des contraintes metier.
- Risques si mal fait: API partiellement fonctionnelle.
- Temps estime: 15 min.

## 2. Objectifs pedagogiques (Bloom)
- Comprendre: dependances entre DTO, Providers, Processors, Voters.
- Appliquer: assembler toutes les briques.
- Analyser: verifier la coherence globale.
- Evaluer: tests end-to-end.
- Creer: checklist de validation finale.
- Temps estime: 15 min.

## 3. Pre-requis
- Ateliers precedents: 01 a 09.
- Etat du code attendu: tests API complets, upload OK, OpenAPI exporte.
- Temps estime: 5 min.

## 4. Contexte metier
- User story: une marketplace complete, de l'ajout produit a la commande.
- Regles metier: isolation par user, stock correct, upload image valide.
- Temps estime: 10 min.

## 5. Pratique guidee (demo formateur)
- Etape 1: verifier endpoints via `project/docs/endpoints.md`.
- Etape 2: executer `make openapi` et `make test`.
- Etape 3: analyser les logs et ajuster si besoin.
- Temps estime: 45 min.

Extrait de code cible:
```bash
make openapi
make test
```

## 6. Application (travail stagiaire)
- Checklist:
  - Lancer `make ci`.
  - Executer un flux complet (create product -> upload -> order).
  - Verifier la coherence du stock.
- Livrables attendus:
  - Rapport de tests OK.
  - OpenAPI exporte.
- Definition of Done:
  - `make test` passe.
  - OpenAPI contient l'endpoint upload.
- Temps estime: 45 min.

## 7. Analyse / Debrief (questions + variations)
- Questions:
  - Quels points restent sensibles en prod?
  - Comment industrialiser le pipeline CI?
- Variantes:
  - Ajout d'une file d'attente pour l'upload.
- Recommandations:
  - Formaliser la checklist release.
- Temps estime: 20 min.

## 8. Tests & validation
- Tests a ecrire (ApiTestCase):
  - Reprise de l'ensemble des tests du projet.
- Scenarios:
  - Happy path complet.
  - Error path sur stock insuffisant.
- Exemple curl:
```bash
curl -X POST \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{"lines":[{"productId":"prod_123","quantity":2}]}' \
  https://api.example.test/api/orders
```
- Exemple JSON (reponse):
```json
{ "id": "ord_123", "total": 3000, "lines": [ { "productId": "prod_123", "quantity": 2 } ] }
```
- Assertions attendues:
  - Codes 200/201/204 selon operation.
- Preuves audit:
  - `make test` OK.
  - `project/docs/openapi.json` present.
- Temps estime: 25 min.

## 9. Pieges frequents & Debug tips
- Ignorer les dependances entre ateliers.
- Oublier l'auth JWT dans les requetes.
- Ne pas verifier la coherence stock.
- Debug: utiliser `project/docs/upload.md` pour l'endpoint upload.
- Debug: relire `project/docs/endpoints.md`.

## 10. Bonus (avance)
- Ajout d'un workflow CI complet.
- Ajout de tests de charge simples.
