<!DOCTYPE html>
<html lang="en">


<div class="fancy-resp">




<!-- 2. Create links -->


<br>
<br><br><br><br>










<h3>Recherche des Projets </h3>
 
    <center> <tr>
         <td>Nom : </td>
         
     </tr>
     </table>
</center>

     <?php
    $recherche = isset($_POST['recherche']) ? $_POST['recherche'] : '';

    $q = $bdd->query(
     "SELECT Nom FROM search
      WHERE Nom LIKE '%$recherche%'
      LIMIT 10");

     while( $r = $q->fetch()){
          echo "<tr>  <td>".$r['Nom']."</td> </tr>" ;
         };

?>
<form method="post" action="" style="margin-top:4rem;">
     <form method="POST" action="">
         Rechercher projet en particulier : <input type="text" name="recherche">
         <input type="SUBMIT" value="chercher le projet ">
     </form>
 </form>












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