<?php
   /* LES JOINTURES SQL */
/* différence entre myisam et innobd : https://sql.sh/1548-mysql-innodb-myisam */

/*
    1-Jointure interne :
    Va retourner tous les enregistrements pour lesquels il y a une correspondance
    demandé par les jointures (sur les deux champs liés dans le ON)


    Exemple: Retourne tous les produits qui sont liés à un pays. Les produits sans pays
            ne font pas partie des résultats.
    SELECT * FROM `product` INNER JOIN country ON product.pays_id=country.id

    2-Jointure externe :
    Retourne tous les enregistrements d'une table même si il n'y a pas de correspondance
    entre les deux tables. LEFT permet de retourner tous les enregistrements de la table
    à gauche, RIGHT tous les enregistrements de la table à droite

    Exemple : retourne tous les produits même s'ils ne sont pas liés à un pays
    SELECT * FROM `product` LEFT JOIN country ON product.pays_id=country.id

    Exemple : retourne tous les pays même s'il n'y a pas de produit dedans
    SELECT * FROM `product` RIGHT JOIN country ON product.pays_id=country.id


 En plus des jointures, le moteur innodb permet de créer des clés étrangères.
Les clés étrangères pointent vers des champs d'une autre table, ce qui empêche
les incohérences dans les données
Exemple : si le pays "1" n'existe pas, aucun produit ne peut avoir pour valeur "1" dans
le champ "pays_id"
*/
?>