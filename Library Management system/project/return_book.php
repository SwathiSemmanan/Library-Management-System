<?php
session_start();
if ($_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}

include 'db.php';
$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = $_POST['book_id'];

    $stmt = $conn->prepare("DELETE FROM borrowed_books WHERE user_id = ? AND book_id = ?");
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE books SET count = count + 1 WHERE id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();

    $stmt->close();
    header("Location: user_panel.php");
}

$borrowed_books_result = $conn->query("SELECT books.id, books.title, books.author FROM borrowed_books JOIN books ON borrowed_books.book_id = books.id WHERE borrowed_books.user_id = $user_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Book</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Return a Book</h2>
        <form action="return_book.php" method="post">
            <label for="book_id">Select Book:</label>
            <select id="book_id" name="book_id" required>
                <?php while($row = $borrowed_books_result->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['title']; ?> by <?php echo $row['author']; ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit">Return</button>
        </form>
        <a href="user_panel.php">Back to User Panel</a>
    </div>
</body>
</html>
