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
	<title>Document</title>
	<link rel="stylesheet" href="styles/styles.css">
	<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
</head>
<body>
	<?php include 'navbar.php'; ?>
	<div class="insertPhotoForm" style="display: flex; justify-content: center;">
    <form action="core/handleForms.php" method="POST" enctype="multipart/form-data" style="background-color: ghostwhite; border-style: solid; border-color: gray; width: 50%; padding: 25px;">
        <p>
            <label for="#" style="font-weight: bold;">Album Name</label>
            <input type="text" name="album_name" placeholder="Album Name" style="width: 100%; padding: 8px; margin-top: 10px;">
        </p>
        <p>
            <button type="submit" name="createAlbumBtn" style="margin-top: 10px; padding: 10px; background-color: #007BFF; color: white; border: none; cursor: pointer; width: 100%;">Create Album</button>
        </p>
    </form>
</div>

<div class="insertPhotoForm" style="display: flex; justify-content: center; margin-top: 25px;">
    <form action="core/handleForms.php" method="POST" enctype="multipart/form-data" style="background-color: ghostwhite; border-style: solid; border-color: gray; width: 50%; padding: 25px;">
        <p>
            <label for="photoDescription" style="font-weight: bold;">Photo Description</label>
            <input type="text" name="photoDescription" placeholder="Photo Description" style="width: 100%; padding: 8px; margin-top: 10px;">
        </p>
        <p>
            <label for="album" style="font-weight: bold;">Album</label>
            <select name="album_id" style="width: 100%; padding: 8px; margin-top: 10px;">
                <?php foreach ($albums as $album) { ?>
                    <option value="<?php echo $album['album_id']; ?>"><?php echo $album['album_name']; ?></option>
                <?php } ?>
            </select>
        </p>
        <p>
            <label for="photo" style="font-weight: bold;">Photo Upload</label>
            <input type="file" name="image" style="width: 100%; padding: 8px; margin-top: 10px;">
        </p>
        <p>
            <button type="submit" name="insertPhotoBtn" style="margin-top: 10px; padding: 10px; background-color: #007BFF; color: white; border: none; cursor: pointer; width: 100%;">Upload Photo</button>
        </p>
    </form>
</div>
	<?php $getAllPhotos = getAllPhotos($pdo); ?>
	<?php foreach ($getAllPhotos as $row) { ?>

	<div class="images" style="display: flex; justify-content: center; margin-top: 25px;">
		<div class="photoContainer" style="background-color: ghostwhite; border-style: solid; border-color: gray;width: 50%;">

			<img src="images/<?php echo $row['photo_name']; ?>" alt="" style="width: 100%;">

			<div class="photoDescription" style="padding:25px;">
				<a href="profile.php?username=<?php echo $row['username']; ?>"><h2><?php echo $row['username']; ?></h2></a>
				<p><i><?php echo $row['date_added']; ?></i></p>
				<h4><?php echo $row['description']; ?></h4>

				<?php if ($_SESSION['username'] == $row['username']) { ?>
					<a href="editphoto.php?photo_id=<?php echo $row['photo_id']; ?>" style="float: right;"> Edit </a>
					<br>
					<br>
					<a href="deletephoto.php?photo_id=<?php echo $row['photo_id']; ?>" style="float: right;"> Delete</a>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php } ?>
</body>
</html>
