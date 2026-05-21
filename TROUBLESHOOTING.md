# Cas d'Usage et Dépannage

## 📚 Cas d'Usage Courants

### Cas 1 : Vente Simple (1 client, 1 produit)

**Scénario** : Aïcha veut vendre 1 Huile à 1200 FCFA

**Étapes** :
1. Cliquer "Ventes" → Nouvelle Vente
2. Rechercher "Huile" dans la barre
3. Entrer quantité `1`
4. Cliquer "Ajouter"
5. Panier affiche : Huile x1 = 1200 FCFA
6. Client : (vide)
7. Paiement : Espèces
8. Cliquer "Valider"
9. ✅ Vente #123 créée, stock -1

---

### Cas 2 : Vente Multiple (Plusieurs produits)

**Scénario** : Mamadou achète pour 10 400 FCFA
- Huile x2 @ 1200 = 2400
- Riz x1 @ 5000 = 5000  
- Sucre x3 @ 1000 = 3000

**Étapes** :
1. Ajouter Huile → quantité 2 → Ajouter
2. Ajouter Riz → quantité 1 → Ajouter
3. Ajouter Sucre → quantité 3 → Ajouter
4. Panier affiche 3 articles, Total = 10400
5. Client : "Mamadou"
6. Paiement : Espèces
7. Cliquer "Valider"
8. ✅ Facture créée avec 3 items

---

### Cas 3 : Modifier Prix à la Volée

**Scénario** : Le prix du Riz a changé, c'est maintenant 4800

**Étapes** :
1. Ajouter Riz → quantité 1 → Total 5000
2. Dans le panier, cliquer sur le champ prix
3. Changer 5000 → 4800
4. Appuyer Entrée
5. ✅ Sous-total recalculé : 4800
6. Total global recalculé

---

### Cas 4 : Vente en Crédit (Dette)

**Scénario** : Fatima achète à crédit pour 8000 FCFA

**Étapes** :
1. Ajouter articles
2. Sélectionner mode "Crédit/Dette"
3. Champ Client devient ROUGE → **Obligatoire**
4. Entrer "Fatima"
5. Cliquer "Valider"
6. ✅ Vente enregistrée avec `payment_method = 'dette'`
7. **Important** : Cette vente peut être suivie pour dettes

---

### Cas 5 : Annuler et Recommencer

**Scénario** : L'utilisateur s'est trompé

**Étapes** :
1. Panier contient 5 articles
2. Cliquer "Réinitialiser"
3. ✅ Panier vidé
4. Tous les champs = vide
5. Recommencer fresh

---

### Cas 6 : Consulter Historique

**Scénario** : Voir toutes les ventes du jour

**Étapes** :
1. Cliquer "Ventes" dans le menu
2. Affiche le formulaire pour nouvelle vente
3. **Alternative** : URL directe `/sales/list`
4. ✅ Tableau avec toutes les ventes
5. Colonnes : ID, Client, Total, Paiement, Date

---

### Cas 7 : Voir Détails d'une Facture

**Scénario** : Vérifier les articles d'une vente

**Étapes** :
1. Aller à `/sales/list`
2. Trouver la vente dans le tableau
3. Cliquer "Détails"
4. ✅ Affiche :
   - Info vente (client, paiement, date)
   - Tableau articles complet
   - Résumé financier
   - Boutons imprimer/PDF

---

## 🐛 Dépannage Courant

### Problème 1 : "Produit introuvable"

**Cause possible** :
- Produit supprimé
- Boutique différente
- Base données corrupt

**Solution** :
```bash
# Vérifier produits
SELECT * FROM products WHERE id = 1;

# Recalculer stock
UPDATE products SET quantite = 0 WHERE quantite IS NULL;
```

---

### Problème 2 : "Stock insuffisant" - Mais stock existe

**Cause** :
- Cache pas à jour
- Requête concurrent

**Solution** :
```bash
# Forcer actualisation
UPDATE products SET quantite = quantite WHERE id = 1;

# Vérifier intégrité
SELECT SUM(quantite) as quantite_vendue 
FROM sale_items 
WHERE product_id = 1;
```

---

### Problème 3 : Panier vide quand on refresh

**Cause** :
- Session expirée (normal, par design)
- Cookies désactivés

**Solution** :
- Les données ne sont pas perdues
- C'est par design (cart = session temp)
- Si besoin de persistence, ajouter cookies

---

### Problème 4 : CSRF Token Invalid

**Cause** :
- Page trop ouverte
- Token expiré
- Plusieurs onglets

**Solution** :
1. Refresh la page
2. Essayer à nouveau
3. Vérifier paramètre `csrf_token` dans form

---

### Problème 5 : Total incorrect

**Cause** :
- Calcul JS pas exécuté
- Prix modifié mal

**Solution** :
```javascript
// Vérifier dans console
console.log(cartData.items);

// Recalculer
let total = cartData.items.reduce((sum, item) => sum + item.subtotal, 0);
console.log('Total correct:', total);
```

---

### Problème 6 : Vente créée mais stock pas réduit

**Cause** :
- Erreur dans boucle items
- Produit n'existe pas
- Permission BDD manquante

**Solution** :
```sql
-- Vérifier ventes sans articles
SELECT s.id, COUNT(si.id) as items
FROM sales s
LEFT JOIN sale_items si ON s.id = si.sale_id
GROUP BY s.id
HAVING COUNT(si.id) = 0;

-- Vérifier dernière vente
SELECT * FROM sales ORDER BY id DESC LIMIT 1;
SELECT * FROM sale_items WHERE sale_id = (SELECT MAX(id) FROM sales);
```

---

## ⚙️ Configurations Recommandées

### `php.ini`

```ini
; Taille max panier (articles JSON)
post_max_size = 8M
upload_max_filesize = 8M

; Session
session.cookie_httponly = 1
session.use_only_cookies = 1
session.gc_maxlifetime = 3600
```

### `app/Config/Session.php`

```php
public $sessionExpiration = 3600;        // 1 heure
public $sessionMatchIP = false;          // Flexible
public $sessionTimeToUpdate = 300;       // Rafraichir chaque 5 min
public $sessionRegenerateDestroy = true;
```

---

## 📊 Performance

### Optimisations en Place

✅ **Panier en Session** : Pas de BDD à chaque clic  
✅ **AJAX Endpoints** : Pas de page refresh  
✅ **Lazy Loading** : Produits chargés une fois  
✅ **Indexes BDD** : Sur shop_id, created_at  

### Si Performance Lente

**1. Vérifier nombre produits** :
```sql
SELECT COUNT(*) FROM products WHERE shop_id = 'shop-1';
-- Si > 10000, implémenter pagination
```

**2. Vérifier ventes par jour** :
```sql
SELECT COUNT(*) FROM sales 
WHERE DATE(created_at) = CURDATE();
-- Si > 1000, archiver anciennes données
```

**3. Indexes manquants** :
```sql
CREATE INDEX idx_sales_shop ON sales(shop_id);
CREATE INDEX idx_sales_date ON sales(created_at);
CREATE INDEX idx_sale_items_sale ON sale_items(sale_id);
CREATE INDEX idx_products_shop ON products(shop_id);
```

---

## 🔐 Sécurité - Checklist

- ✅ CSRF tokens sur tous les POST
- ✅ Validation client ET serveur
- ✅ Filtrage par shop_id obligatoire
- ✅ Pas de prix en JS modifiable avant submit
- ✅ Stock vérifié côté serveur
- ✅ Session isolation par user

**À améliorer** :
- ☐ Rate limiting sur endpoints
- ☐ Logging des modifications
- ☐ Audit trail ventes
- ☐ Signature électronique factures
- ☐ Two-factor auth admin

---

## 🌐 Pour Mobile

L'interface est 100% **responsive** :

- ✅ Bootstrap 5 grid system
- ✅ Stack vertical sur petit écran
- ✅ Boutons larges (min 48px)
- ✅ Touch-friendly
- ✅ Pas de zoom nécessaire

**Tests recommandés** :
- iPhone 12 (390px)
- Samsung S10 (360px)
- iPad (768px)
- Desktop (1920px)

---

## 📋 Checklist Déploiement

Avant de passer en production :

- [ ] Backup base données
- [ ] Exécuter migrations
- [ ] Tester panier simple
- [ ] Tester panier multiple
- [ ] Tester crédit/dette
- [ ] Vérifier stocks réduits
- [ ] Consulter historique
- [ ] Test mobile
- [ ] Test multi-navigateur
- [ ] Vérifier logs (`writable/logs/`)
- [ ] Vérifier uploads (`writable/uploads/`)
- [ ] Tester déconnexion (session)

---

## 📞 Logs Importants

### Où trouver les erreurs

```
writable/logs/
├── log-2026-05-13.log      ← Erreurs du jour
├── log-2026-05-12.log
└── ...
```

### Chercher erreurs panier

```bash
grep -i "cart\|sales\|stock" writable/logs/log-*.log
```

---

## 🎓 Améliorations Futures

### Priority 1 (Court terme)
1. Générer PDF factures
2. Imprimer reçus
3. Remises panier
4. Filtre historique ventes

### Priority 2 (Moyen terme)
1. Barcode scanner
2. Sync multi-device
3. Analytics dashboard
4. SMS receipt

### Priority 3 (Long terme)
1. Mode offline (PWA)
2. Signature électronique
3. API externe
4. IA recommendations

---

**Version** : 1.0  
**Date** : 13 May 2026  
**Maintenance** : ✅ Stable
