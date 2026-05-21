# GUIDE DE DÉPLOIEMENT - Système Multi-Produits avec Panier Dynamique

## 🎯 Résumé des Changements

Votre système de vente a été transformé de **1 produit par vente** à **plusieurs produits par vente** avec un **panier dynamique professionnel**.

---

## 📋 ÉTAPES DE DÉPLOIEMENT

### 1️⃣ Exécuter les Migrations

Accédez à votre terminal et exécutez :

```bash
php spark migrate
```

Cela va :
- ✅ Refactoriser la table `sales` 
- ✅ Créer la table `sale_items` 

**Important** : Vos données existantes seront migrées automatiquement (le total sera calculé à partir de prix × quantité).

---

### 2️⃣ Vérifier la Structure des Tables

#### Table `sales` (refactorisée)
```sql
- id (PK, BIGINT)
- shop_id (VARCHAR)
- client (VARCHAR)
- total (DECIMAL 10,2)         ← NOUVEAU
- payment_method (VARCHAR)
- created_at (TIMESTAMP)
```

#### Table `sale_items` (nouvelle)
```sql
- id (PK, BIGINT)
- sale_id (FK → sales)
- product_id (FK → products)
- product_name (VARCHAR)
- quantite (INT)
- prix_unitaire (DECIMAL 10,2) ← PERSONNALISABLE
- sous_total (DECIMAL 10,2)    ← AUTO
- created_at (TIMESTAMP)
```

---

## 🚀 FONCTIONNALITÉS IMPLÉMENTÉES

### Interface de Panier Dynamique
**URL** : `/sales/create`

**Caractéristiques** :
- ✅ Recherche produit en temps réel
- ✅ Ajouter plusieurs produits
- ✅ Modifier quantité (boutons +/-)
- ✅ Modifier prix unitaire (override du prix par défaut)
- ✅ Supprimer produit du panier
- ✅ Calcul automatique des sous-totaux et total
- ✅ Formulaire client & mode paiement

**Modes de paiement** :
- 💰 Espèces
- 📝 Crédit/Dette (avec client obligatoire)
- 📱 Mobile Money
- 🏦 Virement

---

### Historique des Ventes
**URL** : `/sales/list`

**Affiche** :
- Liste de toutes les ventes
- Client, total, mode paiement, date
- Lien vers détails de chaque vente

---

### Détail d'une Vente
**URL** : `/sales/detail/{id}`

**Affiche** :
- Informations complètes de la vente
- Tous les articles avec quantités et prix
- Résumé financier
- Boutons imprimer & PDF (à implémenter)

---

## 🔌 FLUX TECHNIQUE

### 1. Panier en Session
Le panier est stocké en **session PHP** (`$_SESSION['cart']`).

```php
$cart = [
    [
        'product_id'   => 1,
        'product_name' => 'Huile',
        'quantite'     => 2,
        'prix'         => 1200,
        'subtotal'     => 2400
    ],
    ...
]
```

### 2. Endpoints AJAX
Tous les endpoints panier retournent du JSON :

```javascript
GET/POST /sales/addToCart
GET/POST /sales/updateCartItem
GET/POST /sales/updateCartPrice
GET/POST /sales/removeFromCart
```

### 3. Validation et Enregistrement
Lors du clic "Valider la Vente" :

1. Créer une facture dans `sales`
2. Récupérer l'ID généré
3. Boucler sur chaque article du panier
4. Insérer dans `sale_items`
5. **Déduire le stock automatiquement**
6. Vider la session panier

---

## 📦 FICHIERS CRÉÉS / MODIFIÉS

### Migrations
- `app/Database/Migrations/20260513000000_RefactorSalesTable.php`
- `app/Database/Migrations/20260513010000_CreateSaleItemsTable.php`

### Modèles
- `app/Models/SalesItemModel.php` (NOUVEAU)
- `app/Models/SalesModel.php` (MODIFIÉ)

### Contrôleur
- `app/Controllers/Sales.php` (REFACTORISÉ)

### Vues
- `app/Views/V_sales.php` (REMPLACÉE)
- `app/Views/V_sales_list.php` (NOUVELLE)
- `app/Views/V_sales_detail.php` (NOUVELLE)

### Routes
- `app/Config/Routes.php` (MISE À JOUR)

---

## 🛡️ FILTRAGE PAR BOUTIQUE

Toutes les requêtes respectent l'isolation multi-boutique :

```php
if (!$isAdmin) {
    $shopId = session()->get('shop_id');
    // Filtrer par shop_id
}
```

---

## 🎨 DESIGN

- **Framework** : Bootstrap 5 (conservé)
- **Style** : Dark mode moderne (conservé)
- **Mobile-first** : Responsive design
- **Bottom Navigation** : Préservée
- **Cohérence** : Design identique au reste de l'app

---

## 🧪 TESTS RECOMMANDÉS

### Test 1 : Panier Simple
1. Aller à `/sales/create`
2. Ajouter 1 produit (Huile x1 = 1200)
3. Total doit afficher 1200 FCFA
4. Cliquer "Valider"
5. Vérifier : vente créée, stock réduit

### Test 2 : Panier Multiple
1. Ajouter Huile x2
2. Ajouter Riz x1
3. Ajouter Sucre x3
4. Total = (2×1200) + (1×5000) + (3×1000) = ?
5. Valider et vérifier stocks

### Test 3 : Modification Prix
1. Ajouter produit
2. Modifier prix unitaire (ex: 1200 → 1500)
3. Vérifier sous-total recalculé
4. Valider

### Test 4 : Historique
1. Aller à `/sales/list`
2. Vérifier toutes les ventes listées
3. Cliquer sur une vente
4. Vérifier détails complets

### Test 5 : Crédit/Dette
1. Ajouter panier
2. Sélectionner "Crédit/Dette"
3. Champ client devient obligatoire ✅
4. Sans client → erreur ✅
5. Avec client → vente enregistrée ✅

---

## ⚡ OPTIMISATIONS FUTURES

1. **PDF** : Générer factures PDF (TCPDF)
2. **Impression** : Formatage impression
3. **Reçus** : Imprimer reçus thermiques
4. **Remises** : Appliquer remises au panier
5. **Recherche avancée** : Filtres historique
6. **Synchronisation** : Sync avec backend
7. **Barcode** : Lecture code-barres produits
8. **Analytics** : Statistiques ventes

---

## 🔐 SÉCURITÉ

✅ **CSRF Protection** : Tokens CSRF inclus  
✅ **Session Isolation** : Filtrage par shop_id  
✅ **Input Validation** : Validation côté serveur  
✅ **Stock Integrity** : Déduction stock vérifiée  

---

## 📞 SUPPORT

Si vous avez besoin d'aide :
1. Vérifier les fichiers créés
2. Exécuter les migrations
3. Tester les endpoints AJAX
4. Consulter les logs (`writable/logs/`)

---

**Version** : 1.0  
**Date** : 13 May 2026  
**Status** : ✅ Prêt pour déploiement
