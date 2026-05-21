# 🎯 ÉTAPES À SUIVRE - Déploiement Immédiat

## 📋 Checklist Pré-Déploiement

Avant d'exécuter les commandes, vérifiez :

- [ ] PHP 8.0+ installé
- [ ] MySQL 5.7+ accessible
- [ ] Accès terminal au dossier `gestistore`
- [ ] Backup BDD actuelle fait
- [ ] Environnement `.env` configuré

---

## 🚀 Étapes Exactes

### ÉTAPE 1️⃣ : Sauvegarder la Base de Données

**Commande** :
```bash
mysqldump -u root -p gestistore > backup_$(date +%Y%m%d_%H%M%S).sql
```

**Que faire** :
1. Ouvrir un terminal
2. Accéder au dossier racine
3. Exécuter la commande
4. Entrer le mot de passe MySQL
5. Attendre la création du fichier `backup_*.sql`

**Vérification** :
```bash
ls -la backup_*.sql  # Doit afficher le fichier
```

---

### ÉTAPE 2️⃣ : Exécuter les Migrations

**Commande** :
```bash
php spark migrate
```

**Que faire** :
1. Accéder au dossier `gestistore`
2. Exécuter cette commande
3. Attendre les messages : 
   ```
   Running: app\Database\Migrations\20260513000000_RefactorSalesTable
   Migrated: app\Database\Migrations\20260513000000_RefactorSalesTable
   
   Running: app\Database\Migrations\20260513010000_CreateSaleItemsTable
   Migrated: app\Database\Migrations\20260513010000_CreateSaleItemsTable
   ```

**Vérification** :
```bash
php spark migrate:status
```

Doit afficher :
```
Ran On              Migration
==========================
YYYY-MM-DD HH:MM:SS  20260513000000_RefactorSalesTable
YYYY-MM-DD HH:MM:SS  20260513010000_CreateSaleItemsTable
```

---

### ÉTAPE 3️⃣ : Vérifier les Tables

**Via PHPMyAdmin** :
1. Ouvrir `http://localhost/phpmyadmin`
2. Sélectionner BDD `gestistore`
3. Vérifier tables :
   - ✅ `sales` (colonnes : id, shop_id, client, **total**, payment_method, created_at)
   - ✅ `sale_items` (colonnes : id, sale_id, product_id, product_name, quantite, prix_unitaire, sous_total, created_at)

**Via Terminal** :
```sql
mysql -u root -p gestistore
DESCRIBE sales;
DESCRIBE sale_items;
```

---

### ÉTAPE 4️⃣ : Tester le Panier

**URL** :
```
http://localhost/gestistore/sales/create
```

**Que faire** :
1. Ouvrir le navigateur
2. Accéder à l'URL ci-dessus
3. Vous devriez voir :
   - Panier vide à gauche
   - Liste produits à droite
   - Formulaire en bas

**Test Simple** :
1. Chercher un produit (ex: "Huile")
2. Entrer quantité `1`
3. Cliquer "Ajouter"
4. Le panier doit afficher : `Huile x1 = [prix] FCFA`
5. Total automatique en bas du panier

**Si ça marche** ✅ → Passer à ÉTAPE 5

**Si erreur** ❌ → Consulter [TROUBLESHOOTING.md](./TROUBLESHOOTING.md)

---

### ÉTAPE 5️⃣ : Tester le Multi-Produit

**Actions** :
1. Ajouter 3 produits différents
2. Panier doit afficher 3 articles
3. Total doit être calculé automatiquement
4. Modifier quantité d'un article (cliquer + ou -)
5. Total doit se recalculer
6. Modifier prix d'un article
7. Sous-total et total doivent se recalculer
8. Supprimer un article
9. Panier doit se mettre à jour

**Test Crédit** :
1. Cliquer "Mode Paiement" → "Crédit/Dette"
2. Champ "Client" doit devenir ROUGE (obligatoire)
3. Entrer un nom client
4. Cliquer "Valider la Vente"

**Vérification** :
- Vente créée (page affiche succès)
- Stocks réduits dans produits
- Panier vidé

---

### ÉTAPE 6️⃣ : Vérifier l'Historique

**URL** :
```
http://localhost/gestistore/sales/list
```

**Que faire** :
1. Accéder à l'URL
2. Vérifier que la vente créée à l'étape 5 est listée
3. Vérifier colonnes : ID, Client, Total, Paiement, Date
4. Cliquer "Détails" sur une vente
5. Vérifier affichage des articles

**Détails Vente** :
- Doit afficher tous les produits achetés
- Prix unitaire de chaque produit
- Quantité vendue
- Sous-total par article
- Total facture

---

### ÉTAPE 7️⃣ : Vérifier les Stocks

**Via Interface** :
1. Aller à `/stock`
2. Vérifier les quantités des produits vendus
3. Doit être réduit de la quantité vendue

**Via SQL** :
```sql
mysql -u root -p gestistore
SELECT nom, quantite FROM products WHERE id IN (1,2,3);
```

---

### ÉTAPE 8️⃣ : Consulter les Logs

**Vérifier qu'il n'y a pas d'erreurs** :

```bash
tail -f writable/logs/log-*.log
```

Doit afficher :
- Pas de `ERROR`
- Pas de `CRITICAL`
- Messages informatifs normaux

**Si erreurs** :
```bash
grep -i "error\|critical" writable/logs/log-*.log
```

---

### ÉTAPE 9️⃣ : Tests de Sécurité

**CSRF Protection** :
1. Soumettre le panier
2. Doit créer la vente
3. Pas d'erreur CSRF

**Session Isolation** :
1. Si multi-boutiques, tester avec différents users
2. Chacun ne voit que ses ventes

**Stock Integrity** :
1. Ajouter 100 articles de 10 quantités
2. Soumettre avec quantité 50
3. Stock doit être réduit de 50

---

## ⚡ Déploiement Production

### Avant Passer en Prod

- [ ] Tous les tests ci-dessus OK
- [ ] Pas d'erreurs dans logs
- [ ] Backup BDD fait
- [ ] Rollback plan disponible
- [ ] Team notifiée

### Rollback en Cas de Problème

```bash
# 1. Réinitialiser BDD
mysql -u root -p gestistore < backup_*.sql

# 2. Rollback migrations
php spark migrate:rollback

# 3. Redémarrer serveur
# (Apache/Nginx restart selon config)
```

---

## 📊 Résultats Attendus

### Après Succès

| Élément | État |
|---------|------|
| Tables créées | ✅ sales, sale_items |
| Données migrées | ✅ Anciennes ventes conservées |
| Panier fonctionne | ✅ Ajouter/modifier/supprimer |
| Historique visible | ✅ Toutes ventes listées |
| Détails ventes | ✅ Articles visibles |
| Stock réduit | ✅ Automatiquement |
| Logs sans erreur | ✅ Messages normaux |

### Performance

| Action | Temps | Status |
|--------|-------|--------|
| Charger /sales/create | < 2s | ✅ |
| Ajouter produit (AJAX) | < 200ms | ✅ |
| Valider vente | < 1s | ✅ |
| Charger /sales/list | < 500ms | ✅ |
| Charger détails | < 500ms | ✅ |

---

## 🆘 Si Ça Ne Marche Pas

### Erreur 1: "Class not found"
```
Cause: Migrations non exécutées
Solution: php spark migrate
```

### Erreur 2: "Unknown column"
```
Cause: Vieille colonne référencée
Solution: Vérifier requêtes dans Code
```

### Erreur 3: "CSRF token"
```
Cause: Token expiré
Solution: Refresh page, réessayer
```

### Erreur 4: "Stock insuffisant"
```
Cause: Quantité > stock disponible
Solution: Réduire quantité
```

**Pour plus** : Consulter [TROUBLESHOOTING.md](./TROUBLESHOOTING.md)

---

## 📞 Points d'Appui

### Documentation
- **Pour déploiement** → [IMPLEMENTATION_GUIDE.md](./IMPLEMENTATION_GUIDE.md)
- **Pour endpoints** → [API_DOCUMENTATION.md](./API_DOCUMENTATION.md)
- **Pour architecture** → [ARCHITECTURE.md](./ARCHITECTURE.md)
- **Pour problèmes** → [TROUBLESHOOTING.md](./TROUBLESHOOTING.md)
- **Pour SQL** → [SQL_EXAMPLES.md](./SQL_EXAMPLES.md)

---

## ✅ Validation Finale

### Questions à se Poser

1. **Panier** : Peux-tu ajouter 3 produits? ✅
2. **Modification** : Peux-tu modifier quantité/prix? ✅
3. **Suppression** : Peux-tu supprimer un article? ✅
4. **Validation** : La vente se crée-t-elle? ✅
5. **Stock** : Le stock est-il réduit? ✅
6. **Historique** : Les ventes sont-elles listées? ✅
7. **Détails** : Peux-tu voir les articles? ✅
8. **Logs** : Pas d'erreurs dans logs? ✅
9. **Performance** : < 2s chargement? ✅
10. **Mobile** : Ça fonctionne sur mobile? ✅

**Si tout est ✅ → DÉPLOIEMENT OK !**

---

## 🎉 Conclusion

**Vous avez mis en place avec succès** :
- ✅ Système multi-produits
- ✅ Panier dynamique
- ✅ Factures professionnelles
- ✅ Historique complet
- ✅ Code maintenable

**Prochaines étapes possibles** :
- PDF factures
- Imprimer reçus
- Scanner code-barres
- Analytics dashboard

---

**Date Déploiement** : 13 May 2026  
**Status** : ✅ **PRÊT**  
**Durée Déploiement** : ~30 minutes  

🚀 **BON DÉPLOIEMENT !**
