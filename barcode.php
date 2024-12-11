<?php
include 'barcode128.php'; // Include the barcode generation script

class BarcodePrinter
{
    private $product_id;
    private $product_name;
    private $price;
    private $print_qty;

    // Constructor to initialize the product details
    public function __construct($post_data)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Initialize product details from the form data
            $this->product_id = $post_data['product_id'];
            $this->product_name = $post_data['product'];
            $this->price = $post_data['price'];
            $this->print_qty = (int)$post_data['print_qty']; // Cast to integer

            // Validate the print quantity
            if ($this->print_qty <= 0) {
                die("Print quantity must be greater than zero.");
            }
        }
    }

    // Method to generate the HTML for barcode printing
    public function generateBarcodeHtml()
    {
        $html = "<div style='margin-left: 5%;'>";
        // Loop to print the barcodes
        for ($i = 1; $i <= $this->print_qty; $i++) {
            // Output the product name and barcode for the product ID
            $html .= "<p><span><b>Item: " . htmlspecialchars(ucfirst($this->product_name)) . "</b></span> " 
                     . $this->generateBarcode($this->product_id) . "</p>";
        }
        $html .= "</div>";
        return $html;
    }

    // Method to generate barcode using the Code128Barcode class
    private function generateBarcode($product_id)
    {
        // Create an instance of Code128Barcode and generate the barcode for product_id
        $barcode = new Code128Barcode();
        return $barcode->generate(stripslashes($product_id));
    }
}

// Instantiate the BarcodePrinter class with the POST data
$barcodePrinter = new BarcodePrinter($_POST);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Print Barcode</title>
</head>
<body onload="window.print();">
    <?php
    // Output the barcode HTML
    echo $barcodePrinter->generateBarcodeHtml();
    ?>
</body>
</html>
