# API Documentation - Endpoints Panier Dynamique

## 🔌 Endpoints AJAX

Tous les endpoints utilisent **POST** sauf indication contraire et retournent du **JSON**.

### Headers Requis
```javascript
{
    'Content-Type': 'application/x-www-form-urlencoded',
    'X-Requested-With': 'XMLHttpRequest'
}
```

### CSRF Protection
Tous les POST doivent inclure :
```php
'<?php echo csrf_token(); ?>': '<?php echo csrf_hash(); ?>'
```

---

## 📦 Endpoints

### 1. Ajouter au Panier
**POST** `/sales/addToCart`

**Paramètres** :
```javascript
{
    product_id: 1,
    quantite: 2,
    prix_unitaire: 1200
}
```

**Réponse (succès)** :
```json
{
    "success": true,
    "message": "Produit ajouté au panier",
    "cart": [
        {
            "product_id": 1,
            "product_name": "Huile",
            "quantite": 2,
            "prix": 1200,
            "subtotal": 2400
        }
    ],
    "total": 2400,
    "count": 1
}
```

**Réponse (erreur)** :
```json
{
    "error": "Stock insuffisant. Stock actuel : 5"
}
```

---

### 2. Modifier Quantité
**POST** `/sales/updateCartItem`

**Paramètres** :
```javascript
{
    product_id: 1,
    quantite: 3
}
```

**Réponse** :
```json
{
    "success": true,
    "cart": [...],
    "total": 3600,
    "count": 1
}
```

**Notes** :
- Quantité `<= 0` supprime l'article
- Stock n'est pas vérifié (cart validation au moment du submit)

---

### 3. Modifier Prix Unitaire
**POST** `/sales/updateCartPrice`

**Paramètres** :
```javascript
{
    product_id: 1,
    prix_unitaire: 1500
}
```

**Réponse** :
```json
{
    "success": true,
    "cart": [...],
    "total": 4500
}
```

---

### 4. Supprimer du Panier
**POST** `/sales/removeFromCart`

**Paramètres** :
```javascript
{
    product_id: 1
}
```

**Réponse** :
```json
{
    "success": true,
    "cart": [],
    "total": 0,
    "count": 0
}
```

---

### 5. Créer la Vente (Submit)
**POST** `/sales/store`

**Paramètres** (form standard) :
```javascript
{
    client: "Junior",
    payment_method: "cash"
}
```

**Processus Backend** :
1. ✅ Valider panier pas vide
2. ✅ Vérifier client si crédit
3. ✅ Créer facture dans `sales`
4. ✅ Récupérer insert ID
5. ✅ Boucler panier → `sale_items`
6. ✅ Déduire stock
7. ✅ Vider panier session
8. ✅ Redirect `/sales/list` avec succès

**Réponse** :
- Redirect avec flash message "Vente #123 enregistrée avec succès"

---

### 6. Afficher Liste Ventes
**GET** `/sales/list`

**Réponse (Vue)** :
```php
$data['sales'] = [
    [
        'id' => 1,
        'client' => 'Junior',
        'total' => 7400,
        'payment_method' => 'cash',
        'created_at' => '2026-05-13 14:30:00'
    ],
    ...
]
```

---

### 7. Afficher Détail Vente
**GET** `/sales/detail/{id}`

**Réponse (Vue)** :
```php
$data['sale'] = [
    'id' => 1,
    'client' => 'Junior',
    'total' => 7400,
    'payment_method' => 'cash',
    'created_at' => '2026-05-13 14:30:00'
];

$data['items'] = [
    [
        'id' => 1,
        'sale_id' => 1,
        'product_id' => 1,
        'product_name' => 'Huile',
        'quantite' => 2,
        'prix_unitaire' => 1200,
        'sous_total' => 2400,
        'created_at' => '2026-05-13 14:30:00'
    ],
    ...
]
```

---

## 🔐 Sécurité et Validation

### Côté Client (JavaScript)
```javascript
// Vérification stock avant ajout
if (quantite > stock) {
    alert('Stock insuffisant !');
    return;
}
```

### Côté Serveur (PHP)
```php
// Vérifier produit appartient à la boutique
if (!$isAdmin && $product['shop_id'] != $shopId) {
    return error('Accès refusé');
}

// Vérifier stock
if ($product['quantite'] < $quantite) {
    return error('Stock insuffisant');
}

// Valider client si crédit
if ($payment === 'dette' && empty($client)) {
    return error('Client obligatoire');
}
```

---

## 🧪 Exemples d'Utilisation

### JavaScript Vanilla
```javascript
// Ajouter au panier
const formData = new URLSearchParams({
    product_id: 1,
    quantite: 2,
    prix_unitaire: 1200,
    'CSRF_TOKEN': 'hash_here'
});

fetch('/sales/addToCart', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest'
    },
    body: formData
})
.then(res => res.json())
.then(data => {
    if (data.success) {
        console.log('Panier:', data.cart);
        console.log('Total:', data.total);
    } else {
        console.error('Erreur:', data.error);
    }
});
```

### jQuery (si nécessaire)
```javascript
$.ajax({
    url: '/sales/addToCart',
    type: 'POST',
    dataType: 'json',
    headers: {
        'X-Requested-With': 'XMLHttpRequest'
    },
    data: {
        product_id: 1,
        quantite: 2,
        prix_unitaire: 1200
    },
    success: function(data) {
        console.log(data.cart);
    }
});
```

---

## 📤 Format Session Panier

Le panier est stocké dans `$_SESSION['cart']` :

```php
$_SESSION['cart'] = [
    [
        'product_id' => 1,
        'product_name' => 'Huile',
        'quantite' => 2,
        'prix' => 1200,           // Prix unitaire modifiable
        'subtotal' => 2400        // quantite * prix
    ],
    [
        'product_id' => 2,
        'product_name' => 'Riz',
        'quantite' => 1,
        'prix' => 5000,
        'subtotal' => 5000
    ]
];
```

---

## ⚠️ Erreurs Courantes

### Error 404
```
Request URL: /sales/addToCart
→ Vérifier les routes dans Routes.php
→ Vérifier les méthodes du contrôleur Sales
```

### Error 400 (Bad Request)
```
JSON non valide
→ Vérifier headers Content-Type
→ Vérifier URLSearchParams format
```

### CSRF Token Invalid
```
Token expiré ou manquant
→ Régénérer les tokens page
→ Vérifier csrf_field() dans la vue
```

### Stock insuffisant
```
"error": "Stock insuffisant"
→ Réduire quantité
→ Vérifier stock base de données
```

---

## 📊 Model Methods

### SalesModel

```php
// Récupérer total revenus
$total = $salesModel->getTotalRevenue($shopId);

// Nombre ventes
$count = $salesModel->getTotalSales($shopId);

// Ventes avec items
$sales = $salesModel->getSalesWithItems($shopId, 50);

// Vente complète
$sale = $salesModel->getSaleWithItems($saleId);
```

### SalesItemModel

```php
// Articles d'une vente
$items = $saleItemsModel->getSaleItems($saleId);

// Calculer sous-total
$subtotal = $saleItemsModel->calculateSubtotal(2, 1200);
```

---

## 🔄 Workflow Complet

```
1. GET /sales/create
   → Affiche Vue avec produits
   → Init JS cartData

2. User ajoute produit
   → POST /sales/addToCart
   → Panier sauvegardé en session
   → JS met à jour UI

3. User modifie panier
   → POST /sales/updateCartItem ou updateCartPrice
   → Session mise à jour
   → Total recalculé

4. User valide
   → POST /sales/store
   → Créer sale + sale_items
   → Déduire stock
   → Vider panier
   → Redirect /sales/list

5. User consulte historique
   → GET /sales/list
   → Affiche tableau ventes

6. User voit détails
   → GET /sales/detail/1
   → Affiche articles + résumé
```

---

**Version API** : 1.0  
**Date** : 13 May 2026  
**Status** : ✅ Stable
