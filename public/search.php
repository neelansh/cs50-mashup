<?php

    require(__DIR__ . "/../includes/config.php");

    // numerically indexed array of places
    $places = [];

    // TODO: search database for places matching $_GET["geo"]
    
    $param = array_map('trim' , explode("," , $_GET["geo"]));
    $result = [];
    foreach($param as $chunk)
    {
        $result = array_merge($result , explode(" ",$chunk));
    }
    $param = $result;
  
    
    $sql = "SELECT * FROM places WHERE (";
     for ($i = 0, $count = count($param); $i < $count; $i++) 
     {
        if(is_numeric($param[$i]))
        {
            if($param[$i] > 999)
            {
                $sql.="postal_code = ".htmlspecialchars($param[$i]);
            }
            else
            {
                $sql.= "admin_code2 = ".htmlspecialchars($param[$i]);
            }
        }
        else
        {
            if(strlen($param[$i]) == 2)
            {
                $query = query("SELECT country_code FROM places WHERE country_code LIKE '".htmlspecialchars($param[$i])."%';");
                if(count($query) == 1)
                {
                    $sql.="country_code LIKE '".htmlspecialchars($param[$i])."%' ";
                }
                else
                {
                    $sql.="admin_code1 LIKE '".htmlspecialchars($param[$i])."%' ";
                }
            }
            else
            {
                $sql.="place_name LIKE '".htmlspecialchars($param[$i])."%' OR admin_name1 LIKE '".htmlspecialchars($param[$i])."%' OR admin_name2 LIKE '".htmlspecialchars($param[$i])."%' ";
            }
        }
        if($i == $count-1)
        {
            $sql.=");";
        } 
        else
        {
            $sql.=")AND (";
        }
        
    }
    
    $query = query($sql);
    if($query === false)
    {
        echo("something went wrong");
        return -1;
    }
    
    $places = $query;
    
    // output places as JSON (pretty-printed for debugging convenience)
    header("Content-type: application/json");
    print(json_encode($places, JSON_PRETTY_PRINT));

?>
