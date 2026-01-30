<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_book'])) {
        $book_title = $_POST['book_title'];
        $book_author = $_POST['book_author'];
        $book_count = $_POST['book_count'];

        $stmt = $conn->prepare("INSERT INTO books (title, author, count) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $book_title, $book_author, $book_count);
        $stmt->execute();
        $stmt->close();
    }

    if (isset($_POST['delete_book'])) {
        $book_id = $_POST['book_id'];

        $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $stmt->close();
    }
}

$books_result = $conn->query("SELECT * FROM books");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Manage Books</h2>
        <form action="manage_books.php" method="post">
            <label for="book_title">Book Title:</label>
            <input type="text" id="book_title" name="book_title" required>
            <label for="book_author">Book Author:</label>
            <input type="text" id="book_author" name="book_author" required>
            <label for="book_count">Book Count:</label>
            <input type="number" id="book_count" name="book_count" required>
            <button type="submit" name="add_book">Add Book</button>
        </form>
        <h3>Existing Books</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Count</th>
                <th>Action</th>
            </tr>
            <?php while($row = $books_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['title']; ?></td>
                    <td><?php echo $row['author']; ?></td>
                    <td><?php echo $row['count']; ?></td>
                    <td>
                        <form action="manage_books.php" method="post">
                            <input type="hidden" name="book_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete_book">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        <a href="admin_panel.php">Back to Admin Panel</a>
    </div>
</body>
</html>
