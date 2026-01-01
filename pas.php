<?php
$hash = '$2y$10$maKqChUQWaQRcnmVLO1YbOHmr59nrBHbK/uJ4X48sVgeIgLwDKMP6';

// Replace this with the password you want to test
$password = 'Sa@2024@Sa';

if (password_verify($password, $hash)) {
    echo "Password is correct!";
} else {
    echo "Password is incorrect!";
}
?>
