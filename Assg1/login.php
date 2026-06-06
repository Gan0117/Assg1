<?php 
session_start();
require 'libs/db_connect_PDO.php';
?>
<?php 
// User try to login
if (isset($_POST['username'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];
  
  // Query user auth info from the database
  $stmt_userCheck = $pdo->prepare("SELECT * FROM users WHERE username=:username AND password=:password");
  
  try {
    $stmt_userCheck->execute([':username'=>$username, ':password'=>$password]);
    $user = $stmt_userCheck->fetch();
    
    if ($user && $user['role'] == 'ADMIN') {
      setcookie("username", $username, time() + 3600); 
      setcookie("role", $user['role'], time() + 3600);
      header("Location: MenuManage.php");
      exit;
      
    } else if ($user && $user['role'] == 'CASHIER') {
      setcookie("username", $username, time() + 3600); 
      setcookie("role", $user['role'], time() + 3600);
      header("Location: OrderList.php");
      exit;
      
    } else if ($user && $user['role'] == 'CUSTOMER') {
      setcookie("username", $username, time() + 900); 
      setcookie("role", $user['role'], time() + 900);
      $_SESSION['email'] = $user['email'];
      header("Location: OrderForm.php");
      exit;
    }
    
  } catch (PDOException $ex) { 
    echo "Database Error: " . $ex->getMessage();
  }
  
}

// User already succesful login
if (isset($_COOKIE['username'])) {
  header("Location: Menu.php");
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Tasty Bites - Login</title>
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
    <!-- Login Section -->
    <td align="center">
      <form action="Login.php" method='POST'>
        Username: <input type='text' name='username'><br><br>
        Password: <input type='password' name='password'><br><br>
        <button type='submit'>Login</button>
      </form>
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



