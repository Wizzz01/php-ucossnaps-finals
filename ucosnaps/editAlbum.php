<?php 
require_once 'core/dbConfig.php'; 
require_once 'core/models.php'; 

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
}

$album_id = $_GET['album_id'];
$album = getAlbumById($pdo, $album_id); // Fetch album info by album_id
$photos = getPhotosByAlbum($pdo, $album_id); 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Album Photos</title>
    <link rel="stylesheet" href="styles/styles.css">
    <style>
        .album-container {
            margin-bottom: 20px;
            border: 1px solid gray;
            padding: 10px;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .album-container h3 {
            margin-bottom: 10px;
        }

        .album-photos {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .photo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 150px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            background-color: #fff;
        }

        .photo-container img {
            width: 100%; 
            height: 100px; 
            object-fit: cover; 
            border-radius: 4px; 
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <h2>Manage Photos in Album</h2>

    <!-- Edit Album Title Form -->
    <div class="album-container">
        <h3>Edit Album Title</h3>
        <form action="core/handleForms.php" method="POST">
            <input type="hidden" name="album_id" value="<?php echo $album_id; ?>">
            <input type="text" name="new_album_name" value="<?php echo htmlspecialchars($album['album_name']); ?>" required>
            <button type="submit" name="updateAlbumNameBtn">Update Album Title</button>
        </form>
    </div>

    <!-- Add Photo Form -->
    <form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="album_id" value="<?php echo $album_id; ?>">
        <label for="photoDescription">Photo Description:</label>
        <input type="text" name="photoDescription" placeholder="Description">
        <label for="photoUpload">Upload Photo:</label>
        <input type="file" name="image" required>
        <button type="submit" name="insertPhotoBtn">Add Photo</button>
    </form>

    <!-- Existing Photos in Album -->
    <div class="album-photos">
        <?php foreach ($photos as $photo): ?>
            <div class="photo-container">
                <img src="images/<?php echo $photo['photo_name']; ?>" alt="<?php echo $photo['description']; ?>" style="width: 150px; height: 150px; object-fit: cover;">
                <p><?php echo $photo['description']; ?></p>
                <form action="core/handleForms.php" method="POST">
                    <input type="hidden" name="photo_id" value="<?php echo $photo['photo_id']; ?>">
                    <input type="hidden" name="photo_name" value="<?php echo $photo['photo_name']; ?>">
                    <button type="submit" name="deletePhotoBtn" style="color: red;">Delete Photo</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
