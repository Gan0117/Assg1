<!--
Lab2, SCSM2223-25262 (MenuUpdate.php)
Group Name: ???
-->
<?php require 'libs/authpage.php'; ?>
<?php require 'libs/db_connect_PDO.php'; ?>
<?php 
$id = $_REQUEST['id'];
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : "";

if ($task == "Cancel") {
    header("Location: MenuManage.php");
    exit;
    
} else if ($task == "Update") {
    $stmt_update = $pdo->prepare("UPDATE menus SET name = :name, type = :type, price = :price WHERE id = :id");
    try {
        $stmt_update->execute([
            ':name' => $_POST['name'],
            ':type' => $_POST['type'],
            ':price' => $_POST['price'],
            ':id' => $_POST['id']
        ]);
        header("Location: MenuManage.php");
        exit;
    } catch (PDOException $ex) {
        echo "Database Error: " . $ex->getMessage();
    }
}

$stmt_select = $pdo->prepare("SELECT * FROM menus WHERE id = :id");
try {
    $stmt_select->execute([':id' => $id]);
    $row = $stmt_select->fetch();
} catch (PDOException $ex) {
    echo "Database Error: " . $ex->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Tasty Bites - Update Menu</title>
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
      <h2>Update Menu Item</h2>
      <table width="400" cellspacing="5">
        <form action="MenuUpdate.php" method="POST">
          <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
          
          <tr>
            <th align="right">Name:</th>
            <td>
              <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>
            </td>
          </tr>
          <tr>
            <th align="right">Type:</th>
            <td>
              <select name="type" required>
                <option value="Food" <?= $row['type'] == 'Food' ? 'selected' : '' ?>>Food</option>
                <option value="Drink" <?= $row['type'] == 'Drink' ? 'selected' : '' ?>>Drink</option>
              </select>
            </td>
          </tr>
          <tr>
            <th align="right">Price (RM):</th>
            <td>
              <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($row['price']) ?>" required>
            </td>
          </tr>
          <tr>
            <td></td>
            <td>
              <input type="submit" name="task" value="Update"> 
              <input type="submit" name="task" value="Cancel">
            </td>
          </tr>
        </form>
      </table>
    </td>
  </tr>

  <tr>
    <td align="center">
      <?php include 'libs/footer.php'; ?>
    </td>
  </tr>

</table>

</body>
</html>
