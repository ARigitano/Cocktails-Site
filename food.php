<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Site</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
<?php
require_once 'includes/database.php';

    if(isset($_POST['validate'])) 
    {
        if(isset($_POST['ingredientName'])) 
        {
            $chosenIngredients = $_POST['ingredientName'];
        }
        else 
        {
            $chosenIngredients = [];
        }

        foreach($chosenIngredients as $value) 
        {
            mysqli_query($conn, "UPDATE ingredientsfood SET have='1' WHERE name='$value'");
        }

        $hiddenIngredients = $_POST['ingredientHidden'];

        foreach($hiddenIngredients as $ingredientID) {
            $nameFixed = str_replace('/', '', $ingredientID);
            if(!in_array($nameFixed, $chosenIngredients)){
                mysqli_query($conn, "UPDATE ingredientsfood SET have='0' WHERE name='$nameFixed'");
            }
       }
    }

    $ingredientsHave = [];
    
    function DisplayIngredients($ingredientType) 
    {
        include 'includes/database.php';

        $sql = "SELECT * FROM ingredientsfood WHERE type='$ingredientType' ORDER by name ASC";
        $result = mysqli_query($conn, $sql);
        $rowCount = mysqli_num_rows($result);

        if($rowCount > 0) 
        {
            while ($row = mysqli_fetch_assoc($result)) 
            {
                $words = preg_replace('/(?<!\ )[A-Z]/', ' $0', $row['name']);
                //$nameNoSpaces = str_replace(' ', '', $row['name']);
                $nameNoSpaces = $row['name'];
                echo "<div class=\"oneingredient\">";

                if($row['have'] != 0) {
                    echo"<input type=\"checkbox\" class=\"checky\" id={$nameNoSpaces} name=\"ingredientName[]\" value={$nameNoSpaces} checked>";
                    array_push($GLOBALS['ingredientsHave'], $row['name']);
                }
                else 
                {
                echo"<input type=\"checkbox\" class=\"checky\" id={$nameNoSpaces} name=\"ingredientName[]\" value={$nameNoSpaces}>";
                }
                echo"<input type=\"hidden\" name=\"ingredientHidden[]\" value={$nameNoSpaces}/>";

                echo"<label for={$nameNoSpaces}>{$words}</label>";
                echo"</div>";
            }
        }
        else 
        {
            echo "No results found";
        }
    }
?>
    <header>
        <div class="container">
                <h1>Food Site</h1>
                <a href="index.php">Cocktails</a>
        </div>
    </header>

    <main>
    <div class = "flex">
    <form method="POST" action="" class = "flex">
    <div class="ingredients">
        
        <?php
        DisplayIngredients('Meat');
        ?>
    </div>

    <div class="ingredients">

        <?php
        DisplayIngredients('Vegetable');
        ?>
    </div>

    <div class="ingredients">
        <?php
        DisplayIngredients('Spice');
        ?>
    </div>

    <div class="ingredients">
        <?php
        DisplayIngredients('Other');
        ?>
    </div>
    <input type="submit" name="validate" value="Save ingredients/Search recipes">
    </form>

    <div class="cocktails">
        
        <?php
        $sql = "SELECT * FROM recipes ORDER by name ASC";
        $result = mysqli_query($conn, $sql);
        $rowCount = mysqli_num_rows($result);

        if($rowCount > 0) 
        {
            while ($row = mysqli_fetch_assoc($result)) 
            {
                $ingredientsUnparsed = $row['ingredients'];
                $ingredientsArray = explode(';', $ingredientsUnparsed);

                $ingredientsInCommon = [];

                foreach($ingredientsArray as $ingredientInArray) 
                {
                    foreach($ingredientsHave as $ingredientHave) 
                    {
                        if($ingredientInArray == $ingredientHave) 
                        {
                            array_push($ingredientsInCommon, $ingredientInArray);
                            break;
                        }
                    }
                }

                if(count($ingredientsArray) == count($ingredientsInCommon)) 
                {
                    echo "<div class =\"onecocktail\">";
                    echo "<h2>{$row['name']}</h2>";
                    $words = preg_replace('/(?<!\ )[A-Z]/', ' $0', $ingredientsUnparsed);
                    $namePrepared = str_replace(';', ', ', $words);
                    echo "<p>{$namePrepared}</p>";
                    echo "</div>";
                }
            }
        }
        else 
        {
            echo "No results match";
        }
        ?>
    </div>
        <!--<script src="ingredients.js"></script>-->
    </div>
    </main>
</body>
</html>