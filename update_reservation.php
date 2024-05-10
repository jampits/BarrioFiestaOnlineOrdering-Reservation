

<?php
include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
   header('location:home.php');
};

if(isset($_POST['submit'])){
   $num_of_people = $_POST['num_of_people'];
   $reservation_time = $_POST['reservation_time'];
   // You may want to add more validation and sanitization for these inputs.

   $reservation_details = [
      'num_of_people' => $num_of_people,
      'reservation_time' => $reservation_time,
   ];

   // Save reservation details to the database or perform other actions as needed.
   // Example:
   $update_reservation = $conn->prepare("UPDATE `users` SET reservation_details = ? WHERE id = ?");
   $update_reservation->execute([json_encode($reservation_details), $user_id]);
   

   $message[] = 'Reservation saved!';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>update address</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php' ?>

<section class="form-container">
      <form action="" method="post">
         <h3>Make Reservation</h3>
         <input type="number" class="box" placeholder="Number of People" required min="1" max="999" name="num_of_people">

         <input type="datetime-local" class="box" placeholder="Reservation Time" required name="reservation_time">
         <!-- You may want to add more input fields for additional reservation details. -->
         <input type="submit" value="Make Reservation" name="submit" class="btn">
      </form>
   </section>









<?php include 'components/footer.php' ?>







<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>