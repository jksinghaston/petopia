<?php
require_once('php/mainLogCheck.php');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to the login page
    header("Location: login.php");
    exit(); // Stop further execution
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petopia</title>
    <link href="css/basket.css" rel="stylesheet" type="text/css">

    <!--[Google Fonts]-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!--Nunito Font-->
    <link
        href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,700;1,800&family=Work+Sans:wght@700;800&display=swap"
        rel="stylesheet">

    <!--Box Icons-->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!--
        [Navigation & Footer]
    -->
    <script src="templates/navigationTemplate.js"></script>
    <link href="css/navigation.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/footer.css">

    <!--Flickity-->
    <!--CSS Templates-->
    <link rel="stylesheet" href="https://unpkg.com/flickity@2/dist/flickity.min.css">
    <link rel="stylesheet" href="templates/hero-banner.css">
    <!--JS-->
    <script src="https://unpkg.com/flickity@2/dist/flickity.pkgd.min.js"></script>

</head>

<body>

<header></header>
    
    <main>
        <!--Hero Banner-->
        <section class="hero-banner">
            <!--Hero Banner Image-->
            <div class="hero-banner-image"><img src="assets/Homepage/hero-banner2.jpg" alt=""></div>

            <!--Hero Banner Text Container-->
            <div class="hero-banner-left">

                <div class="hero-banner-content">
                    <h2>Basket</h2>
                    <p></p>
                </div>
            </div>
        </section>
        <!--Basket Container-->
        <section class="basket-container">
            <!--Basket Container-->
                <div class="top-container">
                    <h3>Your basket</h3>
                </div>
            <!--Basket Table-->
            <?php
            require_once('php/connectdb.php');

            try {
                // Fetch basket items for the currently logged-in user
                $username = $_SESSION['username'];
                $query = "SELECT product.Product_ID, product.Name, basket.Quantity, basket.Subtotal
                          FROM basket 
                          JOIN product ON product.Product_ID = basket.Product_ID 
                          JOIN customer ON basket.Customer_ID = customer.Customer_ID
                          WHERE customer.Username = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$username]);
                $basketItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Display basket items in a table
                if ($basketItems) {
            ?>
                    <table cellspacing="10" cellpadding="15" class="productTable">
                        <tr class="basket-top">
                            <th align='center'><b>Image</b></th>
                            <th align='center'><b>Product Name</b></th>
                            <th align='center'><b>Quantity</b></th>
                            <th align='center'><b>Subtotal</b></th>
                            <th align='center'><b>Action</b></th>
                        </tr>
                        <?php
                        foreach ($basketItems as $item) {
                            echo  "<tr class='basket-row' data-product-id='" . $item['Product_ID'] . "'>
                                        <td align='center'><img src='assets/Homepage/hero-banner2.jpg' alt='Product Image' width='50' height='50'></td>
                                        <td align='center'>" . $item['Name'] . "</td>
                                        <td align='center'>" . $item['Quantity'] . "</td>
                                        <td align='center'>£" . $item['Subtotal'] . "</td>
                                        <td align='center'><button class='remove-basket'>Remove</button></td>
                                    </tr>";
                        }
                        ?>
                    </table>

                    <script>
                        // JavaScript code for removing items from the basket
                        // This script can be kept as it is
                    </script>

                <?php
                } else {
                    echo  "<p>No items in the basket.</p>\n"; // No items found in the basket
                }
            } catch (PDOException $ex) {
                echo "Sorry, a database error occurred! <br>";
                echo "Error details: <em>" . $ex->getMessage() . "</em>";
            }
            ?>



        </section>
            
        
    </main>
</body>
    
    <footer>
        &copy; 2023 Petopia
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var removeButtons = document.querySelectorAll('.remove-basket');

            removeButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    var productId = this.closest('.basket-row').dataset.productId;

                    // Make an AJAX request to remove the item
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', 'remove_from_basket.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                    xhr.onreadystatechange = function () {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            // Refresh the page or update the UI as needed
                            location.reload();
                        } else if (xhr.readyState == 4 && xhr.status != 200) {
                            alert('Error removing item from the basket.');
                        }
                    };

                    xhr.send('productId=' + encodeURIComponent(productId));
                });
            });
        });
    </script>

</body>
</html>