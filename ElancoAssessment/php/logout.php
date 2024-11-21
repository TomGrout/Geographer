<?php 
include("navbar.php");
?>

<div>
        <div class="centre">

            <h1>Are you sure you want to log out?</h1>
            <br></br>
            <form action="logout.php" method="post">
                <input type="hidden" value="logout" name="logout"/>
                <input class="button" type="submit" value="Log Out" name="submit">
            </form>
           
        </div>
    <?php 
        header('Location: home.php?');
     ?>
</div>