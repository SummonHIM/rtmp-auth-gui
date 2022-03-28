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

    <title>RTMP User Auth Editor</title>
</head>

<body>
    <div class="container" style="margin-top: 5%;">
        <?php
        try {
            $dbh = new PDO('mysql:host=' . $dbhost . ';dbname=' . $dbname . ';charset=utf8', $dbusername, $dbpassword);
            $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            echo '<div class="alert alert-danger" role="alert"><b>Database Error! </b>';
            echo 'Message: ' . $e->getMessage();
            echo "</div>";
            die();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if ($_POST['id_user'] == null) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Must input a User ID
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                ';
            } elseif (!is_numeric($_POST['id_user'])) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    User ID must be numeric!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                ';
            } else {
                if (isset($_POST["save"])) {
                    $qGetUserIDInfo = $dbh->prepare("SELECT id_user,username,stream_key,private,private_key,live_status FROM users WHERE id_user = :old_id_user");
                    $qGetUserIDInfo->execute(array(':old_id_user' => $_POST['save']));
                    $qGetUserIDResults = $qGetUserIDInfo->fetch(PDO::FETCH_ASSOC);
                    try {
                        if ($qGetUserIDResults["username"] != $_POST["username"]) {
                            $changeDB = $dbh->prepare("UPDATE users SET username=:username WHERE id_user=:old_id_user");
                            $changeDB->execute(array(':username' => $_POST['username'], ':old_id_user' => $_POST['save']));
                        }
                        if ($qGetUserIDResults["stream_key"] != $_POST["stream_key"]) {
                            $changeDB = $dbh->prepare("UPDATE users SET stream_key=:stream_key WHERE id_user=:old_id_user");
                            $changeDB->execute(array(':stream_key' => $_POST['stream_key'], ':old_id_user' => $_POST['save']));
                        }
                        if ($qGetUserIDResults["private"] != $_POST["private"]) {
                            $changeDB = $dbh->prepare("UPDATE users SET private=:private WHERE id_user=:old_id_user");
                            $changeDB->execute(array(':private' => $_POST['private'], ':old_id_user' => $_POST['save']));
                        }
                        if ($qGetUserIDResults["private_key"] != $_POST["private_key"]) {
                            $changeDB = $dbh->prepare("UPDATE users SET private_key=:private_key WHERE id_user=:old_id_user");
                            $changeDB->execute(array(':private_key' => $_POST['private_key'], ':old_id_user' => $_POST['save']));
                        }
                        if ($qGetUserIDResults["id_user"] != $_POST["id_user"]) {
                            $changeDB = $dbh->prepare("UPDATE users SET id_user=:id_user WHERE id_user=:old_id_user");
                            $changeDB->execute(array(':id_user' => $_POST['id_user'], ':old_id_user' => $_POST['save']));
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger" role="alert"><b>Database Error! </b>';
                        echo 'Message: ' . $e->getMessage();
                        echo "</div>";
                        die();
                    }
                }

                if (isset($_POST["delete"])) {
                    try {
                        $changeDB = $dbh->prepare("DELETE FROM users WHERE `id_user`=:old_id_user");
                        $changeDB->execute(array(':old_id_user' => $_POST["delete"]));
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger" role="alert"><b>Database Error! </b>';
                        echo 'Message: ' . $e->getMessage();
                        echo "</div>";
                        die();
                    }
                }

                if (isset($_POST["new"])) {
                    try {
                        $changeDB = $dbh->prepare("INSERT INTO users (id_user, username, stream_key, private, private_key, live_status) VALUES (:id_user, :username, :stream_key, :private, :private_key, 0)");
                        $changeDB->execute(array(
                            'id_user' => $_POST["id_user"],
                            'username' => $_POST['username'],
                            'stream_key' => $_POST['stream_key'],
                            'private' => $_POST['private'],
                            'private_key' => $_POST['private_key']
                        ));
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger" role="alert"><b>Database Error! </b>';
                        echo 'Message: ' . $e->getMessage();
                        echo "</div>";
                        die();
                    }
                }
            }
        }

        echo '
        <div class="row" style="margin-bottom: 5%;">
            <div class="col-xl-9"><h1 style="margin-bottom: 5%;">RTMP User Auth Editor</h1></div>
            <div class="col-xl-3">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#newUser">Create User</button>
                    <a class="btn btn-primary btn-lg" href="index.php">Back</a>
                </div>
            </div>
        </div>
        ';

        $qGetUsersInfo = "SELECT * FROM users";
        $qGetUsersResult = $dbh->query('select id_user,username,stream_key,private,private_key,live_status from users');

        echo '<div class="row row-cols-1 row-cols-lg-2 g-4">';
        while ($loopUsersResult = $qGetUsersResult->fetch()) {
            echo '
            <form method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">
                <div class="card">
                    <h5 class="card-header">User ' . $loopUsersResult['id_user'];
            if ($loopUsersResult['live_status'] == 1) {
                echo ' <span class="badge bg-success">On Live</span>';
            }
            if ($loopUsersResult['username'] == null || $loopUsersResult['stream_key'] == null) {
                echo ' <span class="badge bg-danger">Unavailable</span>';
            }
            if ($loopUsersResult['private'] == 1 && $loopUsersResult['private_key'] == null) {
                echo ' <span class="badge bg-danger">Private password not set</span>';
            }
            echo '</h5><div class="card-body">
                        <div class="row align-items-center" style="margin-bottom: 1%;">
                            <div class="col-sm-3">User ID</div>
                            <div class="col-sm-9"><input type="text" class="form-control" name="id_user" maxlength="11" value="' . $loopUsersResult['id_user'] . '"></div>
                        </div>
                        <div class="row align-items-center" style="margin-bottom: 1%;">
                            <div class="col-sm-3">Username</div>
                            <div class="col-sm-9"><input type="text" class="form-control" name="username" maxlength="255" value="' . $loopUsersResult['username'] . '"></div>
                        </div>
                        <div class="row align-items-center" style="margin-bottom: 1%;">
                            <div class="col-sm-3">Stream key</div>
                            <div class="col-sm-9"><input type="text" class="form-control" name="stream_key" maxlength="255" value="' . $loopUsersResult['stream_key'] . '"></div>
                        </div>
                        <div class="row align-items-center" style="margin-bottom: 1%;">
                            <div class="col-sm-3">Private stream</div>
                            <div class="col-sm-9">
                                <select class="form-select" name="private">';
            if ($loopUsersResult['private'] == 0) {
                echo '<option selected value="0">False</option>
                          <option value="1">True</option>';
            }
            if ($loopUsersResult['private'] == 1) {
                echo '<option value="0">False</option>
                          <option selected value="1">True</option>';
            }
            echo '</select>
                            </div>
                        </div>
                        <div class="row align-items-center" style="margin-bottom: 1%;">
                            <div class="col-sm-3">Private password</div>
                            <div class="col-sm-9"><input type="text" class="form-control" name="private_key" maxlength="255" value="' . $loopUsersResult['private_key'] . '"></div>
                        </div>';
            if ($loopUsersResult['username'] != null && $loopUsersResult['stream_key'] != null) {
                if ($loopUsersResult['private'] == 1 && $loopUsersResult['private_key'] != null) {
                    echo '<div class="alert alert-success" role="alert">OBS stream server: ' . $rtmpurl . '<br>OBS stream key: ' . $loopUsersResult['stream_key'] . '<br>Stream URL: '. $rtmpurl . $loopUsersResult['username'] . '?key=' . $loopUsersResult['private_key'] . '</div>';
                }
                if ($loopUsersResult['private'] == 1 && $loopUsersResult['private_key'] == null) {
                    echo '<div class="alert alert-success" role="alert">OBS stream server: ' . $rtmpurl . '<br>OBS stream key: ' . $loopUsersResult['stream_key'] . '<br>Stream URL: Temporarily unavailable</div>';
                }
                if ($loopUsersResult['private'] == 0) {
                    echo '<div class="alert alert-success" role="alert">OBS stream server: ' . $rtmpurl . '<br>OBS stream key: ' . $loopUsersResult['stream_key'] . '<br>Stream URL: '. $rtmpurl . $loopUsersResult['username'] . '</div>';
                }
            }
            echo '</div>
                    <div class="card-footer">
                    <div class="btn-group">';
            if ($loopUsersResult['live_status'] == 1) {
                if ($loopUsersResult['private'] == 1 && $loopUsersResult['private_key'] != null) {
                    echo ' <a class="btn btn-primary" href="'. $rtmpurl . $loopUsersResult['username'] . '?key=' . $loopUsersResult['private_key'] . '" >Watch</a>';
                }
                if ($loopUsersResult['private'] == 0) {
                    echo ' <a class="btn btn-primary" href="'. $rtmpurl . $loopUsersResult['username'] . '" >Watch</a>';
                }
            }
            echo '<button type="submit" class="btn btn-primary" value="' . $loopUsersResult['id_user'] . '" name="save">Save</button>
                        <button type="submit" class="btn btn-danger" value="' . $loopUsersResult['id_user'] . '" name="delete">Delete</button>
            
                        </div>
                        </div>
                </div>
            </form>';
        }
        echo '</div>';

        ?>
        <hr style="margin-top: 5%;">
        <p class="text-center">
            Powered by <a style="text-decoration: none;" href="https://github.com/Sora012" target="_blank">Sora012</a> and <a style="text-decoration: none;" href="https://github.com/SummonHIM" target="_blank">SummonHIM</a>
        </p>
    </div>

    <div class="modal fade" id="newUser" tabindex="-1" aria-labelledby="newUserLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <?php echo '<form method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">' ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="newUserLabel">Create a new user</h5>
                </div>
                <div class="modal-body">
                    <div class="row align-items-center" style="margin-bottom: 1%;">
                        <div class="col-lg-3">User ID</div>
                        <div class="col-lg-9"><input type="text" class="form-control" name="id_user" maxlength="11">
                        </div>
                    </div>
                    <div class="row align-items-center" style="margin-bottom: 1%;">
                        <div class="col-lg-3">Username</div>
                        <div class="col-lg-9"><input type="text" class="form-control" name="username" maxlength="255">
                        </div>
                    </div>
                    <div class="row align-items-center" style="margin-bottom: 1%;">
                        <div class="col-lg-3">Stream key</div>
                        <div class="col-lg-9"><input type="text" class="form-control" name="stream_key" maxlength="255">
                        </div>
                    </div>
                    <div class="row align-items-center" style="margin-bottom: 1%;">
                        <div class="col-lg-3">Private stream</div>
                        <div class="col-lg-9">
                            <select class="form-select" name="private">';
                                <option selected value="0">False</option>
                                <option value="1">True</option>';
                            </select>
                        </div>
                    </div>
                    <div class="row align-items-center" style="margin-bottom: 1%;">
                        <div class="col-lg-3">Private password</div>
                        <div class="col-lg-9"><input type="text" class="form-control" name="private_key" maxlength="255">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" name="new">Save</button>
                </div>
                <?php echo '</form>' ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>