<?php require_once 'core/dbConfig.php'; ?>
<?php require_once 'core/models.php'; ?>
<?php  
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
}

$albums = getAlbumsByUser($pdo, $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Albums</title>
    <link rel="stylesheet" href="styles/styles.css">
    <!-- Slick Carousel CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <style>
        .album {
            margin: 20px auto;
            padding: 20px;
            background-color: ghostwhite;
            border: 1px solid gray;
            width: 80%;
        }

        .carousel-container {
            max-width: 100%;
            margin: 0 auto;
        }

        .carousel-container img {
            width: 100%;
            max-height: 300px;
            object-fit: contain;
            border-radius: 8px;
        }

        .slick-prev, .slick-next {
            color: #007BFF !important; /* Customize navigation arrow color */
        }

        .album-actions {
            margin-top: 10px;
        }

        .album-actions a, .album-actions button {
            margin-right: 10px;
            text-decoration: none;
            color: #007BFF;
        }

        .album-actions button {
            background: none;
            border: none;
            color: red;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="albums-container">
        <?php foreach ($albums as $album) { ?>
            <div class="album">
                <h2><?php echo htmlspecialchars($album['album_name']); ?></h2>

                <div class="carousel-container">
                    <?php 
                    $photos = getPhotosByAlbum($pdo, $album['album_id']);
                    if (!empty($photos)) {
                        foreach ($photos as $photo) { ?>
                            <div>
                                <img src="images/<?php echo htmlspecialchars($photo['photo_name']); ?>" 
                                     alt="Photo" title="<?php echo htmlspecialchars($photo['description']); ?>">
                            </div>
                        <?php }
                    } else { ?>
                        <p>No photos in this album yet.</p>
                    <?php } ?>
                    
                </div>

                <!-- Edit Album and Manage Photos Links -->
                <?php if ($_SESSION['user_id'] == $album['user_id']) { ?>
                    <div class="album-actions">
                        <a href="editAlbum.php?album_id=<?php echo $album['album_id']; ?>">Edit Album</a>

                        <!-- Delete Album Form -->
                        <form action="core/handleForms.php" method="POST" style="display: inline;">
                            <input type="hidden" name="album_id" value="<?php echo $album['album_id']; ?>">
                            <button type="submit" name="deleteAlbumBtn">Delete Album</button>
                        </form>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>

    <script>
        $(document).ready(function() {
            $('.carousel-container').slick({
                dots: true,            // Show navigation dots
                infinite: true,        // Infinite looping
                speed: 500,            // Transition speed
                slidesToShow: 1,       // Show one slide at a time
                slidesToScroll: 1,     // Scroll one slide at a time
                autoplay: true,        // Auto-slide
                autoplaySpeed: 3000,   // Auto-slide speed in ms
                arrows: true           // Show next/prev arrows
            });
        });
    </script>
</body>
</html>
