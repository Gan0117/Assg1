<?php require 'libs/db_connect_PDO.php'; ?>
<?php
// Read order info from POST method safely
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$food_id = $_POST['food_id'] ?? '';
$qtyFood = $_POST['qtyFood'] ?? '';
$drink_id = $_POST['drink_id'] ?? '';
$qtyDrink = $_POST['qtyDrink'] ?? '';
$delivery = $_POST['delivery'] ?? '';
$comments = $_POST['comments'] ?? '';

// Prevent blank orders from bypassing the system
if ($qtyFood == '' && $qtyDrink == '') {
    header("Location: OrderForm.php");
    exit;
}

// User confirm to order
if (isset($_POST['button']) && $_POST['button'] == "Confirm") {
    
    // Force PDO to throw exceptions on errors
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $user_id = -1;
    $order_id = -1;
    
    date_default_timezone_set('Asia/Kuala_Lumpur');
    $datetime = date('Y-m-d H:i:s');
    
    try {
        // 1. Check for returning customer by email
        $stmt_userCheck = $pdo->prepare("SELECT * FROM users WHERE email=:email");
        $stmt_userCheck->execute(['email' => $email]);
        
        if ($user = $stmt_userCheck->fetch()) {
            $user_id = $user['id'];
        } else {
            // Register new user (Phone as password, Email as username)
            $stmt_userSave = $pdo->prepare("INSERT INTO users (name, email, phone, role, username, password) VALUES (:name, :email, :phone, 'CUSTOMER', :username, :password)");
            $stmt_userSave->execute([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'username' => $email,
                'password' => $phone
            ]);
            $user_id = $pdo->lastInsertId();
        }

        // 2. Save Order
        $stmt_orderSave = $pdo->prepare("INSERT INTO orders (user_id, datetime, delivery, comments, status) VALUES (:user_id, :datetime, :delivery, :comments, 'New')");
        $stmt_orderSave->execute([
            'user_id' => $user_id,
            'datetime' => $datetime,
            'delivery' => $delivery,
            'comments' => $comments
        ]);
        $order_id = $pdo->lastInsertId(); 

        // 3. Save Order Menus
        $stmt_ordermenuSave = $pdo->prepare("INSERT INTO order_menus (order_id, menu_id, qty) VALUES (:order_id, :menu_id, :qty)");
        
        if ($qtyFood != '' && $qtyFood > 0) {
            $stmt_ordermenuSave->execute(['order_id' => $order_id, 'menu_id' => $food_id, 'qty' => $qtyFood]);
        }
        if ($qtyDrink != '' && $qtyDrink > 0) {
            $stmt_ordermenuSave->execute(['order_id' => $order_id, 'menu_id' => $drink_id, 'qty' => $qtyDrink]);
        }

        // 4. Redirect automatically to Menu.php
        header("Location: Menu.php");
        exit;

    } catch (PDOException $ex) { 
        die("<h2 style='color:red;'>CRITICAL DATABASE ERROR: " . $ex->getMessage() . "</h2>");
    }
}

// Query selected food & drink for confirmation display
$stmt_food = $pdo->prepare("SELECT * FROM menus WHERE id=:id");
$stmt_drink = $pdo->prepare("SELECT * FROM menus WHERE id=:id");

$foodPrice = 0;
$drinkPrice = 0;
$totalPrice = 0;

$food = NULL;
$drink = NULL;

try {
    if ($food_id) {
        $stmt_food->execute(['id' => $food_id]);
        if ($food = $stmt_food->fetch()) {
            $foodPrice = ($qtyFood != '') ? $food['price'] * $qtyFood : 0;
        }
    }

    if ($drink_id) {
        $stmt_drink->execute(['id' => $drink_id]);
        if ($drink = $stmt_drink->fetch()) {
            $drinkPrice = ($qtyDrink != '') ? $drink['price'] * $qtyDrink : 0;
        }
    }

    $totalPrice = $foodPrice + $drinkPrice;
  
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

<table border="0" width="100%">
  <tr>
    <td align="center">
        <?php include 'libs/header.php'; ?>
    </td>
  </tr>

  <tr>
    <td align="center">
        <?php include 'libs/navigation.php'; ?>
    </td>
  </tr>

  <tr>
    <td>
      <form action="OrderConfirm.php" method="POST">
        <h2>Please Confirm Your Order</h2>
        <hr>
        <h3>Customer Information</h3>
        
        <table cellpadding="3">
          <tr>
            <th align="right">Name: </th>
            <td><?= htmlspecialchars($name) ?></td>
          </tr>
          <tr>
            <th align="right">Email: </th>
            <td><?= htmlspecialchars($email) ?></td>
          </tr>
          <tr>
            <th align="right">Phone Number: </th>
            <td><?= htmlspecialchars($phone) ?></td>
          </tr>
          <tr>
            <th align="right">Delivery Option: </th>
            <td><?= htmlspecialchars($delivery) ?></td>
          </tr>
        <tr>
          <th align="right">Date-Time: </th>
          <td id="clock"></td>
        </tr>
        </table>
        <br><hr>
        <h3>Order Details</h3>

        <table border="0" width="320">
          <tr>
            <th align="center">&nbsp; QTY &nbsp;</th>
            <th align="left">ITEM</th>
            <th align="right">PRICE (RM)</th>
          </tr>
<?php if ($qtyFood != '') { ?>
          <tr>
            <td align="center"><?= htmlspecialchars($qtyFood) ?></td>
            <td><?= htmlspecialchars($food['name'] ?? '') ?></td>
            <td align="right"><?= number_format($foodPrice, 2) ?></td>
          </tr>
<?php } ?>
<?php if ($qtyDrink != '') { ?>
          <tr>
            <td align="center"><?= htmlspecialchars($qtyDrink) ?></td>
            <td><?= htmlspecialchars($drink['name'] ?? '') ?></td>
            <td align="right"><?= number_format($drinkPrice, 2) ?></td>
          </tr>
<?php } ?>          
          <tr>
            <td colspan="3">&nbsp;</td>
          </tr>
          
          <tr>
            <th colspan="2" align="left">SUBTOTAL</th>
            <td align="right">RM <?= number_format($totalPrice, 2) ?></td>
          </tr>
          
          <tr>
            <th colspan="2" align="left">SST (6%)</th>
            <td align="right">RM <?= number_format($totalPrice * 0.06, 2) ?></td>
          </tr> 
          
          <tr>
            <th colspan="2" align="left">TOTAL</th>
            <td align="right">RM <?= number_format($totalPrice + $totalPrice * 0.06, 2) ?></td>
          </tr> 
        </table>
        
        <p>
          <b>Additional Comments: </b><i><?= htmlspecialchars($comments) ?></i>
        </p>

        <input type="hidden" name="name" value="<?= htmlspecialchars($name) ?>">
        <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
        <input type="hidden" name="phone" value="<?= htmlspecialchars($phone) ?>">
        <input type="hidden" name="food_id" value="<?= htmlspecialchars($food_id) ?>">
        <input type="hidden" name="qtyFood" value="<?= htmlspecialchars($qtyFood) ?>">
        <input type="hidden" name="drink_id" value="<?= htmlspecialchars($drink_id) ?>">
        <input type="hidden" name="qtyDrink" value="<?= htmlspecialchars($qtyDrink) ?>">
        <input type="hidden" name="delivery" value="<?= htmlspecialchars($delivery) ?>">
        <input type="hidden" name="comments" value="<?= htmlspecialchars($comments) ?>">

        <p>
          <input type="submit" name="button" value="Confirm">
          <input type="button" onclick="history.back()" value="Cancel">
        </p>
      </form>
    </td>
  </tr>

  <tr>
    <td colspan="2" align="center">
      <?php include 'libs/footer.php'; ?>
    </td>
  </tr>
</table>
</body>
</html>

<script>
function updateClock() {
    const now = new Date();
    document.getElementById('clock').textContent = now.toLocaleString(); 
}
setInterval(updateClock, 1000);
updateClock();
</script>