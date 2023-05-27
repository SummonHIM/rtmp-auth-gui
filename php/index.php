<?php
include "config.php";
?>

<!doctype html>
<html>

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <title>RTMP Stream System</title>
</head>

<body>
    <div class="container" style="margin-top: 5%;">
        <?php
        try {
            $dbh = new PDO('mysql:host=' . $dbhost . ';dbname=' . $dbname . ';charset=utf8', $dbusername, $dbpassword);
            $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            echo '<div class="alert alert-danger" role="alert"><b>Database Error!</b>';
            echo 'Message: ' . $e->getMessage();
            echo "</div>";
            die();
        }

        echo '
        <div class="row" style="margin-bottom: 5%;">
            <div class="col-md-9"><h1 style="margin-bottom: 5%;">RTMP Stream System</h1></div>
            <div class="col-md-3"><a type="button" class="btn btn-primary btn-lg" href="profile">Edit User</a></div>
        </div>
        ';

        $qGetUsersInfo = "SELECT * FROM users";
        $qGetUsersResult = $dbh->query('select id_user,username,stream_key,private,private_key,live_status from users');

        echo '<div class="row row-cols-1 row-cols-md-2 g-4">';
        while ($loopUsersResult = $qGetUsersResult->fetch()) {
            if ($loopUsersResult['username'] != null && $loopUsersResult['stream_key'] != null) {
                echo '<div class="col">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">' . $loopUsersResult['username'] . '</h5>
                                <p class="card-text">
                                    Stream status: ';
                if ($loopUsersResult['live_status'] == 1) {
                    echo '<span class="badge bg-success">On live</span>';
                } else {
                    echo '<span class="badge bg-warning">Not on live</span>';
                }
                echo '<br>Private stream: ';
                if ($loopUsersResult['private'] == 1) {
                    echo '<span class="badge bg-warning">Yes</span>';
                }
                if ($loopUsersResult['private'] == 0) {
                    echo '<span class="badge bg-success">No</span>';
                }
                echo '<br>Stream URL: ';
                if ($loopUsersResult['private'] == 1 && $loopUsersResult['private_key'] != null) {
                    echo $rtmpurl . $loopUsersResult['username'] . '?key=&lt;password&gt;';
                }
                if ($loopUsersResult['private'] == 1 && $loopUsersResult['private_key'] == null) {
                    echo 'Temporarily unavailable';
                }
                if ($loopUsersResult['private'] == 0) {
                    echo $rtmpurl . $loopUsersResult['username'];
                }
                echo '</p>
                            </div>';
                if ($loopUsersResult['live_status'] == 1) {
                    if ($loopUsersResult['private'] == 1 && $loopUsersResult['private_key'] != null) {
                        echo '<div class="card-footer">
                                <div class="alert alert-warning" role="alert">
                                    You need a password to watch this stream! Example<span class="badge bg-secondary">' . $rtmpurl . $loopUsersResult['username'] . '?key=&lt;password&gt;</span>.
                                </div>
                              </div>';
                    }
                    if ($loopUsersResult['private'] == 0) {
                        echo '<div class="card-footer">
                                <a class="btn btn-primary" href="' . $rtmpurl . $loopUsersResult['username'] . '" >Watch</a>
                              </div>';
                    }
                }
                echo '</div>
                    </div>';
            }
        }

        echo '</div>';
        ?>
        <hr style="margin-top: 5%;">
        <p class="text-center">
            Powered by <a style="text-decoration: none;" href="https://github.com/Sora012" target="_blank">Sora012</a> and <a style="text-decoration: none;" href="https://github.com/SummonHIM" target="_blank">SummonHIM</a>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>