<!--
Assignment 1, SCSM2223-25262 (Menu.php)
Group Name: DNF
-->
<?php require 'libs/db_connect_PDO.php'; ?>

<?php
// Start query foods and drinks from database here
$stmt_drink = $pdo->prepare("SELECT * FROM menus WHERE type='Drink' ORDER BY name ");
$stmt_food = $pdo->prepare("SELECT * FROM menus WHERE type='Food' ORDER BY name ");

try {
  $stmt_drink->execute();
  $stmt_food->execute();
  
} catch (PDOException $ex) { 
  echo "Database Error: " . $ex->getMessage();
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Tasty Bites - List Menu</title>
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
    <!-- Menu Section -->
    <td>
      <h2>Foods</h2>
      <ol>
<?php while ($row = $stmt_food->fetch()) { ?>
        <li><?=$row['name']?> - RM <?=number_format($row['price'], 2)?></li>
<?php } ?>
      </ol>
      
      <h2>Drinks</h2>
      <ol>
<?php while ($row = $stmt_drink->fetch()) { ?>
        <li><?=$row['name'] ?> - RM <?= number_format($row['price'], 2) ?></li>
<?php } ?>
      </ol>
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
