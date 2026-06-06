<!--
Assignment 1, SCSM2223-25262 (OrderForm.php)
Group Name: DNF
-->
<?php
session_start();
require 'libs/db_connect_PDO.php';
?>

<?php
// Check for returned customer
$name = "";
$email = "";
$phone = "";


if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    try {
        $stmt_user = $pdo->prepare("SELECT * FROM users WHERE email=:email");
        $stmt_user->execute(['email' => $email]);
        if ($row = $stmt_user->fetch()) {
            $name = $row['name'];
            $phone = $row['phone'];
        }
    } catch (PDOException $ex) {
        echo "Database Error: " . $ex->getMessage();
    }
}

// Start query foods and drinks from database here
$stmt_food = $pdo->prepare("SELECT * FROM menus WHERE type='Food' ORDER BY name ");
$stmt_drink = $pdo->prepare("SELECT * FROM menus WHERE type='Drink' ORDER BY name ");

try {
  $stmt_food->execute();
  $stmt_drink->execute();
  
} catch (PDOException $ex) { 
  echo "Database Error: " . $ex->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Tasty Bites - Order Form</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="main_style.css"  rel="stylesheet" type="text/css">
</head>

<body>

<!-- Main Layout Table -->
<table border="0" width="100%">
  <!-- Header -->
  <tr>
    <td align="center">
        <?php include 'libs/header.php'; ?>
    </td>
  </tr>

  <!-- Navigation -->
  <tr>
    <td align="center">
        <?php include 'libs/navigation.php'; ?>
    </td>
  </tr>

  <!-- Content Row -->
  <tr>
    <!-- Order Form Section -->
    <td>
      <form action="OrderConfirm.php" method="POST">
        <h2>Place Your Order</h2>
        <hr>
        <h3>Customer Information</h3>
        <table cellpadding="3">
          <tr>
            <td align="right">Name: </td>
            <td><input type="text" name="name" size="30" value="<?= htmlspecialchars($name) ?>" required></td>
          </tr>
          <tr>
            <td align="right">Email: </td>
            <td><input type="text" name="email" size="20" value="<?= htmlspecialchars($email) ?>" required></td>
          </tr>
          <tr>
            <td align="right">Phone Number: </td>
            <td><input type="text" name="phone" size="20" value="<?= htmlspecialchars($phone) ?>" required></td>
          </tr>
        </table>
        <br><hr>
        <h3>Order Details</h3>
        <table cellpadding="5">
          <tr>
            <td align="right">Food:</td>
            <td>
              <select name="food_id">
              <?php while ($row = $stmt_food->fetch()) { ?>
                <option value="<?=$row['id'] ?>"><?= $row['name']?></option>
              <?php } ?>
              </select>
            </td>
            <td align="right">Quantity:</td>
            <td><input type="number" name="qtyFood" size="5" min=1 max=5></td>
          </tr>
          <tr>
            <td align="right">Drink:</td>
            <td>
              <select name="drink_id">
                <?php while ($row = $stmt_drink->fetch()) { ?>
                  <option value="<?=$row['id'] ?>"><?= $row['name']?></option>
                <?php } ?>
              </select>
            </td>
            <td align="right">Quantity: </td>
            <td><input type="number" name="qtyDrink" size="5" min=1 max=5></td>
          </tr>
          <tr>
            <td align="right">Delivery Option: </td>
            <td colspan="3">
              <input type="radio" name="delivery" value="Dine-in" required> Dine-in
              <input type="radio" name="delivery" value="Takeaway" required> Takeaway
            </td>
          </tr>
        </table>
        <br>
        Additional Comments: <br>
        <textarea name="comments" rows="3" cols="30"></textarea><br><br>

        <input type="submit" value="Submit Order">
        <input type="reset" value="Reset">
      </form>
    </td>
  </tr>

  <!-- Footer -->
  <tr>
    <td colspan="2" align="center">
      <?php include 'libs/footer.php'; ?>
    </td>
  </tr>

</table>

</body>
</html>
