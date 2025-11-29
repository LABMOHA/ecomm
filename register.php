<?php

require_once("includes/config.php");

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-3xl font-bold text-center mb-6">Create Account</h2>


            <form method="POST" action="">
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Full Name</label>
                    <input type="text" name="full_name"
                        value=""
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Email</label>
                    <input type="email" name="email"
                        value=""
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Password</label>
                    <input type="password" name="password"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>


                <button type="submit"
                    class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 font-semibold">
                    Register
                </button>
            </form>


            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Already have an account?
                    <a href="login.php" class="text-blue-500 hover:underline font-semibold">Login</a>
                </p>
                <a href="index.php" class="text-gray-500 hover:underline mt-2 block">Back to Home</a>
            </div>
        </div>
    </div>
</body>

</html>
<?php


if ($_SERVER["REQUEST_METHOD"] == "POST") {


    $name = filter_input(INPUT_POST, "full_name", FILTER_SANITIZE_SPECIAL_CHARS);

    $email = filter_input(INPUT_POST, "email");
    $password = filter_input(INPUT_POST, "password");

    $hash = password_hash($password, PASSWORD_DEFAULT);

    // $sql = "INSERT INTO users (full_name, email, password) VALUES ('$name', '$email', '$hash')";
    // $pdo->exec($sql);
    $insert = $pdo->prepare("INSERT INTO users (full_name, email, password_) VALUES (?, ?, ?)");
    $insert->execute([$name, $email, $hash]);

    header(("Location: login.php"));
}






?>