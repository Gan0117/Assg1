<!--
Assignment 1, SCSM2223-25262 (MenuManage.php)
Group Name: DNF
-->
<?php require 'libs/authpage.php'; ?>
<?php require 'libs/db_connect_PDO.php'; ?>
<?php
$keywordMenu = isset($_REQUEST['keywordMenu']) ? trim($_REQUEST['keywordMenu']) : "";
$sortMenu = isset($_REQUEST['sortMenu']) ? $_REQUEST['sortMenu'] : "type,name";

$sortOptions = [
  'name' => 'name',
  'type,name' => 'type, name',
  'price' => 'price'
];

if (!isset($sortOptions[$sortMenu])) {
  $sortMenu = "type,name";
}

// Assume that no field is being selected to sort the menu rows
$sortFields = ['name'=>'', 'type,name'=>'', 'price'=>''];

// Determine which field is selected for sorting
$sortFields[$sortMenu] = "***";

// Construct the SQL to list menu based on $keywordMenu and $sortMenu variable
$sortSql = $sortOptions[$sortMenu];
$stmt = $pdo->prepare("SELECT * FROM menus WHERE name LIKE :keyword OR type LIKE :keyword ORDER BY $sortSql");
  
try {
  $stmt->execute([':keyword'=>"%$keywordMenu%"]);
  //echo $stmt->debugDumpParams();
} catch (PDOException $ex) {
  echo "Database Error: " . $ex->getMessage();
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Tasty Bites - Manage Menu</title>
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
      <h2>Manage Menu Item</h2>
      <div style="text-align: right">
        <form action="MenuManage.php" method="GET">
          <b>Search:</b> <input type="text" name="keywordMenu" value="<?= htmlspecialchars($keywordMenu) ?>"> <button type="submit">Submit</button>
        </form>
      </div>
      <br>
      <table border="1" width="100%">
        <tr>
          <th>No.</th>
          <th>ID</th>
          <th><a href="MenuManage.php?sortMenu=name&keywordMenu=<?= urlencode($keywordMenu) ?>">Name<?= $sortFields['name'] ?></a></th>
          <th><a href="MenuManage.php?sortMenu=type,name&keywordMenu=<?= urlencode($keywordMenu) ?>">Type <?= $sortFields['type,name'] ?></a></th>
          <th><a href="MenuManage.php?sortMenu=price&keywordMenu=<?= urlencode($keywordMenu) ?>">Price (RM) <?= $sortFields['price'] ?></a></th>
          <th>Operations</th>
        </tr>
<?php 
$no = 1;
while ($row = $stmt->fetch()) { 
?>
        <tr>
          <td align="center"><?= $no ?></td>
          <td align="center"><?= $row['id'] ?></td>
          <td><?= $row['name'] ?></td>
          <td align="center"><?= $row['type'] ?></td>
          <td align="right"><?= number_format($row['price'], 2) ?></td>
          <td align="center">
            <a href="MenuUpdate.php?id=<?= $row['id'] ?>">Update</a> | 
            <a href="MenuDelete.php?id=<?= $row['id'] ?>">Delete</a>
          </td>
        </tr>
<?php 
  $no++;
} 
?>
        <tr>
          <td align="center">...</td>
          <td align="center">...</td>
          <td>...</td>
          <td align="center">...</td>
          <td align="right">...</td>
          <td align="center">
            <a href="MenuAdd.php">Add</a>
          </td>
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
