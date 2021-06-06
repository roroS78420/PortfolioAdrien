<!DOCTYPE html>
<html lang="en">


<div class="fancy-resp">




<!-- 2. Create links -->


<br>
<br><br><br><br>




 <form method="post" action="" class="d-flex">
                <input type="search" name="f_titre" placeholder="Rechercher une formation" class="form-control me-2">
                <button type="submit" name="subsearch" class="btn btn-outline-success">Search</button>
            </form>


<?php

if (isset($_POST['subsearch']))  {
    $search = htmlentities($_POST['f_titre']);
    $sql = "SELECT * FROM formations WHERE ";

    if (strlen($search) > 0)
        $sql .= "f_titre LIKE '%$search%' ";

    $sql = substr($sql, 0, strlen($sql)-0);

    $requete = $bdd->query($sql);
   

    $formations = $requete->fetchAll();

    foreach ($formations as $formation) {
        
        Echo "<a href=".$formation['f_titre']." >". $formation['f_titre']. "</a>";
    }
}

?>


<hr class="my-5" />

<p class="imglist" style="max-width: 1000px;">
  <a href="images/filelec.png" data-fancybox="images" data-caption="projet PPE">
    <img src="images/filelec.png" width="400px" height="300px" />
  </a>
<input type="button" value="En savoir plus" class="homebutton" id="btnHome" 
onClick="document.location.href='PPE'" />

<br>

  <a href="images/forum.png" data-fancybox="images" data-caption="Un forum">
    <img src="images/forum.png" width="400px" height="300px"/>
  </a>
  <input type="button" value="En savoir plus" class="homebutton" id="btnHome" 
onClick="document.location.href='Forum'" />

  
</p>
                    



</div>
    <!-- end header end --> 
    
</body>
</html>