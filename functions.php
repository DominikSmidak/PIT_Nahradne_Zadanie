<?php
session_start();

// Šablóna pre navbar
function navbar(){
	?>
	<nav class="nav1">
		<?php
			if(isset($_SESSION['email']) && $_SESSION['password']){
				echo "<p><a href='login.php'>Welcome, " . $_SESSION['name'] . "</a></p>";
			} else {
				echo "<a href='login.php'>Login</a>";
			}
		?>
	</nav>
	<nav class="nav2">
    <a href="index.php">Home</a>
	<a href= "cart.php">Cart(<?php echo get_cart_total(); ?>)</a>
	</nav>
<?php	
}

// Výpis produktov
function allProducts(){
    $conn = new mysqli("localhost", "root", "", "nahradne_zadanie");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
	}
    $sql = "SELECT * FROM product";
    $result = $conn->query($sql);

    if ($result !== false && $result->num_rows > 0) {
        echo '<div class="container">';
        while ($row = $result->fetch_assoc()) {
            echo '<div class="product">'; 
            echo '<a href="product_info.php?id=' . $row["id"] . '">';
            echo "<p>" . $row["name"] . "</p></a>";
            echo "<p>" . $row["description"] . "</p>";
            echo "</div>";
        }
        echo '</div>';
		echo '<div>';
		if(isset($_SESSION['name'])){
			echo '<a href="product_add.php"><button class="add">Add Product</button></a>';
		}
		echo '</div>';
		
    } else {
        echo "NO RESULTS !";
    }
    $conn->close();
}

// Výpis daného produktu
function productInfo($id){
	$conn = new mysqli("localhost", "root", "", "nahradne_zadanie");
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
		}
	$sql = "SELECT * FROM `product` WHERE `id` = ?";
	if ($stmt = $conn->prepare($sql)) {
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$result = $stmt->get_result();
		
		if ($result !== false && $result->num_rows > 0) {
			while($row = $result->fetch_assoc()){
				echo '<div class = "container">';
				echo '<p>Name : ' . $row["name"] . "</p>";
				echo '<p>Desc : ' . $row["description"] . "</p>";
				echo '<p>Price : ' . $row["price"] . " €</p>";	
				echo '<p>Price + VAT: ' . number_format($row["price"]* 1.2, 2) . " €</p>";
				echo '<form method="post">
						<input type="hidden" name="prod_id" value="' . $row['id'] . '">
						<input type="hidden" name="prod_name" value="' . $row['name'] . '">
						<input type="hidden" name="prod_price" value="' . $row['price'] . '">
						<input type="submit" name="add_to_cart" value="Add to Cart" class="add">    
						</form>
						<br>';
				if(isset($_SESSION['name'])){
					echo '<form method="post">
						<input type="hidden" name="prod_id" value="' . $row['id'] . '">
						<input type="submit" name="delete_prod" value="Delete Product" class="remove">    
						</form>
						<br>';						
					echo '<a href = "product_edit?id=' . $row["id"] . '"><button class="add">Edit Product</button></a>';
				}
			}
		}
		
		else {
			echo "Product ID out of bounds! NT NT";
		}	
		$stmt->close();
	}
 else {
	echo "STATEMENT FAILED!";
	}
}

// Prihlásenie
function login(){
	?>
	<div class="container">
	<form method="post" class="form">
		<label>Email:</label>
		<input type="text" name="email" placeholder="email" required>
		<br>
		<label>Password:</label>
		<input type = "password" name="password" placeholder = "password" required>
		<br>
		<input type="submit" name = "login" value="Log In" class="add">
		<label>No account? Register <a href = "registration.php">here</a>.</label><br>
	</form>
	</div>
	<?php 
}

// Registrácia
function registration(){
	?>
	<div class="container">
		<form method = "POST">
			<label>Username: </label>
			<input type = "text" name = "rusername" required>
			<br>
			<label>Email: </label>
			<input type = "text" name = "remail" required>
			<br>
			<label>Password: </label>
			<input type = "password" name = "rpassword" required>
			<br>
			<input type = "submit" name = "registracia" value = "Log On" class="add">
		</form>
	</div>
	<?php
}

// Validný mail
function isValidEmail($email) {
	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
		return true;
	}
	return 0;
}

// Pridanie do košíka
function add_to_cart($product_id, $product_name, $product_price) {
    if (!isset($_COOKIE['cart'])) {
        $cart = array();
    } else {
        $cart = json_decode($_COOKIE['cart'], true);
    }
    if (isset($cart[$product_id])) {
          $cart[$product_id]['quantity']++;
    } 
	else {
        $cart[$product_id] = array(
            'name' => $product_name,
            'price' => $product_price,
            'quantity' => 1
        );
    }
	echo "<script type='text/javascript'>alert('ADDED TO CART !');</script>";
    setcookie('cart', json_encode($cart), time() + 3600, '/');
    header('Location: product_info.php?id=' . $product_id);
    exit();
}

// Celková suma
function get_cart_total_price() {
    $total = 0;
    if (isset($_COOKIE['cart'])) {
        $cart = json_decode($_COOKIE['cart'], true);
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
    }
    return $total;
}

function get_cart_total() {
    $total = 0;
    if (isset($_COOKIE['cart'])) {
        $cart = json_decode($_COOKIE['cart'], true);
        foreach ($cart as $item) {
            $total += 1 * $item['quantity'];
        }
    }
    return $total;
}

// Produkty v košíku
function displayAllProductsInCart() {
    $conn = new mysqli("localhost", "root", "", "nahradne_zadanie");
	if (isset($_COOKIE['cart'])) {
        $cart = json_decode($_COOKIE['cart'], true);
        if (!empty($cart)) {
            foreach ($cart as $product_id => $item) {
                $query = "SELECT * FROM product WHERE id = $product_id";
                $result = $conn->query($query);
                if ($result->num_rows > 0) {
                    $product = $result->fetch_assoc();
                    echo '<div>';
                    echo '<h3>' . $product['name'] . '</h3>';
                    echo '<p>Price: ' . $product['price'] . '€</p>';
					echo '<p>Price + VAT: ' . number_format($product['price']* 1.2, 2) . '€</p>';
                    echo '<p>Quantity: ' . $item['quantity'] . '</p>';
                    echo '<form method="post">';
                    echo '<input type="hidden" name="remove_from_cart" value="' . $product['id'] . '">';
                    echo '<input type="submit" value="Remove from Cart" class="remove">';
                    echo '</form>';
                    echo '</div>';
                }
            }
        } else {
            if(isset($_SESSION['name'])){
				echo '<div>Cart is empty.</div>';
				}
			else{
				echo '<div>Need to Log In.</div>';
	}
        }
    } else {
		echo '<div>Cart is empty.</div>';
	}
}

// Odstránenie z košíku
function remove_from_cart($product_id) {
    if (isset($_COOKIE['cart'])) {
        $cart = json_decode($_COOKIE['cart'], true);
        if (isset($cart[$product_id])) {
            if ($cart[$product_id]['quantity'] > 1) {
                $cart[$product_id]['quantity']--;
            } else {
                unset($cart[$product_id]);
            }
            setcookie('cart', json_encode($cart), time() + 3600, '/');
        }
    }
    header('Location: cart.php');
    exit();
}

// Vymazať celý košík
function remove_cart(){
    setcookie('cart', '', time() - 3600, '/');
    header('Location: cart.php');
    exit();
}
?>

