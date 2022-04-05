<?php
// CSRF Protection 
if (isset($_POST) && !empty($_POST)) {
    //CSRF token validation
    if (isset($_POST['csrf_token'])) {
        if ($_POST['csrf_token'] != $_SESSION['csrf_token']) {
            header("Location: index.php");
            die();
        } 
    }
}
$token = md5(uniqid(rand(), true));
$_SESSION['csrf_token'] = $token;

// insert/add, remove , empty the product into the cart
if (!empty($_GET["action"])) {
    switch ($_GET["action"]) {
        case "add":
            if (!empty($_POST["input_quantity"])) {

                $discount =  $checkout->getDiscountedPrice($_POST["id"], $_POST["input_quantity"]);
                $insertedProduct = $db_handle->runQuery(" SELECT * FROM tbl_products LEFT JOIN tbl_specialoffers ON tbl_products.id = tbl_specialoffers.id WHERE tbl_products.id='" . $_POST["id"] . "'");

                $itemArray = array(
                    $insertedProduct[0]["id"] =>
                    array(
                        'product_name' => $insertedProduct[0]["product_name"],
                        'product_sku' => $insertedProduct[0]["product_sku"],
                        'id' => $insertedProduct[0]["id"],
                        'quantity' => $_POST["input_quantity"],
                        'price' => $discount
                    )
                );
                if (!empty($_SESSION["cart_item"])) {
                    if (in_array($insertedProduct[0]["id"], array_keys($_SESSION["cart_item"]))) {

                        foreach ($_SESSION["cart_item"] as $k => $v) {
                            if ($insertedProduct[0]["id"] == $k) {
                                if (empty($_SESSION["cart_item"][$k]["quantity"])) {
                                    $_SESSION["cart_item"][$k]["quantity"] = 0;
                                }
                                $_SESSION["cart_item"][$k]["quantity"] += $_POST["input_quantity"];
                                $_SESSION["cart_item"][$k]["price"] =   $checkout->getDiscountedPrice($_SESSION["cart_item"][$k]["id"], $_SESSION["cart_item"][$k]["quantity"]);
                            }
                        }
                    } else {
                        $_SESSION["cart_item"] = $_SESSION["cart_item"] + $itemArray;
                    }
                } else {
                    $_SESSION["cart_item"] = $itemArray;
                }
            }
            break;
        case "remove":
            if (!empty($_SESSION["cart_item"])) {
                foreach ($_SESSION["cart_item"] as $k => $v) {
                    if ($_GET["id"] == $k)
                        unset($_SESSION["cart_item"][$k]);
                    if (empty($_SESSION["cart_item"]))
                        unset($_SESSION["cart_item"]);
                }
            }
            break;
        case "empty":
            unset($_SESSION["cart_item"]);
            break;
    }
}
?>
<HTML>

<HEAD>
    <TITLE>Super Market</TITLE>
    <link href="style.css" type="text/css" rel="stylesheet" />

</HEAD>

<BODY>
    <div id="shopping-cart">
        <div class="txt-heading">
            <h3>Supermarket Cart<h3>
        </div>

        <a id="btnEmpty" href="index.php?action=empty">Empty Cart</a>
        <?php
        if (isset($_SESSION["cart_item"])) {
            $total_quantity = 0;
            $total_price = 0;
        ?>
            <table class="tbl-cart" cellpadding="10" cellspacing="1">
                <tbody>
                    <tr>
                        <th style="text-align:left;">Name</th>
                        <th style="text-align:left;">Code</th>
                        <th style="text-align:right;" width="5%">Quantity</th>
                        <th style="text-align:right;" width="10%">SKU</th>
                        <th style="text-align:right;" width="15%">Price</th>
                    </tr>
                    <?php
                    foreach ($_SESSION["cart_item"] as $item) {

                        $item_price =  $item["price"];
                    ?>
                        <tr>
                            <td><strong><?php echo $item["product_name"]; ?></strong></td>
                            <td><?php echo $item["id"]; ?></td>
                            <td style="text-align:right;"><?php echo $item["quantity"]; ?></td>
                            <td style="text-align:right;"><?php echo $item["product_sku"]; ?></td>
                            <td style="text-align:right;"><?php echo "£ " . $item["price"]; ?></td>
                            <td style="text-align:center;"><a href="index.php?action=remove&id=<?php echo $item["id"]; ?>" class="btnRemoveAction"><img src="icon-delete.png" alt="Remove Item" /></a></td>

                        </tr>
                    <?php
                        $total_quantity += $item["quantity"];
                        $total_price += ($item["price"]);
                    }
                    ?>

                    <tr>
                        <td colspan="2" align="right">Total:</td>
                        <td align="right"><?php echo $total_quantity; ?></td>
                        <td align="right" colspan="2"><strong><?php echo "£  " . number_format($total_price, 2); ?></strong></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        <?php
        } else {
        ?>
            <div class="no-records">Your Cart is Empty</div>
        <?php
        }
        ?>
    </div>

    <div id="product-grid">
        <div class="txt-heading">
            <h3>Products<h3>
        </div>
        <?php
        $product_array = $db_handle->runQuery("SELECT * FROM tbl_products LEFT JOIN tbl_specialoffers ON tbl_products.id = tbl_specialoffers.id");
        if (!empty($product_array)) {
            foreach ($product_array as $key => $value) {
        ?>
                <div class="product-item">
                    <form method="post" id="<?php echo $product_array[$key]["id"]; ?>" action="index.php?action=add">
                        <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                        <input type="hidden" id="id" name="id" value="<?php echo $product_array[$key]["id"];  ?>" />
                        <div class="product-tile-footer">
                            <div class="product-title"><?php echo $product_array[$key]["product_name"]; ?></div>
                            <div class="product-price">Original Price <?php echo "£" . $product_array[$key]["product_original_price"]; ?></div>
                            <br>

                            <?php if ($product_array[$key]["dependent_product_id"] >  0) { ?>
                                <br>
                                <div class="product-price">Specical price <?php echo "£" . $product_array[$key]["special_price"]; ?> If you purchase with A </div>
                            <?php } else if ($product_array[$key]["s_id"]) { ?>
                                <br>
                                <div class="product-price">Specical price for product quantity <?php echo  $product_array[$key]["quantity"] ?> at <?php echo "£" . $product_array[$key]["special_price"]; ?></div>
                            <?php } else { ?>
                                <br>
                                <div class="product-price">No offer </div>
                            <?php } ?>
                            <br>
                            <br>
                            <div class="cart-action"><input type="text" class="product-quantity" name="input_quantity" value="1" size="2" /><input type="submit" value="Add to Cart" class="btnAddAction" name="insert" /></div>
                        </div>
                    </form>
                </div>
        <?php
            }
        }
        ?>
    </div>
</BODY>

</HTML>