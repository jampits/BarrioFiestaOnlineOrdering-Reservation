

<?php
include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:home.php');
};

// Fetch user profile and reservation details
$select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
$select_profile->execute([$user_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

// Ensure that reservation_details is set and not null
$reservation_details = !empty($fetch_profile['reservation_details']) ? json_decode($fetch_profile['reservation_details'], true) : null;

if(isset($_POST['submit'])){
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $method = $_POST['method'];
   $method = filter_var($method, FILTER_SANITIZE_STRING);
   $address = $_POST['address'];
   $address = filter_var($address, FILTER_SANITIZE_STRING);
   // $num_people = $_POST['num_people'];
   // echo "Number of People: $num_people<br>";
   // $num_people = filter_var($num_people, FILTER_SANITIZE_NUMBER_INT);
   // echo "Sanitized Number of People: $num_people<br>";
   // $reservation_time = $_POST['reservation_time'];
   // $reservation_time = filter_var($reservation_time, FILTER_SANITIZE_STRING);

   $total_products = $_POST['total_products'];
   $total_price = $_POST['total_price'];

   $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $check_cart->execute([$user_id]);

   if($check_cart->rowCount() > 0){
         
      // Prepare the SQL statement for insertion
      $insert_order = $conn->prepare("INSERT INTO `orders` (user_id, name, number, email, method, address, total_products, total_price, num_people, reservation_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
      
      // Ensure that $reservation_details is not null before accessing its elements
      $num_people = !empty($reservation_details['num_of_people']) ? $reservation_details['num_of_people'] : null;
      $reservation_time = !empty($reservation_details['reservation_time']) ? $reservation_details['reservation_time'] : null;

      // Execute the prepared statement with the provided values
      $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $total_price, $num_people, $reservation_time]);

      $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
      $delete_cart->execute([$user_id]);

      $message[] = 'Order placed successfully!';
   } else {
      $message[] = 'Your cart is empty';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>checkout</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<div class="heading">
   <h3>checkout</h3>
   <p><a href="home.php">home</a> <span> / checkout</span></p>
</div>

<section class="checkout">

   <h1 class="title">order summary</h1>

<form action="" method="post">

   <div class="cart-items">
      <h3>cart items</h3>
      <?php
         $grand_total = 0;
         $cart_items[] = '';
         $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
         $select_cart->execute([$user_id]);
         if($select_cart->rowCount() > 0){
            while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
               $cart_items[] = $fetch_cart['name'].' ('.$fetch_cart['price'].' x '. $fetch_cart['quantity'].') - ';
               $total_products = implode($cart_items);
               $grand_total += ($fetch_cart['price'] * $fetch_cart['quantity']);
      ?>
      <p><span class="name"><?= $fetch_cart['name']; ?></span><span class="price">₱<?= $fetch_cart['price']; ?> x <?= $fetch_cart['quantity']; ?></span></p>
      <?php
            }
         }else{
            echo '<p class="empty">your cart is empty!</p>';
         }
      ?>
      <p class="grand-total"><span class="name">grand total :</span><span class="price">₱<?= $grand_total; ?></span></p>
      <a href="cart.php" class="btn">view cart</a>
   </div>

   <input type="hidden" name="total_products" value="<?= $total_products; ?>">
   <input type="hidden" name="total_price" value="<?= $grand_total; ?>" value="">
   <input type="hidden" name="name" value="<?= $fetch_profile['name'] ?>">
   <input type="hidden" name="number" value="<?= $fetch_profile['number'] ?>">
   <input type="hidden" name="email" value="<?= $fetch_profile['email'] ?>">
   <input type="hidden" name="address" value="<?= $fetch_profile['address'] ?>">

   <div class="user-info">
      <h3>your info</h3>
      <p><i class="fas fa-user"></i><span><?= $fetch_profile['name'] ?></span></p>
      <p><i class="fas fa-phone"></i><span><?= $fetch_profile['number'] ?></span></p>
      <p><i class="fas fa-envelope"></i><span><?= $fetch_profile['email'] ?></span></p>
      <a href="update_profile.php" class="btn">update info</a>

      <h3>Reservation Details</h3>
      <?php
      if (!empty($fetch_profile['reservation_details'])) {
         $reservation_details = json_decode($fetch_profile['reservation_details'], true);
         ?>
         <p><strong>Number of People:</strong> <?php echo $reservation_details['num_of_people']; ?></p>
         <p><strong>Reservation Time:</strong> <?php echo $reservation_details['reservation_time']; ?></p>
         <!-- Add more fields as needed -->
      <?php } else { ?>
         <p>No reservation details available. Please make a reservation.</p>
      <?php } ?>

      <a href="update_reservation.php" class="btn">Update Reservation Details</a>

      <select name="method" class="box" required>
         <option value="" disabled selected>select payment method --</option>
         <option value="cash">cash</option>
      </select>
      <input type="submit" value="place order" class="btn" style="width:100%; background:var(--red); color:var(--white);" name="submit">
   </div>

</form>
   
</section>

<!-- footer section starts  -->
<?php include 'components/footer.php'; ?>
<!-- footer section ends -->

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>
