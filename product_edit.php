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
				<div class="input-box">
					<label for="name">Product Name:</label>
    				<input type="text" id="prod_name" name="name" required>
				</div>
				<div class="input-box">
    				<label for="description">Product Description:</label>
    				<textarea id="prod_desc" name="desc" required></textarea>
				</div>
				<div class="input-box">
    				<label for="price">Product Price:</label>
    				<input type="number" id="prod_price" name="price"  step = "0.01" min = "0"required>
				</div>
				<input type="submit" name="add_prod" value="Modify Product" class="add">    
			</form>
		</section>';
	
if(isset($_POST['add_prod'])){
	$conn = new mysqli("localhost", "root", "", "nahradne_zadanie");
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	$id = $_GET['id'];
	$name = check_input($_POST['name']);
	$description = check_input($_POST['desc']);
	$price = floatval($_POST['price']);
	$sql = "UPDATE product SET name=?, description=?, price=? WHERE id=?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("ssdi", $name, $description, $price, $id);
	if ($stmt->execute()) {
		header('Location: index.php');
	} else {
		echo "ERROR: " . $stmt->error;
	}
	$stmt->close();
	$conn->close();
}
?>
</body>
</html>