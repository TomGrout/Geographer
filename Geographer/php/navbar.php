<?php 
session_start();
?>

<!DOCTYPE html>

<head>
    <title>Population-Data</title>
    <link rel='stylesheet' href='../css/style.css' />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>

<body>
    <header>
        <div>
            <table class="black-bg"> 
                    <td>
                        <h1 class="margin-top-20 bold">Population Data</h1> 
                    </td>
                </tr>
            </table>
            <div>
            <table class="black-bg"> 
                <?php ($lang = 'en')?>
                    <tr class="centre">
                                <td>
                                    <a href="home.php"><button class="header-text bold">Home</button></a>
                                </td>
                                <td>
                                    <a href="viewTable.php"><button class="header-text bold">Countries Table</button></a>
                                </td>
                                <td>
                                    <a href="viewPopulations.php"><button class="header-text bold">Population Data</button></a>
                                </td>
                                <td>
                                    <a href="viewMap.php"><button class="header-text bold">Interactive Map</button></a>
                                </td>
                            
                            <td>
                                <a href="settings.php"><button class="header-text bold">Settings</button></a>
                            </td>
                </tr>
            </table>    
        </div>
    </header>
</body>
