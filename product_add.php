<?php
include "header.php";
function check_input($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}
?>

<body>
<?php
navbar();
echo '	<section class="container">
			<form method="post" class="form">
				<header>New Product</header>
				<div class="input-box">
					<label for="name">Name:</label>
    				<input type="text" id="prod_name" name="name" required>
				</div>
				<div class="input-box">
    				<label for="description">Description:</label>
    				<textarea id="prod_desc" name="desc" required></textarea>
				</div>
				<div class="input-box">
    				<label for="price">Price:</label>
    				<input type="number" id="prod_price" name="price" step="0.01" min="0" required>
				</div>
				<input type="submit" name="add_prod" value="Add Product" class="add">    
			</form>
		</section>';

if(isset($_POST['add_prod'])){
	$conn = new mysqli("localhost", "root", "", "nahradne_zadanie");
	if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
	}
	$name = check_input($_POST['name']);
	$desc = check_input($_POST['desc']);
	$price = floatval($_POST['price']);
	$sql = "INSERT INTO product (name, description, price) VALUES (?, ?, ?)";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("ssd", $name, $desc, $price);
	if ($stmt->execute()) {
		echo "<div>PRODUCT HAS BEEN ADDED!/div>";
		$result = $conn->query("SELECT MAX(id) AS max_id FROM product");
    	$row = $result->fetch_assoc();
    	$new_id = $row['max_id'] + 1;
    	$conn->query("SET @count = 0;");
    	$conn->query("UPDATE product SET id = @count:= @count + 1;");
		header('Location: index.php');
	}
	else {
		echo "ERROR: " . $stmt->error;
}
$stmt->close();
$conn->close();
}
?>
</body>
</html>