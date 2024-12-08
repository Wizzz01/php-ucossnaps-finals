<?php  
require_once 'dbConfig.php';
require_once 'models.php';

if (isset($_POST['insertNewUserBtn'])) {
	$username = trim($_POST['username']);
	$first_name = trim($_POST['first_name']);
	$last_name = trim($_POST['last_name']);
	$password = trim($_POST['password']);
	$confirm_password = trim($_POST['confirm_password']);

	if (!empty($username) && !empty($first_name) && !empty($last_name) && !empty($password) && !empty($confirm_password)) {

		if ($password == $confirm_password) {

			$insertQuery = insertNewUser($pdo, $username, $first_name, $last_name, password_hash($password, PASSWORD_DEFAULT));
			$_SESSION['message'] = $insertQuery['message'];

			if ($insertQuery['status'] == '200') {
				$_SESSION['message'] = $insertQuery['message'];
				$_SESSION['status'] = $insertQuery['status'];
				header("Location: ../login.php");
			}

			else {
				$_SESSION['message'] = $insertQuery['message'];
				$_SESSION['status'] = $insertQuery['status'];
				header("Location: ../register.php");
			}

		}
		else {
			$_SESSION['message'] = "Please make sure both passwords are equal";
			$_SESSION['status'] = '400';
			header("Location: ../register.php");
		}

	}

	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../register.php");
	}
}

if (isset($_POST['loginUserBtn'])) {
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);

	if (!empty($username) && !empty($password)) {

		$loginQuery = checkIfUserExists($pdo, $username);
		$userIDFromDB = $loginQuery['userInfoArray']['user_id'];
		$usernameFromDB = $loginQuery['userInfoArray']['username'];
		$passwordFromDB = $loginQuery['userInfoArray']['password'];

		if (password_verify($password, $passwordFromDB)) {
			$_SESSION['user_id'] = $userIDFromDB;
			$_SESSION['username'] = $usernameFromDB;
			header("Location: ../index.php");
		}

		else {
			$_SESSION['message'] = "Username/password invalid";
			$_SESSION['status'] = "400";
			header("Location: ../login.php");
		}
	}

	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../register.php");
	}

}

if (isset($_GET['logoutUserBtn'])) {
	unset($_SESSION['user_id']);
	unset($_SESSION['username']);
	header("Location: ../login.php");
}


if (isset($_POST['insertPhotoBtn'])) {

	// Get Description
	$description = $_POST['photoDescription'];

	// Get file name
	$fileName = $_FILES['image']['name'];

	// Get temporary file name
	$tempFileName = $_FILES['image']['tmp_name'];

	// Get file extension
	$fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

	// Generate random characters for image name
	$uniqueID = sha1(md5(rand(1,9999999)));

	// Combine image name and file extension
	$imageName = $uniqueID.".".$fileExtension;

	// If we want edit a photo
	if (isset($_POST['photo_id'])) {
		$photo_id = $_POST['photo_id'];
	}
	else {
		$photo_id = "";
	}
	// Get album ID
	$album_id = $_POST['album_id'];

	// Save image 'record' to database
	$saveImgToDb = insertPhoto($pdo, $imageName, $_SESSION['username'], $description, $photo_id, $album_id);

	// Store actual 'image file' to images folder
	if ($saveImgToDb) {

		// Specify path
		$folder = "../images/".$imageName;

		// Move file to the specified path 
		if (move_uploaded_file($tempFileName, $folder)) {
			header("Location: ../index.php");
		}
	}

}

if (isset($_POST['createAlbumBtn'])) {
    $album_name = trim($_POST['album_name']);
    $user_id = $_SESSION['user_id'];

    if (!empty($album_name)) {
        $createAlbum = createAlbum($pdo, $album_name, $user_id);
        if ($createAlbum) {
            $_SESSION['message'] = "Album created successfully!";
        } else {
            $_SESSION['message'] = "Failed to create album.";
        }
    } else {
        $_SESSION['message'] = "Album name cannot be empty.";
    }
    header("Location: ../index.php");
}

// Handle album name update
if (isset($_POST['updateAlbumNameBtn'])) {
    $album_id = $_POST['album_id'];
    $new_name = trim($_POST['new_album_name']);

    if (!empty($new_name)) {
        $updated = updateAlbumName($pdo, $album_id, $new_name);
        $_SESSION['message'] = $updated ? "Album name updated successfully!" : "Failed to update album name.";
    } else {
        $_SESSION['message'] = "Album name cannot be empty.";
    }
    header("Location: ../albums.php");
}

// Handle album deletion
if (isset($_POST['deleteAlbumBtn'])) {
    $album_id = $_POST['album_id'];

    $deleted = deleteAlbumAndPhotos($pdo, $album_id);
    $_SESSION['message'] = $deleted ? "Album deleted successfully!" : "Failed to delete album.";
    header("Location: ../albums.php");
}

if (isset($_POST['updateAlbumNameBtn'])) {
    $album_id = $_POST['album_id'];
    $new_album_name = $_POST['new_album_name'];

    // Update album name in the database
    $stmt = $pdo->prepare("UPDATE albums SET album_name = :album_name WHERE album_id = :album_id");
    $stmt->bindParam(':album_name', $new_album_name);
    $stmt->bindParam(':album_id', $album_id);
    $stmt->execute();

    // Redirect back to the edit album page after updating
    header("Location: ../editAlbum.php?album_id=$album_id");
    exit();
}

if (isset($_POST['deletePhotoBtn'])) {
	$photo_name = $_POST['photo_name'];
	$photo_id = $_POST['photo_id'];
	$deletePhoto = deletePhoto($pdo, $photo_id);

	if ($deletePhoto) {
		unlink("../images/".$photo_name);
		header("Location: ../index.php");
	}

}