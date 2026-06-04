<!--
Assignment 1, SCSM2223-25262 (MenuAdd.php)
Group Name: DNF
-->
<?php require 'libs/authpage.php'; ?>
<?php require 'libs/db_connect_PDO.php'; ?>
<?php 
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : "";
$form_complete = FALSE;

if ($task == "Cancel") {
    header("Location: MenuManage.php");
    exit;
} else if ($task == "Add") {
  $stmt = $pdo->prepare("INSERT INTO menus (name, type, price) VALUES (:name, :type, :price)");

  try {
    $stmt->execute([
      ':name' => $_POST['name'],
      ':type' => $_POST['type'],
      ':price' => $_POST['price']
    ]);

    header("Location: MenuManage.php");
    exit;
  } catch (PDOException $ex) {
    echo "Database Error: " . $ex->getMessage();
  }
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Tasty Bites - Add Menu</title>
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
      <h2>Add Menu Item</h2>
      <table width="400" cellspacing="5">
        <form action="MenuAdd.php" method="POST">
          <tr>
            <th align="right">Name:</th>
            <td>
              <input type="text" name="name" required>
            </td>
          </tr>
          <tr>
            <th align="right">Type:</th>
            <td>
              <select name="type">
                <option value=""></option>
                <option value="Drink">Drink</option>
                <option value="Food">Food</option>
              </select>
            </td>
          </tr>
          <tr>
            <th align="right">Price (RM):</th>
            <td>
              <input type="number" step="0.01" name="price" required>
            </td>
          </tr>
          <tr>
            <td></td>
            <td>
              <input type="submit" name="task" value="Add"> 
              <input type="button" onclick="history.back()" value="Cancel">
            </td>
          </tr>
        </form>
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
