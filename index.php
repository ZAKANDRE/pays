<?php
try{
 $dbh = new PDO('mysql:host=localhost;dbname=pays',"root","");
}
catch (PDOException $e){
    print "Connexion echoué!";
}


    $listeContinent = $_GET["continent_list"] ?? null;
    $regionListe = $_GET["region_list"] ?? null;
    
    $monde = $dbh->prepare("SELECT libelle_continent, ROUND(SUM(population_pays),1) AS population, 
    ROUND(taux_natalite_pays,1) AS natalite, ROUND(taux_mortalite_pays,1) AS mortalite, ROUND(esperance_vie_pays,1) AS vie,
    ROUND(taux_mortalite_infantile_pays,1) AS infantile, ROUND(nombre_enfants_par_femme_pays,1) AS enfants,
    ROUND(taux_croissance_pays,1) AS croissance,
    ROUND((population_plus_65_pays *100) / population_pays,1) AS pop_65 
    FROM t_pays 
    INNER JOIN t_continents ON t_pays.continent_id = t_continents.id_continent 
    GROUP BY libelle_continent;
    ");
    $monde->execute();


    $totaleLigneM = $dbh->prepare("SELECT *, ROUND(SUM(population_pays),1) AS population, ROUND(AVG(taux_natalite_pays),1) AS natalite,
    ROUND(AVG(taux_mortalite_pays),1) AS mortalite, ROUND(AVG(esperance_vie_pays),1) AS vie, ROUND(AVG(taux_mortalite_infantile_pays),1) AS infantile, 
    ROUND(AVG(nombre_enfants_par_femme_pays),1) AS enfants, ROUND(AVG(taux_croissance_pays),1) AS croissance, 
    ROUND(AVG((population_plus_65_pays *100) / population_pays),1) AS pop_65
    FROM t_pays 
    INNER JOIN t_continents ON t_pays.continent_id = t_continents.id_continent;");
    $totaleLigneM->execute();
  
    $totaleLigneCont1 = $dbh->prepare(
            "SELECT  libelle_continent, 
                ROUND(SUM(population_pays),1) AS population, 
                ROUND(AVG(taux_natalite_pays),1) AS natalite,
                ROUND(AVG(taux_mortalite_pays),1) AS mortalite, 
                ROUND(AVG(esperance_vie_pays),1) AS vie, 
                ROUND(AVG(taux_mortalite_infantile_pays),1) AS infantile, 
                ROUND(AVG(nombre_enfants_par_femme_pays),1) AS enfants, 
                ROUND(AVG(taux_croissance_pays),1) AS croissance, 
                ROUND(AVG((population_plus_65_pays *100) / population_pays),1) AS pop_65
            FROM t_pays 
            INNER JOIN t_continents ON t_pays.continent_id = t_continents.id_continent
            LEFT JOIN t_regions ON t_pays.region_id = t_regions.id_region
            WHERE t_pays.continent_id = 3
            GROUP BY  libelle_continent;");
    $totaleLigneCont1->execute();
    $ligne = $totaleLigneCont1->fetch(PDO::FETCH_ASSOC);
        
    $continentSelect = $dbh->prepare("SELECT * FROM t_continents GROUP BY libelle_continent;");
    $continentSelect->execute();

    if($_GET){
        
        $totaleLigne = $dbh->prepare("SELECT libelle_continent, ROUND(SUM(population_pays),1) AS population, ROUND(AVG(taux_natalite_pays),1) AS natalite,
        ROUND(AVG(taux_mortalite_pays),1) AS mortalite, ROUND(AVG(esperance_vie_pays),1) AS vie, ROUND(AVG(taux_mortalite_infantile_pays),1) AS infantile, 
        ROUND(AVG(nombre_enfants_par_femme_pays),1) AS enfants, ROUND(AVG(taux_croissance_pays),1) AS croissance, 
        ROUND(AVG((population_plus_65_pays *100) / population_pays),1) AS pop_65
        FROM t_pays 
        INNER JOIN t_continents ON t_pays.continent_id = t_continents.id_continent
        INNER JOIN t_regions ON t_pays.region_id = t_regions.id_region
        WHERE t_pays.continent_id=$listeContinent;");
        $totaleLigne->execute();

        $regionSelect = $dbh->prepare("
            SELECT id_region, libelle_region FROM t_regions
            LEFT JOIN t_continents ON t_regions.continent_id=t_continents.id_continent
            WHERE t_regions.continent_id=$listeContinent GROUP BY libelle_region;");
        $regionSelect->execute(); 

        if($regionListe){
            
        $afficherPays = $dbh->prepare("
            SELECT libelle_pays, ROUND(SUM(population_pays),1) AS population, 
            ROUND(taux_natalite_pays,1) AS natalite, ROUND(taux_mortalite_pays,1) AS mortalite, ROUND(esperance_vie_pays,1) AS vie,
            ROUND(taux_mortalite_infantile_pays,1) AS infantile, ROUND(nombre_enfants_par_femme_pays,1) AS enfants,
            ROUND(taux_croissance_pays,1) AS croissance,
            ROUND((population_plus_65_pays *100) / population_pays,1) AS pop_65
            FROM t_pays 
            INNER JOIN t_continents ON t_pays.continent_id=t_continents.id_continent
            LEFT JOIN t_regions ON  t_pays.region_id=t_regions.id_region
            WHERE t_pays.continent_id=$listeContinent AND t_pays.region_id=$regionListe
            GROUP BY libelle_pays;
           ");
        $afficherPays->execute();

        $totaleLigne = $dbh->prepare(
            "SELECT libelle_region, 
            ROUND(SUM(population_pays),1) AS population, ROUND(AVG(taux_natalite_pays),1) AS natalite,
            ROUND(AVG(taux_mortalite_pays),1) AS mortalite, ROUND(AVG(esperance_vie_pays),1) AS vie, 
            ROUND(AVG(taux_mortalite_infantile_pays),1) AS infantile, 
            ROUND(AVG(nombre_enfants_par_femme_pays),1) AS enfants, ROUND(AVG(taux_croissance_pays),1) AS croissance, 
            ROUND(AVG((population_plus_65_pays *100) / population_pays),1) AS pop_65
            FROM t_pays 
            INNER JOIN t_continents ON t_pays.continent_id = t_continents.id_continent
            INNER JOIN t_regions ON t_pays.region_id = t_regions.id_region
            WHERE t_pays.continent_id=$listeContinent AND t_pays.region_id=$regionListe
            GROUP BY libelle_region;");
        $totaleLigne->execute();


    }

    else{
        $afficherPays = $dbh->prepare("
            SELECT libelle_region,
                CASE WHEN t_regions.id_region is NULL THEN libelle_pays
                ELSE libelle_region 
                END AS zone_affichee,
            ROUND(SUM(population_pays),1) AS population, 
            ROUND(taux_natalite_pays,1) AS natalite, ROUND(taux_mortalite_pays,1) AS mortalite, ROUND(esperance_vie_pays,1) AS vie,
            ROUND(taux_mortalite_infantile_pays,1) AS infantile, ROUND(nombre_enfants_par_femme_pays,1) AS enfants,
            ROUND(taux_croissance_pays,1) AS croissance,
            ROUND((population_plus_65_pays *100) / population_pays,1) AS pop_65
            FROM t_pays 
            INNER JOIN t_continents ON t_pays.continent_id=t_continents.id_continent
            LEFT JOIN t_regions ON  t_pays.region_id=t_regions.id_region
            WHERE t_pays.continent_id=$listeContinent 
            GROUP BY zone_affichee;
           ");
        $afficherPays->execute();
        }

    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pays</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <form action="index.php" method="GET" id="form">
            <p id="text_continent">Par continent:</p>
            <p id="text_region">Par region:</p>
            <select name="continent_list" id="continent_list" class="form-select form-select-lg mb-3" aria-label=".form-select-lg example">
                    <option value="7">Monde</option>

                    <?php foreach($continentSelect as $continent) {?>
                        <option value="<?php print $continent["id_continent"]; ?>"
                        <?php if($continent["id_continent"] == $listeContinent) print "selected";?>
                        >
                                <?php print $continent["libelle_continent"]; ?>
                        </option>
                    <?php }?>

            </select>
            <select name="region_list" id="region_list" class="form-select form-select-lg mb-3" aria-label=".form-select-lg example">
                <option value="" selected disabled>---</option>
                       <?php  foreach ($regionSelect as $region) { ?> 
                        <option value="<?php print $region["id_region"];?>"
                        <?php if($region["id_region"] == $regionListe) print "selected";?>
                        >
                            <?php print $region["libelle_region"]; ?>
                        </option>
                       <?php }?> 
            </select>
    </form>
    <h3 id="title_continent"> </h3>
    <table class="table table-striped" id="table">
        <thead>
            <th>
                Pays
            </th>
            <th>
                Population totale <br>(en milliers)
            </th>
            <th>
                Taux de natalité
            </th>
            <th>
                Taux de mortalité
            </th>
            <th>
                Espérance de vie	
            </th>
            <th>
                Taux de mortalité infantile
            </th>
            <th>
                Nombre d’enfant(s) par femme
            </th>
            <th>
                Taux de croissance	
            </th>
            <th>
                Part des 65 ans et plus (%)
            </th>
        </thead>
        <tbody>
            <?php 
                if(!($_GET) || $listeContinent == '7'){
                    foreach($monde as $pagePrincipale) {?>
                <tr>
                    <td><?php print $pagePrincipale["libelle_continent"];?></td>
                    <td><?php print $pagePrincipale["population"];?></td>
                    <td><?php print $pagePrincipale["natalite"];?></td>
                    <td><?php print $pagePrincipale["mortalite"];?></td>
                    <td><?php print $pagePrincipale["vie"];?></td>
                    <td><?php print $pagePrincipale["infantile"];?></td>
                    <td><?php print $pagePrincipale["enfants"];?></td>
                    <td><?php print $pagePrincipale["croissance"];?></td>
                    <td><?php print $pagePrincipale["pop_65"];?></td>
                </tr>
            <?php } ?>
            <?php foreach($totaleLigneM as $totale) {?>
                <tr class="bottom_line">
                    <td><?php print 'MONDE';?></td>
                    <td><?php print $totale["population"];?></td>
                    <td><?php print $totale["natalite"];?></td>
                    <td><?php print $totale["mortalite"];?></td>
                    <td><?php print $totale["vie"];?></td>
                    <td><?php print $totale["infantile"];?></td>
                    <td><?php print $totale["enfants"];?></td>
                    <td><?php print $totale["croissance"];?></td>
                    <td><?php print $totale["pop_65"];?></td>
                </tr>
            <?php } } else{?>

            <?php foreach($afficherPays as $pays ) {?>
                <tr>
                    <td>
                        <?php if($regionListe) {
                        print $pays["libelle_pays"];
                        } else {
                        print $pays["zone_affichee"];
                        }?>
                    </td>
                    <td><?php print $pays["population"];?></td>
                    <td><?php print $pays["natalite"];?></td>
                    <td><?php print $pays["mortalite"];?></td>
                    <td><?php print $pays["vie"];?></td>
                    <td><?php print $pays["infantile"];?></td>
                    <td><?php print $pays["enfants"];?></td>
                    <td><?php print $pays["croissance"];?></td>
                    <td><?php print $pays["pop_65"];?></td>
                </tr>
              
            <?php } }?>
        <?php  if ($listeContinent != '3' && $listeContinent != '7' && isset($listeContinent)){
            foreach($totaleLigne as $totale) {?>
                <tr class="bottom_line">
                    <td>
                        <?php 
                      
                        if($regionListe){
                            print $totale["libelle_region"];
                        }else{
                            print $ligne['libelle_continent'];
                        }
                        ?>
                    </td>
                    <td><?php print $totale["population"];?></td>
                    <td><?php print $totale["natalite"];?></td>
                    <td><?php print $totale["mortalite"];?></td>
                    <td><?php print $totale["vie"];?></td>
                    <td><?php print $totale["infantile"];?></td>
                    <td><?php print $totale["enfants"];?></td>
                    <td><?php print $totale["croissance"];?></td>
                    <td><?php print $totale["pop_65"];?></td>
                </tr>
            <?php } }?>

<?php if ($listeContinent == '3') {?>
        
                <tr class="bottom_line">
                    <td>
                        <?php 
                            print $ligne["libelle_continent"];
                        ?>
                    </td>
                    <td><?php print $ligne["population"];?></td>
                    <td><?php print $ligne["natalite"];?></td>
                    <td><?php print $ligne["mortalite"];?></td>
                    <td><?php print $ligne["vie"];?></td>
                    <td><?php print $ligne["infantile"];?></td>
                    <td><?php print $ligne["enfants"];?></td>
                    <td><?php print $ligne["croissance"];?></td>
                    <td><?php print $ligne["pop_65"];?></td>
                </tr>
<?php } ?>
        
        </tbody>
    </table>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
    <script type="text/javascript" src="script.js"></script>
</body>
</html>
<?php $dbh = null;?>