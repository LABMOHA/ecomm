<?php
session_start();
require_once('includes/config.php');




if (isset($_POST["login"])) {

    echo "hallo";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];
        $pass = $_POST['password'];


        $stmt = $pdo->prepare("SELECT id, email, password_, full_name FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);





        if ($user && password_verify($pass, $user['password_'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['full_name'];
            redirect('clients/index.php');
        } else {
            echo "Invalid email or password.";
        }
    }
}







?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-3xl font-bold text-center mb-6">Login</h2>




            <form method="POST" action="">
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Email</label>
                    <input type="email" name="email"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 mb-2">Password</label>
                    <input type="password" name="password"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>


                <input type="submit" value="login" name="login"
                    class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 font-semibold">
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Don't have an account?
                    <a href="register.php" class="text-blue-500 hover:underline font-semibold">Register</a>
                </p>

            </div>
        </div>
    </div>
</body>

</html>