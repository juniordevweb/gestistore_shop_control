# 📖 Quick Reference - Guide Développeur

## 🎯 Commandes Importantes

### Exécuter Migrations
```bash
php spark migrate              # Exécuter toutes les migrations
php spark migrate:status       # Voir le statut
php spark migrate:rollback     # Annuler dernière migration
```

### Vérifier Erreurs
```bash
php spark server              # Démarrer serveur test
tail -f writable/logs/*.log   # Voir les logs en temps réel
```

### MySQL
```bash
mysql -u root -p gestistore
SHOW TABLES;                  # Voir tables
DESCRIBE sales;               # Structure table
SELECT * FROM sales LIMIT 5;  # Voir données
```

---

## 🏗️ Structure Fichiers

```
app/
├── Controllers/
│   └── Sales.php
│       ├── create()          → GET /sales/create
│       ├── addToCart()       → POST AJAX
│       ├── updateCartItem()  → POST AJAX
│       ├── updateCartPrice() → POST AJAX
│       ├── removeFromCart()  → POST AJAX
│       ├── store()           → POST form
│       ├── list()            → GET /sales/list
│       └── detail()          → GET /sales/detail/{id}
│
├── Models/
│   ├── SalesModel.php
│   │   ├── getTotalRevenue($shopId)
│   │   ├── getTotalSales($shopId)
│   │   ├── getSalesWithItems($shopId, $limit)
│   │   └── getSaleWithItems($saleId)
│   │
│   └── SalesItemModel.php
│       ├── getSaleItems($saleId)
│       └── calculateSubtotal($qty, $price)
│
├── Views/
│   ├── V_sales.php           → Panier + Produits
│   ├── V_sales_list.php      → Historique
│   └── V_sales_detail.php    → Détail facture
│
├── Database/
│   └── Migrations/
│       ├── 20260513000000_RefactorSalesTable.php
│       └── 20260513010000_CreateSaleItemsTable.php
│
└── Config/
    └── Routes.php
        ├── GET  /sales/create
        ├── POST /sales/addToCart
        ├── POST /sales/updateCartItem
        ├── POST /sales/updateCartPrice
        ├── POST /sales/removeFromCart
        ├── POST /sales/store
        ├── GET  /sales/list
        └── GET  /sales/detail/{id}
```

---

## 📊 Modèles de Données

### Sales Model
```php
$salesModel = new SalesModel();

// Créer vente
$salesModel->insert([
    'shop_id'        => 'shop-1',
    'client'         => 'Junior',
    'total'          => 10400,
    'payment_method' => 'cash'
]);

// Récupérer avec ID
$sale = $salesModel->find(123);

// Récupérer complet (avec items)
$complete = $salesModel->getSaleWithItems(123);
// Returns: ['id' => 123, 'client' => '...', 'items' => [...]]
```

### SalesItem Model
```php
$itemModel = new SalesItemModel();

// Insérer article
$itemModel->insert([
    'sale_id'       => 123,
    'product_id'    => 1,
    'product_name'  => 'Huile',
    'quantite'      => 2,
    'prix_unitaire' => 1200,
    'sous_total'    => 2400
]);

// Récupérer articles vente
$items = $itemModel->getSaleItems(123);
```

---

## 🔄 Session Panier

### Ajouter au Panier (Backend)
```php
$cart = session()->get('cart') ?? [];

// Vérifier si produit existe
$found = false;
foreach ($cart as &$item) {
    if ($item['product_id'] == $productId) {
        $item['quantite'] += $quantite;
        $item['subtotal'] = $item['quantite'] * $item['prix'];
        $found = true;
        break;
    }
}

// Ajouter nouveau
if (!$found) {
    $cart[] = [
        'product_id'   => $productId,
        'product_name' => $product['nom'],
        'quantite'     => $quantite,
        'prix'         => $prixUnitaire,
        'subtotal'     => $quantite * $prixUnitaire
    ];
}

session()->set('cart', $cart);
```

### Vider le Panier
```php
session()->remove('cart');
```

### Accéder au Panier (Frontend JS)
```javascript
// Dans cartData object
cartData.items = [
    { product_id: 1, product_name: 'Huile', quantite: 2, prix: 1200, subtotal: 2400 },
    ...
]

// Recalculer total
let total = cartData.items.reduce((sum, item) => sum + item.subtotal, 0);
```

---

## 🔑 Variables Utilisateur Essentielles

```php
// Session actuelle
session()->get('id')          // User ID
session()->get('shop_id')     // Shop ID (filtrage)
session()->get('is_admin')    // Est admin?
session()->get('email')       // Email user

// CSRF
csrf_token()   // Nom token
csrf_hash()    // Valeur token

// Flash messages
session()->getFlashdata('success')
session()->getFlashdata('error')
```

---

## 🛡️ Filtrage Multi-Shop

**TOUJOURS inclure** :
```php
if ($isAdmin) {
    // Admin voit tout
    $data = $model->findAll();
} else {
    // User voit que sa boutique
    $shopId = session()->get('shop_id');
    $data = $model->where('shop_id', $shopId)->findAll();
}
```

---

## 📋 Validations Importantes

### Client Obligatoire si Crédit
```php
if ($payment === 'dette' && empty($client)) {
    return redirect()->back()
        ->with('error', 'Client obligatoire pour crédit');
}
```

### Stock Suffisant
```php
$product = $productModel->find($productId);
if ($product['quantite'] < $quantite) {
    return response()->setStatusCode(400)->setJSON([
        'error' => 'Stock insuffisant'
    ]);
}
```

### Produit Appartient à Shop
```php
if (!$isAdmin && $product['shop_id'] != $shopId) {
    return redirect()->back()->with('error', 'Accès refusé');
}
```

---

## 🔗 Relations BDD

```sql
PRODUCTS (1)
    ↑
    │ 1:many
    │
SALE_ITEMS (many)
    ↑
    │ many:1
    │
SALES (1)
```

**Foreign Keys** :
- `sale_items.sale_id` → `sales.id` (CASCADE DELETE)
- `sale_items.product_id` → `products.id` (CASCADE DELETE)

---

## 📤 Format Réponses AJAX

### Succès
```json
{
    "success": true,
    "message": "Produit ajouté au panier",
    "cart": [...],
    "total": 10400,
    "count": 3
}
```

### Erreur
```json
{
    "error": "Stock insuffisant"
}
```

### Redirect (Form POST)
```php
return redirect()->to('/sales/list')
    ->with('success', 'Vente enregistrée');
```

---

## 🧪 Tests Rapides

### Test Panier Simple
```javascript
// Console browser
fetch('/sales/addToCart', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'product_id=1&quantite=1&prix_unitaire=1200'
})
.then(r => r.json())
.then(d => console.log(d));
```

### Test Vente
```sql
SELECT * FROM sales WHERE created_at > NOW() - INTERVAL 1 HOUR;
SELECT * FROM sale_items WHERE sale_id IN (SELECT id FROM sales WHERE created_at > NOW() - INTERVAL 1 HOUR);
```

---

## 🐛 Debugging Courant

### Panier Vide
```php
// Dans create()
echo '<pre>';
var_dump(session()->get('cart'));
echo '</pre>';
```

### Stock Pas Réduit
```php
// Vérifier la boucle dans store()
foreach ($cart as $item) {
    error_log("Réduisant stock produit {$item['product_id']} de {$item['quantite']}");
    $productModel->update($item['product_id'], [
        'quantite' => max(0, $product['quantite'] - $item['quantite'])
    ]);
}
```

### AJAX Non Fonctionnel
```javascript
// Vérifier dans console
console.log('CSRF:', document.querySelector('[name="csrf_token"]').value);
console.log('Response:', await fetch('/sales/addToCart').then(r => r.json()));
```

---

## 🔄 Workflow Création Vente

```
1. POST /sales/store
   ├─ Récupérer cart session
   ├─ Valider (panier, client, paiement)
   │
   ├─ Créer SALES row
   │  └─ $saleId = $salesModel->getInsertID()
   │
   ├─ Loop CART items
   │  ├─ Insert SALE_ITEMS
   │  ├─ UPDATE PRODUCTS.quantite
   │  └─ Catch errors
   │
   ├─ session()->remove('cart')
   │
   └─ Redirect /sales/list + flash success
```

---

## 📊 Requêtes SQL Fréquentes

### Ventes Client
```sql
SELECT * FROM sales 
WHERE client = 'Junior' 
ORDER BY created_at DESC;
```

### Chiffre d'Affaires
```sql
SELECT SUM(total) as revenue 
FROM sales 
WHERE payment_method = 'cash' 
AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY);
```

### Produits Vendus
```sql
SELECT product_name, SUM(quantite) as qty_sold, SUM(sous_total) as revenue
FROM sale_items
GROUP BY product_id
ORDER BY qty_sold DESC;
```

### Stock Après Ventes
```sql
SELECT p.id, p.nom, p.quantite, 
       SUM(si.quantite) as sold
FROM products p
LEFT JOIN sale_items si ON si.product_id = p.id
GROUP BY p.id
ORDER BY p.quantite ASC;
```

---

## 🎨 Modification UI

### Ajouter Bouton Panier
**Dans V_sales.php** :
```html
<button class="btn btn-primary" onclick="cartData.handleAddToCart(event)">
    <i class="fas fa-plus"></i> Ajouter
</button>
```

### Modifier Couleur Thème
**Dans V_sales.php styles** :
```css
--nav-bg: rgba(17, 24, 39, 0.95);    /* Couleur nav */
--nav-active: #22c55e;                /* Couleur active (vert) */
--text-main: #f8fafc;                 /* Texte blanc */
```

### Ajouter Champ Form
**Dans V_sales.php form** :
```html
<div class="mb-3">
    <label for="remarques" class="form-label">Remarques</label>
    <textarea name="remarques" class="form-control"></textarea>
</div>
```

---

## 🚀 Performance Tips

### Optimiser Requête Produits
```php
// Au lieu de
$products = $productModel->findAll();

// Faire
$products = $productModel
    ->where('shop_id', $shopId)
    ->where('quantite >', 0)  // Only in stock
    ->findAll();
```

### Ajouter Pagination
```php
$perPage = 50;
$products = $productModel
    ->where('shop_id', $shopId)
    ->paginate($perPage);

$data['pager'] = $productModel->pager;
```

### Cacher Produits
```php
// Dans controller
$cacheKey = "products_{$shopId}";
$products = cache($cacheKey) ?? 
    $productModel->where('shop_id', $shopId)->findAll();
cache()->save($cacheKey, $products, 3600);
```

---

## 📝 Logging Best Practices

```php
// Info (normal)
log_message('info', 'Vente créée: ' . json_encode($data));

// Warning (possible problème)
log_message('warning', 'Stock faible pour produit ' . $product['id']);

// Error (problème réel)
log_message('error', 'Erreur BDD: ' . $e->getMessage());

// Debug (development only)
if (ENVIRONMENT === 'development') {
    log_message('debug', 'Panier: ' . json_encode($cart));
}
```

---

## 🔐 Checklist Sécurité

Pour toute nouvelle feature :

- [ ] CSRF token sur POST
- [ ] Session auth filter
- [ ] Input validation
- [ ] Shop_id filtering
- [ ] SQL injection protection (use ORM)
- [ ] XSS prevention (htmlspecialchars)
- [ ] Error logging (pas afficher details à user)

---

**Version** : 1.0  
**Date** : 13 May 2026  
**Status** : ✅ Ready for Development
