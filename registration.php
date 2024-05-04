<?php
include "header.php";
?>

<body>
<?php
navbar();
if (isset($_SESSION['name'])){
	 echo "<div>Welcom, " . $_SESSION['name'];
    ?>
    <form method="POST">
        <br>
        <input type="submit" name="odhlasenie" value="Logout">
    </form>
    </div>
    <?php
}
else{
	registration();
}

// Odhlásenie
if (isset($_POST['odhlasenie'])) {
    session_unset();
    session_destroy();
	remove_cart();
    header("Refresh:0");
}

// Registrácia
if (isset($_POST['registracia'])) {
	$email = $_POST['remail'];
	if (isset($_POST['rusername']) && isset($_POST['rpassword']) && isset($_POST['remail']) && isValidEmail($email)){
        $conn = new mysqli("localhost", "root", "", "nahradne_zadanie");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $rusername = $_POST['rusername'];
        $rpassword = hash('sha512', $_POST['rpassword']);
        $remail = $_POST['remail'];
        $sql = "INSERT INTO user (`name`, `password_hash`, `email`) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sss", $rusername, $rpassword, $remail);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                echo "<br>";
                ?>
                <div>
				<p>Registration Passed !</p>
				<p><a href="login.php">[Click here to login!]</a></p>
               </div>
                <?php
            } else {
                echo "<h1>Registration has failed.<h1>";
				echo "ERROR: " . $stmt->error; 
            }
            $stmt->close();
        } else {
            echo "STATEMENT FAILED!";
        }
        $conn->close();
    }
	else{
		if(!isValidEmail($email)){
			echo "<div>Wrong mail.</div>";
		}
	}
} 
?>
</body>
</html>