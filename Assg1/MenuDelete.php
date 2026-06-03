<!--
Lab2, SCSM2223-25262 (MenuDelete.php)
Group Name: ???
-->
<?php require 'libs/authpage.php'; ?>
<?php require 'libs/db_connect_PDO.php'; ?>
<?php 
$id = $_REQUEST['id'];
// 使用 isset() 检查 task 是否存在，彻底解决第 9 行的报错警告
$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : "";

if ($task == "Delete" || $task == "Cancel") {
    if ($task == "Cancel") {
        header("Location: MenuManage.php");
        exit;
    } else if ($task == "Delete") {
        $stmt_delete = $pdo->prepare("DELETE FROM menus WHERE id = :id");
        try {
            $stmt_delete->execute([':id' => $_POST['id']]);
            header("Location: MenuManage.php");
            exit;
        } catch (PDOException $ex) {
            echo "Database Error: " . $ex->getMessage();
        }
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
	<title>Tasty Bites - Delete Menu</title>
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
      <h2>Delete Menu Item</h2>
      <table width="400" cellspacing="5">
        <form action="MenuDelete.php" method="POST">
          <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
          <tr>
            <th align="right">Name:</th>
            <td><?= htmlspecialchars($row['name']) ?></td>
          </tr>
          <tr>
            <th align="right">Type:</th>
            <td><?= htmlspecialchars($row['type']) ?></td>
          </tr>
          <tr>
            <th align="right">Price (RM):</th>
            <td><?= number_format($row['price'], 2) ?></td>
          </tr>
          <tr>
            <td></td>
            <td>
              <input type="submit" name="task" value="Delete"> 
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