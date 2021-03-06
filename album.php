<?php
    // check whether the user has logged in
    session_start();
    if (!isset($_SESSION['Username'])) {
	header("Location: logout.php");
    } else if (!isset($_GET['album']) || $_GET['album'] == '') {
	header("location: userInfo.php");
    }
    include('ini_db.php');
?>

<!doctype html>
<html lang="en">
<head>
    <title>SJBOX -- Search</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <link rel="stylesheet" href="./CSS/album.css">
</head>

<body>
<?php 
    include("./includes/navigation_bar.html");

    // get artist info
    $albumId = $_GET['album'];
    $album_info = $conn->prepare("SELECT DISTINCT AlbumName, AlbumReleaseDate, ArtistTitle, ArtistId
				  FROM Album NATURAL JOIN Track NATURAL JOIN Artist
				  WHERE AlbumId = ?");
    $album_info->bind_param('s', $albumId);
    $album_info->execute();
    $info_result = $album_info->get_result();
    echo "<div id=\"info\">";
    $row = $info_result->fetch_assoc();
    $tmp_release = $row['AlbumReleaseDate'];
    echo "<h1 style=\"font-weight: bold\">" .$row['AlbumName'] . "</h1>";
    echo "<p id=\"artistTitle\"><a href=\"artist.php?artist=" . $row['ArtistId'] . "\">" .$row['ArtistTitle'] . "</a></p>";
    while ($row = $info_result->fetch_assoc()) {  // might have multiple artists
	echo "<p id=\"artistTitle\"><a href=\"artist.php?artist=" . $row['ArtistId'] . "\">" .$row['ArtistTitle'] . "</a></p>";
    }
    echo "<p id=\"release\">Release date: " . $tmp_release . "</p>";
    echo "</div>";
    $album_info->close();

    // get album tracks
    $tracks= $conn->prepare("SELECT TrackId, TrackName
			     FROM Track NATURAL JOIN Album
			     WHERE AlbumId = ?");
    $tracks->bind_param('s', $albumId);
    $tracks->execute();
    $tracks_result = $tracks->get_result();
    echo "<div id=\"tracks\">";
    echo "Here are the tracks in this album.";
    echo "<table id=\"tracktable\">";
    $index = 1;
    while ($row = $tracks_result->fetch_assoc()) {
	echo "<tr>";
	echo "<td>" . $index . "</td>";
	echo "<td><a href=\"track.php?track=" . $row['TrackId'] . "\">" . $row['TrackName'] . "</a></td>";
	echo "</tr>";
	$index++;
    }
    echo "</table>";
    echo "</div>";
    $conn->close();

    include("./includes/footer.html");
?>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
</body>
</html>
