<?php
    function modifDate($dateNaissance, $formatEntree="d/m/Y", $formatSortie="Y-m-d") {
        $dateNaissanceObjet = DateTime::createFromFormat($formatEntree, $dateNaissance);
        $dateNaissance = $dateNaissanceObjet->format($formatSortie);

        return $dateNaissance;
    }
?>