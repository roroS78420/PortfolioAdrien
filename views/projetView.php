<!DOCTYPE html>
<html lang="en">


<div class="fancy-resp">




<!-- 2. Create links -->


<br>
<br><br><br><br>








<?php 
include("commun/connexion.php");
  include("commun/entete.php");
  
  include("fonction/traitement_chaine.php");
  
  if(isset($_POST["mc"]) && $_POST["mc"]!="")
  {
    $chaque_mot=""; $url_fiche=""; $titre_fiche=""; $chaine_fiche=""; $compteur=0;
    $les_mots_cles = strtolower(utf8_decode($_POST["mc"]));
    $les_mots_cles=supprAccents(supprSpeciaux($les_mots_cles));
    $mots_a_exclure="avec|pour|dans|";
    $tableau_exclure=explode("|",$mots_a_exclure);
    $requete_et="SELECT * FROM formations ";
    $requete_ou = $requete_et;
    
    //echo $les_mots_cles;

    for($i=0;$i<sizeof($tableau_exclure);$i++)
    {
      $les_mots_cles=str_replace($tableau_exclure[$i],"",$les_mots_cles);
    }

    $les_mots_cles=str_replace("   "," ",str_replace("  "," ",$les_mots_cles));
    $les_mots_cles = str_replace("-"," ",$les_mots_cles);
    
    $nouveau=false;
    $fichier_nouveau="recherches/".str_replace(" ","-",$les_mots_cles).".txt";
    if(file_exists($fichier_nouveau))
    {
      $d1 = strtotime(date("j F Y H:i", filemtime($fichier_nouveau)));
      $d2 = strtotime(date("j F Y H:i")); //date en cours
      $difference = (int)$d2 - (int)$d1;
      if($difference > 3600) //1 heure
        $nouveau=true;
    }
    else
      $nouveau=true;
    
    //echo $les_mots_cles;

    if($nouveau==true)
    {
      if(strlen(str_replace(" ","",$les_mots_cles))<1)
        $chaine_fiche="Oups !<br /><br />Le contenu de votre demande est insuffisant pour être traité.";
      else if(strlen(str_replace(" ","",$les_mots_cles))>50)
        $chaine_fiche="Oula !<br /><br />Votre demande semble bien compliquée !<br /> Veuillez la simplifier.";
      else if(strpos($les_mots_cles, "a")===false && strpos($les_mots_cles, "e")===false && strpos($les_mots_cles, "i")===false && strpos($les_mots_cles, "o")===false && strpos($les_mots_cles, "u")===false && strpos($les_mots_cles, "y")===false)
        $chaine_fiche="Désolé !<br /><br />Votre demande ne semble pas correcte !<br /> Il faut être plus clair.";
      else
      {
        $tableau_mots_cles=explode(" ",$les_mots_cles);
        for($i=0;$i<sizeof($tableau_mots_cles);$i++)
        {
          $chaque_mot = rtrim($tableau_mots_cles[$i], "s"); //Supprime le s de fin soit le pluriel
          if(strlen($chaque_mot)>3)
          {
            if($compteur==0)
            {
              $requete_et .= "WHERE f_motscles LIKE '%".$chaque_mot."%' ";
              $requete_ou .= "WHERE f_motscles LIKE '%".$chaque_mot."%' ";
            }
            else
            {
              $requete_et .= "AND f_motscles LIKE '%".$chaque_mot."%' ";  
              $requete_ou .= "OR f_motscles LIKE '%".$chaque_mot."%' ";             
            }
            
            $compteur++;
          }
        }
        
        $requete_et .= "LIMIT 0,10;";
        
        $compteur=0;
        $retours = $conn->query($requete_et);
        while($retour = $retours->fetch())
        {
          $url_fiche=supprAccents(supprSpeciaux(strtolower($retour["f_titre"])));
          
          $titre_fiche = stripslashes($retour["f_titre"]);
          $titre_fiche = supprSpeciaux(strtolower($titre_fiche));
          $titre_fiche = str_replace("-"," ",$titre_fiche);
          for($i=0;$i<sizeof($tableau_mots_cles);$i++)
          {
            $chaque_mot = rtrim($tableau_mots_cles[$i], "s"); //Supprime le s de fin soit le pluriel
            if(strlen($chaque_mot)>2)
            {   
              $titre_fiche=str_replace($chaque_mot,"<span style='background-color:yellow;'>".$chaque_mot."</span>",$titre_fiche);
            }
          }
          
          $chaine_fiche.= "<div style='float:left; width:100%; padding-bottom:5px;'>";
          $chaine_fiche.= "<a href='".$url_fiche."' target='_self' style='color:#666666;'>".ucfirst(utf8_encode($titre_fiche))."</a>";
          $chaine_fiche.= "</div>";   
          
          $compteur++;
        }
        
        if($compteur==0)
        {
          $chaine_fiche = "Aucun résultat strictement équivalent trouvé. Rubriques connexes les plus pertinentes :<br /><br />";
          $retours = $conn->query( $requete_ou);

          while($retour = $retours->fetch())
          {
            $proportion = 0;
            $url_fiche=supprAccents(supprSpeciaux(strtolower($retour["f_titre"])));
            
            $titre_fiche = stripslashes($retour["f_titre"]);
            $titre_fiche = supprSpeciaux(strtolower($titre_fiche));
            $titre_fiche = str_replace("-"," ",$titre_fiche);
            for($i=0;$i<sizeof($tableau_mots_cles);$i++)
            {
              $chaque_mot = rtrim($tableau_mots_cles[$i], "s"); //Supprime le s de fin soit le pluriel
              
              if(strpos(supprAccents(supprSpeciaux(strtolower($retour["f_motscles"]))),$chaque_mot)!==false)
                $proportion++;
              
              if(strlen($chaque_mot)>2)
              {   
                $titre_fiche=str_replace($chaque_mot, "<span style='background-color:yellow;'>".$chaque_mot."</span>",$titre_fiche);
              }
            }
            
            $proportion = round($proportion/sizeof($tableau_mots_cles),2);
            if($proportion>=0.6)
            {         
            
              $chaine_fiche.= "<div style='float:left; width:100%; padding-bottom:5px;'>";
              $chaine_fiche.= "<span style='color:#CC3300'>".$proportion*(100)."%</span> : <a href='".$url_fiche."' target='_self' style='color:#666666;'>".ucfirst(utf8_encode($titre_fiche))."</a>";
              $chaine_fiche.= "</div>";   
              
              $compteur++;
              if($compteur>=10)
                break;          
            }
          }       
        }
      }
      $cache=fopen($fichier_nouveau, "w");
      fwrite($cache, $chaine_fiche);
      fclose($cache);     
    }
    else
    {
      $cache=fopen($fichier_nouveau,"r");
      $chaine_fiche=fread($cache, filesize($fichier_nouveau));
      fclose($cache); 
      $chaine_fiche .= "<div style='float:left; width:100%; padding-top:10px; color:#999999;'><i>Restitution de la base de connaissance</i></div>";     
    }
  }
?>
      <div style="width:100%;display:block;text-align:center;">
      </div>
      
      <div class="div_saut_ligne" style="height:30px;">
      </div>            
      
      <div style="float:left;width:10%;height:40px;"></div>
      <div style="float:left;width:80%;height:40px;text-align:center;">
      <div style="width:auto;display:block;height:auto;text-align:center;background-color:#ccccff;border:#7030a0 1px solid;padding-top:12px;box-shadow: 6px 6px 0px #aaa;color:#7030a0;">
      <h1>Moteur de recherche en PHP</h1>
      </div>
      </div>
          
      <div style="float:left;width:10%;height:40px;"></div>
      
      <div class="div_saut_ligne">
      </div>    
      
      <div style="width:100%;height:auto;text-align:center;">
            
      <div style="width:800px;display:inline-block;" id="conteneur">
      
        <div class="centre">
          <div class="titre_centre">
          <form id="formulaire" name="formulaire" method="post" action="projet">           
            <div class="liste_div">
              <input type="text" id="mc" name="mc" class="liste" value="Vos mots clés de recherche" onClick="this.value='';" />
            </div>
            <div class="liste_div" style="float:right;">
              <input type="submit" id="valider" name="valider" class="liste" style="width:100px;" value="Valider" />
            </div>            
          </form>         
          </div>  
        </div>    
      
        <div class="colonne" id="colonne_gauche">
        Liste des résultats<br /><br />
        <?php 
          if(isset($_POST["mc"]) && $_POST["mc"]!="")
            echo $chaine_fiche;//."<br />".$requete_et;
        ?>
        </div>
        
        <div class="centre">
          <div class="titre_centre">
          Résultats PHP.
          </div>  
        </div>          
        
      </div>
      
      </div>

      <div class="div_saut_ligne" style="height:50px;">
      </div>









<hr class="my-5" />

<p class="imglist" style="max-width: 1000px;">
  <a href="images/filelec.png" data-fancybox="images" data-caption="projet PPE">
    <img src="images/filelec.png" width="800px" height="500px" />
  </a>
<input type="button" value="En savoir plus" class="homebutton" id="btnHome" 
onClick="document.location.href='PPE'" />
  <a href="images/forum.png" data-fancybox="images" data-caption="Un forum">
    <img src="images/forum.png" />
  </a>
  <input type="button" value="En savoir plus" class="homebutton" id="btnHome" 
onClick="document.location.href='Forum'" />

  
</p>
                    































</div>
    <!-- end header end --> 
    
</body>
</html>