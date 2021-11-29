<?php
require(__DIR__ . "/../../partials/nav.php");

$results = [];
$db = getDB();
$stmt = $db->prepare("SELECT product_id, unit_cost, quantity from CartItems WHERE user_id = :user_id");
try {
    $stmt->execute([":user_id" => $_SESSION["user"]["id"]]);
    $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($r) {
        $results = $r;
    }
} catch (PDOException $e) {
    flash("<pre>" . var_export($e, true) . "</pre>");
}

?>
<style>
    .cart_item{
        position: fixed;
        left: 50%;
        margin-left: -150px;
        border: 1px solid black;
        box-shadow: 5px 5px black;
        padding: 10px;
        background-color: #a2eda1;
        width: 300px;
        height: 100px;
    }
</style>
<div class="container-fluid">
    <h1>Cart</h1>
    <?php
        foreach($results as $index => $record) : 
            foreach($record as $column => $value) :
    ?>
    <div class="cart_item">
        <?php
            if($column === 'product_id'){
                $stmt = $db->prepare("SELECT name from Products WHERE id = :product_id");
                try {
                    $stmt->execute([":product_id" => $product_column]);
                    $r = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if ($r) {
                        $results_products = $r;
                        echo("Product: " . $results_products["name"] . "<br>");
                    }
                } catch (PDOException $e) {
                    flash("<pre>" . var_export($e, true) . "</pre>");
                }
            }
            if($column === 'unit_cost'){
                echo("Cost: " . $value . "<br>");
            }
            if($column === 'quantity'){
                echo("Quantity: " . $value . "<br>");
            }
        ?>
    </div>
    <?php endforeach; endforeach;?>
</div>
<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>