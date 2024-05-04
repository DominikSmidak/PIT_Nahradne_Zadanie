<?php
include "header.php";

navbar();

if(isset($_SESSION['email']) and isset($_SESSION['password'])){
    echo "<div>Welcome, " . $_SESSION['name'];
    ?>
    <form method="POST">
        <input type="submit" name="odhlasenie" value="Logout" class="remove">
    </form></div>
    <?php
} 
else {
	login();
}

// Prihlásenie 
if(isset($_POST['login'])){
	if(isset($_POST['email']) and isset($_POST['password'])){
		$conn = new mysqli("localhost", "root", "", "nahradne_zadanie");
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$email = $_POST['email'];
		$password = hash('sha512', $_POST['password']);
		$sql = "SELECT * FROM user WHERE email = ? AND password_hash = ?";
		$stmt = $conn->prepare($sql);

		if ($stmt) {
			$stmt->bind_param("ss", $email, $password);
			$stmt->execute();
			$result = $stmt->get_result();
			if ($result->num_rows > 0) {
				echo "S";
				while($row = $result->fetch_assoc()){
				$_SESSION['email'] = $email;
				$_SESSION['password'] = $password;
				$_SESSION['name'] = $row['name'];
				header("Refresh:0");
				} 
			}
			else {
				echo "<div>WRONG LOGIN !</div>";
			}
			$stmt->close();
		} 
		else {
			echo "STATEMENT FAILED !";
		}
    $conn->close();
	}
};

// Odhlásenie
if (isset($_POST['odhlasenie'])) {
    session_unset();
    session_destroy();
	remove_cart();
    header("Refresh:0");
}
?>
</body>
</html>
