<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "barcode1_db";

// Product class to handle product-related operations
class Product {
    private $conn;
    
    // Constructor for setting up the database connection
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Fetch all products from the database
    public function getProducts() {
        $sql = "SELECT product_name, product_id, price FROM products";
        $result = $this->conn->query($sql);
        $products = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }

        return $products;
    }

    // Delete product by ID
    public function deleteProduct($productId) {
        $delete_query = $this->conn->prepare("DELETE FROM products WHERE product_id = ?");
        $delete_query->bind_param("s", $productId);

        if ($delete_query->execute()) {
            return ['message' => "Product with ID '$productId' deleted successfully.", 'message_type' => "success"];
        } else {
            return ['message' => "Error: " . $delete_query->error, 'message_type' => "danger"];
        }

        $delete_query->close();
    }
}
// Database connection class
class Database {
    private $servername;
    private $username;
    private $password;
    private $dbname;
    private $conn;

    // Constructor to initialize connection parameters
    public function __construct($servername, $username, $password, $dbname) {
        $this->servername = $servername;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;
    }
    // Create and return the database connection
    public function connect() {
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        return $this->conn;
    }
    // Close the database connection
    public function close() {
        $this->conn->close();
    }
}

// Start session for message display
session_start();

// Create database connection
$db = new Database($servername, $username, $password, $dbname);
$conn = $db->connect();

// Initialize Product object
$productObj = new Product($conn);

// Handle product deletion if POST request is received
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product_id'])) {
    $delete_product_id = trim($_POST['delete_product_id']);
    $response = $productObj->deleteProduct($delete_product_id);

    // Set session message
    $_SESSION['message'] = $response['message'];
    $_SESSION['message_type'] = $response['message_type'];

    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch products to display
$products = $productObj->getProducts();

// Close the database connection
$db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Barcode Printing</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <style>
        body {
            background-image: url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR6tr1JZxrY4T5oYSExzxASf7zsp9eMjMtCxA&s');
            color: blue;
            font-family: 'Helvetica', sans-serif;
        }

        .container {
            background-color: rgba(236, 240, 241, 0.9);
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
            margin-top: 5%;
        }

        .form-horizontal .form-group label {
            font-weight: bold;
        }

        .form-control {
            height: 50px;
            font-size: 18px;
            border-radius: 5px;
        }

        .btn-custom {
            background-color: #3498db;
            color: white;
            padding: 5px 10px;
            font-size: 18px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            border: none;
        }
    </style>
</head>
<body>

<div class="container">
    <div style="margin: 10%;">

        <h2 class="text-center">Barcode Printing</h2>

        <!-- Display success or error message -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                <?php
                echo $_SESSION['message'];
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
                ?>
            </div>
        <?php endif; ?>

        <!-- Product Selection Form -->
        <form class="form-horizontal" method="post" action="barcode.php" target="_blank">
    <div class="form-group">
        <label class="control-label col-sm-2 label-product" for="product">Product:</label>
        <div class="col-sm-10">
            <select class="form-control" id="product" name="product" onchange="updateProductDetails(this.value)" required>
                <option value="">Select a product</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?php echo htmlspecialchars($product['product_name']); ?>" data-id="<?php echo htmlspecialchars($product['product_id']); ?>">
                        <?php echo htmlspecialchars(ucfirst($product['product_name'])); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2 label-product-id" for="product_id">Product ID:</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="product_id" name="product_id" readonly required>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2 label-price" for="price">Price:</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" id="rate" name="price" readonly required>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2 label-print-qty" for="print_qty">Barcode Quantity:</label>
        <div class="col-sm-10">
            <input autocomplete="OFF" type="text" class="form-control" id="print_qty" name="print_qty" required>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" class="btn btn-custom">Submit</button>
            <button type="button" class="btn btn-danger" onclick="window.location.href='index.php'">Cancel</button>
        </div>
    </div>
</form>

        <!-- Button to open the modal for adding products -->
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addProductModal">Add New Product</button>

        <!-- Modal for adding new products -->
        <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST" action="">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="addProductModalLabel">Add New Product</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="new_product">Product Name:</label>
                                <input type="text" class="form-control" id="new_product" name="new_product" required>
                            </div>
                            <div class="form-group">
                                <label for="new_product_id">Product ID:</label>
                                <input type="text" class="form-control" id="new_product_id" name="new_product_id" required>
                            </div>
                            <div class="form-group">
                                <label for="new_product_rate">Price:</label>
                                <input type="text" class="form-control" id="new_product_rate" name="new_product_price" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Display Product List after submission -->
        <h3 class="text-center">Product List</h3>
        <table class="table table-bordered">
    <thead>
        <tr>
            <th>Product Name</th>
            <th>Product ID</th>
            <th>Price</th>
            <th>Actions</th> <!-- Actions Column -->
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars(ucfirst($product['product_name'])); ?></td>
                    <td><?php echo htmlspecialchars($product['product_id']); ?></td>
                    <td><?php echo htmlspecialchars($product['price']); ?></td>
                    <td>
                        <!-- Edit button -->
                        <button type="button" class="btn btn-warning btn-sm" onclick="openEditModal('<?php echo htmlspecialchars($product['product_id']); ?>', '<?php echo htmlspecialchars(ucfirst($product['product_name'])); ?>', '<?php echo htmlspecialchars($product['price']); ?>')">Edit</button>

                        <!-- Delete button -->
                        <form method="POST" action="" style="display:inline;">
                            <input type="hidden" name="delete_product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="text-center">No products found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>


    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-labelledby="editProductModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="editProductModalLabel">Edit Product</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_product_name">Product Name:</label>
                        <input type="text" class="form-control" id="edit_product_name" name="edit_product_name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_product_id">Product ID:</label>
                        <input type="text" class="form-control" id="edit_product_id" name="edit_product_id" readonly required>
                    </div>
                    <div class="form-group">
                        <label for="edit_product_price">Price:</label>
                        <input type="text" class="form-control" id="edit_product_price" name="edit_product_price" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="edit_product">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    // Function to update the Product ID and Rate based on the selected product
function updateProductDetails(productName) {
    const products = <?php echo json_encode($products); ?>;
    
    // Find the product in the list and update the fields
    const selectedProduct = products.find(p => p.product_name === productName);

    if (selectedProduct) {
        document.getElementById('product_id').value = selectedProduct.product_id;
        document.getElementById('rate').value = selectedProduct.price;
    }
}

    // Function to open the Edit Product modal
    function openEditModal(productId, productName, productPrice) {
        document.getElementById('edit_product_id').value = productId;
        document.getElementById('edit_product_name').value = productName;
        document.getElementById('edit_product_price').value = productPrice;
        $('#editProductModal').modal('show'); // Show the modal
    }
</script>

</body>
</html>
