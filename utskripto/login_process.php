<?php
include 'config.php';

function decryptPassword($encryptedPassword, $key) {
    $len = strlen($encryptedPassword);
    $decrypted = str_repeat(' ', $len);  // Inisialisasi dengan spasi

    // Dekripsi menggunakan sandi transposisi
    $index = 0;
    for ($i = 0; $i < count($key); $i++) {
        for ($j = $i; $j < $len; $j += count($key)) {
            $decrypted[$j] = $encryptedPassword[$index++];
        }
    }

    return trim($decrypted);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Tetapkan kunci sandi transposisi Anda
    $cipherKey = array(3, 1, 2);

    // Ambil kata sandi terenkripsi dari database berdasarkan nama pengguna
    $sql = "SELECT encrypt_password FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $encryptedPasswordFromDB = $row["encrypt_password"];

        // Dekripsi kata sandi
        $decryptedPassword = decryptPassword($encryptedPasswordFromDB, $cipherKey);

        // Debugging output
        echo "Encrypted Password from DB: $encryptedPasswordFromDB <br>";
        echo "Decrypted Password: $decryptedPassword <br>";

        // Periksa apakah kata sandi yang didekripsi cocok dengan kata sandi yang dimasukkan
        if ($decryptedPassword === $password) {
            echo "Login successful!";
        } else {
            echo "Login failed. Invalid username or password.";
        }
    } else {
        echo "Login failed. Invalid username or password.";
    }
}

$conn->close();
?>
