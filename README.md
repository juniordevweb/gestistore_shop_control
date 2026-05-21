# 🛍️ GestiStore - Système de Vente Multi-Produits

## 📌 Vue d'Ensemble

Votre système de vente a été transformé d'un modèle **1 produit = 1 vente** vers un modèle **panier dynamique professionnel** permettant plusieurs produits par vente.

---

## ✨ Nouvelles Fonctionnalités

### 1. **Panier Dynamique** 🛒
- Ajouter plusieurs produits en une vente
- Modifier quantités (+/- boutons)
- Modifier prix unitaire à la volée
- Supprimer articles du panier
- Calcul automatique des totaux
- Recherche produit en temps réel

### 2. **Gestion Multi-Produit**
- Une facture = N produits
- Table `sale_items` pour les articles
- Historique complet par produit
- Traçabilité prix unitaire personnalisé

### 3. **Historique Ventes**
- Liste complète des ventes
- Filtrage par client/date/montant
- Accès aux détails de chaque facture
- Statistiques par vente

### 4. **Détail Vente**
- Affiche tous les produits achetés
- Prix unitaire et sous-total
- Récapitulatif financier
- Prêt pour impression/PDF

---

## 🚀 Démarrage Rapide

### 1. **Exécuter les Migrations**
```bash
php spark migrate
```

Cette commande va :
- ✅ Refactoriser la table `sales`
- ✅ Créer la table `sale_items`
- ✅ Migrer vos données existantes

### 2. **Tester le Panier**
Allez à : `http://localhost/gestistore/sales/create`

### 3. **Consulter l'Historique**
Allez à : `http://localhost/gestistore/sales/list`

---

## 📁 Fichiers Créés/Modifiés

| Chemin | Type | Statut |
|--------|------|--------|
| `app/Database/Migrations/20260513000000_RefactorSalesTable.php` | Migration | ✅ Nouvelle |
| `app/Database/Migrations/20260513010000_CreateSaleItemsTable.php` | Migration | ✅ Nouvelle |
| `app/Models/SalesItemModel.php` | Modèle | ✅ Nouveau |
| `app/Models/SalesModel.php` | Modèle | 🔄 Modifié |
| `app/Controllers/Sales.php` | Contrôleur | 🔄 Refactorisé |
| `app/Views/V_sales.php` | Vue | 🔄 Refactorisée |
| `app/Views/V_sales_list.php` | Vue | ✅ Nouvelle |
| `app/Views/V_sales_detail.php` | Vue | ✅ Nouvelle |
| `app/Config/Routes.php` | Routes | 🔄 Mise à jour |

---

## 📚 Documentation

### 📖 Guides Principaux

1. **[IMPLEMENTATION_GUIDE.md](./IMPLEMENTATION_GUIDE.md)** 📋
   - Étapes déploiement
   - Structure tables
   - Flux technique
   - Tests recommandés

2. **[API_DOCUMENTATION.md](./API_DOCUMENTATION.md)** 🔌
   - Tous les endpoints AJAX
   - Format requêtes/réponses
   - Exemples JavaScript
   - Erreurs courantes

3. **[ARCHITECTURE.md](./ARCHITECTURE.md)** 🏗️
   - Diagrammes de flux
   - Relations BDD
   - Structure fichiers
   - Performance & scalabilité

4. **[TROUBLESHOOTING.md](./TROUBLESHOOTING.md)** 🐛
   - Cas d'usage courants
   - Dépannage
   - Optimisations
   - Checklist déploiement

5. **[SQL_EXAMPLES.md](./SQL_EXAMPLES.md)** 📊
   - Requêtes utiles
   - Exemples données
   - Dashboard queries
   - Maintenance

---

## 🎯 Cas d'Usage Principal

**Avant** (ancien système) :
```
Vente #1: Client: Junior | Produit: Huile | Qty: 2 | Prix: 1200 = 2400 FCFA
Vente #2: Client: Junior | Produit: Riz | Qty: 1 | Prix: 5000 = 5000 FCFA
Vente #3: Client: Junior | Produit: Sucre | Qty: 3 | Prix: 1000 = 3000 FCFA
Total: 3 ventes
```

**Après** (nouveau système) :
```
Vente #123: Client: Junior | Mode: Espèces | Total: 10400 FCFA
├─ Huile x2 @ 1200 = 2400
├─ Riz x1 @ 5000 = 5000
└─ Sucre x3 @ 1000 = 3000
Total: 1 vente facture = professionnelle
```

---

## 🚨 Points d'Attention

### ⚠️ Avant Déploiement Production

1. **Backup BDD** 📦
   - Sauvegarder données actuelles

2. **Tester Migrations** 🧪
   - Exécuter sur copie dev
   - Vérifier données migrées

3. **Tests Complets** ✅
   - Panier simple & multiple
   - Crédit/Dette
   - Historique & détails
   - Mobile responsiveness

4. **Vérifier logs** 📋
   - Pas d'erreurs dans `writable/logs/`

5. **Performance** ⚡
   - < 2s chargement panier
   - < 200ms par action AJAX

---

## 🎉 Résumé

✅ **Système refactorisé** avec panier dynamique  
✅ **Multi-produits par vente** (professionnelle)  
✅ **UI/UX moderne** (mobile-first, dark mode)  
✅ **Code propre** (Models, Controllers, Views séparés)  
✅ **Documentation complète** (5 guides)  
✅ **Prêt pour production** (après tests)  

---

**Version** : 1.0  
**Date** : 13 May 2026  
**Statut** : ✅ **Prêt pour Déploiement**

---

## 🎯 Next Action

```bash
1. php spark migrate           # Exécuter migrations
2. Test /sales/create          # Tester panier
3. Test /sales/list            # Tester historique
4. Deploy en production         # Deployer
```

🚀 **Bon déploiement!**

## Server Requirements

PHP version 8.2 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - The end of life date for PHP 8.1 was December 31, 2025.
> - If you are still using below PHP 8.2, you should upgrade immediately.
> - The end of life date for PHP 8.2 will be December 31, 2026.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library
