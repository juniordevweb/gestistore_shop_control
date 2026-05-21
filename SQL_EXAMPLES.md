# Exemples et Requêtes SQL

## 📊 Exemple de Données

### Exemple 1 : Vente Simple (1 client, 2 produits)

**Facture #1**
```
Client : Junior
Date : 2026-05-13 14:30:00
Mode : Espèces

Articles :
- Huile x2 @ 1200 = 2400
- Riz x1 @ 5000 = 5000
TOTAL = 7400 FCFA
```

**Requête INSERT** :
```sql
-- 1. Créer la vente
INSERT INTO sales (shop_id, client, total, payment_method, created_at)
VALUES ('shop-1', 'Junior', 7400, 'cash', NOW());

-- 2. Récupérer l'ID (suppose ID = 1)
-- 3. Ajouter les articles
INSERT INTO sale_items (sale_id, product_id, product_name, quantite, prix_unitaire, sous_total, created_at)
VALUES 
  (1, 1, 'Huile', 2, 1200, 2400, NOW()),
  (1, 2, 'Riz', 1, 5000, 5000, NOW());

-- 4. Mettre à jour le stock
UPDATE products SET quantite = quantite - 2 WHERE id = 1;
UPDATE products SET quantite = quantite - 1 WHERE id = 2;
```

---

## 🔍 Requêtes Utiles

### Afficher toutes les ventes d'une boutique
```sql
SELECT * FROM sales 
WHERE shop_id = 'shop-1'
ORDER BY created_at DESC;
```

### Afficher les détails d'une vente
```sql
SELECT 
    s.id,
    s.client,
    s.total,
    s.payment_method,
    s.created_at,
    si.product_name,
    si.quantite,
    si.prix_unitaire,
    si.sous_total
FROM sales s
LEFT JOIN sale_items si ON s.id = si.sale_id
WHERE s.id = 1
ORDER BY si.id;
```

### Calculer le chiffre d'affaires
```sql
-- Total de toutes les ventes
SELECT SUM(total) as chiffre_affaires 
FROM sales;

-- Par boutique
SELECT shop_id, SUM(total) as total_ventes, COUNT(*) as nombre_ventes
FROM sales
GROUP BY shop_id
ORDER BY total_ventes DESC;
```

### Ventes en crédit (dettes)
```sql
SELECT client, SUM(total) as montant_total, COUNT(*) as nombre_ventes
FROM sales
WHERE payment_method = 'dette'
GROUP BY client
ORDER BY montant_total DESC;
```

### Produits plus vendus
```sql
SELECT 
    product_name,
    SUM(quantite) as quantite_vendue,
    SUM(sous_total) as montant_total,
    COUNT(*) as nombre_ventes
FROM sale_items
GROUP BY product_id, product_name
ORDER BY quantite_vendue DESC;
```

### Ventes par mode de paiement
```sql
SELECT 
    payment_method,
    COUNT(*) as nombre_ventes,
    SUM(total) as montant_total
FROM sales
GROUP BY payment_method
ORDER BY montant_total DESC;
```

### Ventes par date
```sql
SELECT 
    DATE(created_at) as date_vente,
    COUNT(*) as nombre_ventes,
    SUM(total) as chiffre_journalier
FROM sales
GROUP BY DATE(created_at)
ORDER BY created_at DESC;
```

### Vérifier intégrité stock
```sql
-- Stock actuel
SELECT id, nom, quantite 
FROM products 
WHERE quantite < 5
ORDER BY quantite ASC;

-- Quantité totale vendue par produit
SELECT 
    product_id,
    product_name,
    SUM(quantite) as quantite_vendue
FROM sale_items
GROUP BY product_id, product_name;
```

---

## 📈 Dashboard SQL

### Statistiques globales
```sql
SELECT 
    (SELECT COUNT(*) FROM sales) as nombre_ventes,
    (SELECT SUM(total) FROM sales) as chiffre_affaires_total,
    (SELECT COUNT(DISTINCT client) FROM sales) as nombre_clients,
    (SELECT COUNT(*) FROM products) as nombre_produits,
    (SELECT SUM(quantite) FROM products) as stock_total;
```

### Top 10 produits
```sql
SELECT 
    si.product_name,
    SUM(si.quantite) as quantite,
    SUM(si.sous_total) as montant
FROM sale_items si
GROUP BY si.product_id
ORDER BY quantite DESC
LIMIT 10;
```

### Analyse temporelle
```sql
SELECT 
    YEAR(created_at) as annee,
    MONTH(created_at) as mois,
    COUNT(*) as nombre_ventes,
    SUM(total) as montant
FROM sales
GROUP BY YEAR(created_at), MONTH(created_at)
ORDER BY created_at DESC;
```

---

## 🔄 Migration des Données

### Depuis l'ancienne structure
```sql
-- Si vous aviez des données en ancien format
-- Les migrations ont déjà converti le format
-- Vérifier :

SELECT 
    COUNT(*) as total_ventes,
    SUM(total) as chiffre_affaires,
    SUM(prix * quantite) as ancien_total -- should match
FROM sales;
```

---

## 🛠️ Maintenance

### Nettoyer les ventes orphelines
```sql
-- Supprimer les articles sans vente associée
DELETE FROM sale_items 
WHERE sale_id NOT IN (SELECT id FROM sales);
```

### Archiver les anciennes ventes (si nécessaire)
```sql
-- Créer table archive
CREATE TABLE sales_archive LIKE sales;
CREATE TABLE sale_items_archive LIKE sale_items;

-- Archiver ventes de plus de 1 an
INSERT INTO sales_archive 
SELECT * FROM sales 
WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);

DELETE FROM sales 
WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);
```

---

## 📝 Notes d'Implémentation

1. **Période de transition** : Les anciennes données ont été migrées automatiquement
2. **Compatibilité** : L'ancien format mono-produit n'est plus supporté
3. **Backup** : Faites un backup avant migration
4. **Test** : Testez sur une copie avant production

---

**Dernière mise à jour** : 13 May 2026
