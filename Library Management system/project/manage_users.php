<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];

        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    }

    if (isset($_POST['update_user'])) {
        $user_id = $_POST['user_id'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $dob = $_POST['dob'];
        $role = $_POST['role'];

        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, dob = ?, role = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $username, $email, $dob, $role, $user_id);
        $stmt->execute();
        $stmt->close();
    }
}

$users_result = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Manage Users</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>DOB</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
            <?php while($row = $users_result->fetch_assoc()): ?>
                <tr>
                    <form action="manage_users.php" method="post">
                        <td><?php echo $row['id']; ?></td>
                        <td><input type="text" name="username" value="<?php echo $row['username']; ?>"></td>
                        <td><input type="email" name="email" value="<?php echo $row['email']; ?>"></td>
                        <td><input type="date" name="dob" value="<?php echo $row['dob']; ?>"></td>
                        <td>
                            <select name="role">
                                <option value="user" <?php if ($row['role'] == 'user') echo 'selected'; ?>>User</option>
                                <option value="admin" <?php if ($row['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                            </select>
                        </td>
                        <td>
                            <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="update_user">Update</button>
                            <button type="submit" name="delete_user">Delete</button>
                        </td>
                    </form>
                </tr>
            <?php endwhile; ?>
        </table>
        <a href="admin_panel.php">Back to Admin Panel</a>
    </div>
</body>
</html>
