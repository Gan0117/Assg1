<!--
Assignment 1, SCSM2223-25262 (OrderList.php)
Group Name: DNF
-->
<?php require 'libs/authpage.php'; ?>
<?php require 'libs/db_connect_PDO.php'; ?>
<?php
$keywordOrder = "";
if (isset($_POST['keywordOrder'])) {
    $keywordOrder = $_POST['keywordOrder'];
}

$sortOrder = "datetime desc"; // default sort order is datetime, descending 
if (isset($_GET['sortOrder'])) {
    $sortOrder = $_GET['sortOrder'];
}

// Assume that no field is being selected to sort the menu rows
$sortFields = ['datetime desc'=>'', 'name'=>'', 'delivery'=>'', 'phone'=>''];

// Determine which field is selected for sorting
$sortFields[$sortOrder] = "***";

// Construct the SQL to list customer'a orders based on $keywordOrder and $sortOrder variable
$stmt = $pdo->prepare("SELECT orders.*, users.name, users.phone, 
                              (SELECT COUNT(*) FROM order_menus WHERE order_id = orders.id) AS menu_ordered
                        FROM orders 
                        JOIN users ON orders.user_id = users.id 
                        WHERE users.name LIKE :keyword 
                          OR users.phone LIKE :keyword 
                          OR orders.delivery LIKE :keyword 
                          OR orders.status LIKE :keyword
                        ORDER BY $sortOrder");
  
try {
  $stmt->execute(['keyword' => "%$keywordOrder%"]);
  //echo $stmt->debugDumpParams();
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
      <h2>List Customer Order</h2>
      <div style="text-align: right">
        <form action="OrderList.php" method="POST">
          <b>Search:</b> <input type="text" name="keywordOrder" value="<?= $keywordOrder ?>"> <button type="submit">Submit</button>
        </form>
      </div>
      <br>
      <table border="1" width="100%">
        <tr>
          <th>No.</th>
          <th>ID</th>
          <th><a href="OrderList.php?sortOrder=datetime+desc">Date-Time<?= $sortFields['datetime desc'] ?></a></th>
          <th><a href="OrderList.php?sortOrder=name">Name <?= $sortFields['name'] ?></a></th>
          <th><a href="OrderList.php?sortOrder=delivery">Delivery<?= $sortFields['delivery'] ?></a></th>
          <th><a href="OrderList.php?sortOrder=phone">Phone<?= $sortFields['phone'] ?></a></th>
          <th>Menu Ordered</th>
          <th><a href="OrderList.php?sortOrder=status">Status</a></th>
          <th>Operations</th>
        </tr>
<?php 
$no = 1;
while ($row = $stmt->fetch()) { 
?>
        <tr>
          <td align="center"><?= $no ?></td>
          <td align="center"><?= $row['id'] ?></td>
          <td align="center"><?= $row['datetime'] ?></td>
          <td><?= $row['name'] ?></td>
          <td><?= $row['delivery'] ?></td>
          <td><?= $row['phone'] ?></td>
          <td align="center"><?= $row['menu_ordered'] ?></td>
          <td align="center"><?= $row['status'] ?></td>
          <td align="center">
            <a href="OrderView.php?id=<?= $row['id'] ?>&task=">View</a>
          </td>
        </tr>
<?php 
  $no++;
} 
?>
        <tr>
          <td align="center">...</td>
          <td align="center">...</td>
          <td align="center">...</td>
          <td>...</td>
          <td>...</td>
          <td>...</td>
          <td align="center">...</td>
          <td align="center">...</td>
          <td align="center">...</td>
        </tr>
      </table>
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
