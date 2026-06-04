<!--
Assignment 1, SCSM2223-25262 (OrderView.php)
Group Name: DNF
-->
<?php require 'libs/authpage.php'; ?>
<?php require 'libs/db_connect_PDO.php'; ?>
<?php
$changeStatus = "";
if (isset($_GET['changeStatus'])) {
  $changeStatus  = $_GET['changeStatus'];
}

// Associatve array to conveniently change the order status
$statusMap = [
  'New' => ['prev'=>'New', 'next'=>'Prepare' ],
  'Prepare' => ['prev'=>'New', 'next'=>'Delivered' ],
  'Delivered' => ['prev'=>'Prepare', 'next'=>'Delivered' ],
];

// Construct the SQL to get user info and ordered menus from the database
$stmtOrder = $pdo->prepare("SELECT orders.*, users.name, users.phone 
                            FROM orders 
                            JOIN users ON orders.user_id = users.id 
                            WHERE orders.id = :id");
                           
$stmtMenu = $pdo->prepare("SELECT order_menus.qty, menus.name, menus.price 
                           FROM order_menus 
                           JOIN menus ON order_menus.menu_id = menus.id 
                           WHERE order_menus.order_id = :id");

// Construct the SQL to get user info and ordered menus from the database                           
$stmtStatusSave = $pdo->prepare("UPDATE orders SET status=:status WHERE id=:id");
  
try {
  $stmtOrder->execute(['id' => $_GET['id']]);
  $order = $stmtOrder->fetch();
  
  if ($changeStatus != "") {
      $newStatus = $statusMap[$order['status']][$changeStatus];
      $stmtStatusSave->execute(['status' => $newStatus, 'id' => $_GET['id']]);
      $order['status'] = $newStatus;
  }

  $stmtMenu->execute(['id' => $_GET['id']]);

  //echo $stmtOrder->debugDumpParams();
  //echo $stmtMenu->debugDumpParams();
  //echo $stmtStatusSave->debugDumpParams();
  
} catch (PDOException $ex) {
  echo "Database Error: " . $ex->getMessage();
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Tasty Bites - Delete Menu</title>
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
    <!-- Order Section -->
    <td>
      <h2>View Customer Order</h2>
      <table cellpadding="3">
        <tr>
          <th align="right">Name: </th>
          <td><?= $order['name'] ?></td>
        </tr>
        <tr>
          <th align="right">Phone Number: </th>
          <td><?= $order['phone'] ?></td>
        </tr>
        <tr>
          <th align="right">Delivery Option: </th>
          <td><?= $order['delivery'] ?></td>
        </tr>
        <tr>
          <th align="right">Date-Time: </th>
          <td><?= $order['datetime'] ?></td>
        </tr>
        <tr>
          <th align="right">Status: </th>
          <td>
            <a href="OrderView.php?changeStatus=prev&id=<?= $_GET['id'] ?>">&lt;&lt;</a> 
            [ <?= $order['status'] ?> ] 
            <a href="OrderView.php?changeStatus=next&id=<?= $_GET['id'] ?>">&gt;&gt;</a>
          </td>
        </tr>
      </table>
      <br>
      <table border="0">
        <tr>
          <th align="center">&nbsp; QTY &nbsp;</th>
          <th align="left">ITEM</th>
          <th align="right">PRICE (RM)</th>
        </tr>
<?php 
$totalPrice = 0;
while ($menu = $stmtMenu->fetch()) { 
    $itemPrice = $menu['qty'] * $menu['price'];
    $totalPrice += $itemPrice;
?>
        <tr>
          <td align="center"><?= $menu['qty'] ?></td>
          <td><?= $menu['name'] ?></td>
          <td align="right"><?= number_format($itemPrice, 2) ?></td>
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
      
      <p><i><?= $order['comments'] ?></i></p>
    </td>
  </tr>

  <!-- Footer -->
  <tr>
    <td align="center">
      <?php include 'libs/footer.php'; ?>
    </td>
  </tr>
</table>

</body>
</html>
