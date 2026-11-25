<?php
//Connect to Database
$hostname = "localhost";
$username = "ecpi_user";
$password = "Password1";
$dbname = "sdc310_wk3pa";
$conn = mysqli_connect($hostname, $username, $password, $dbname);

//Establish variables to support add/edit/delete
$userNo = -1;
$fullName = "";
$birthdate = "";
$favoriteColor = "";
$favoritePlace = "";
$nickname = "";

//Variables to determine the type of operation
$add = false;
$edit = false;
$update = false;
$delete = false;

if (isset($_POST['user_no'])) {
    $userNo = (int)$_POST['user_no'];
    $add = isset($_POST['add']);
    $edit = isset($_POST['edit']);
    $update = isset($_POST['update']);
    $delete = isset($_POST['delete']);
}

if ($add) {
    //Add new personal information
    $fullName = $_POST['fullname'];
    $birthdate = $_POST['birthdate'];
    $favoriteColor = $_POST['color'];
    $favoritePlace = $_POST['place'];
    $nickname = $_POST['nickname'];

    $stmt = mysqli_prepare($conn, "INSERT INTO personal_info
        (FullName, Birthdate, FavoriteColor, FavoritePlace, Nickname)
        VALUES (?, ?, ?, ?, ?)");

    mysqli_stmt_bind_param($stmt, "sssss",
        $fullName, $birthdate, $favoriteColor, $favoritePlace, $nickname);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    //Clear the fields
    $userNo = -1;
    $fullName = "";
    $birthdate = "";
    $favoriteColor = "";
    $favoritePlace = "";
    $nickname = "";
}
else if ($edit) {
    //Get the selected user's information
    $selQuery = "SELECT * FROM personal_info WHERE UserNo = $userNo";
    $result = mysqli_query($conn, $selQuery);
    $userInfo = mysqli_fetch_assoc($result);

    //Fill in the values to allow for edit
    $fullName = $userInfo['FullName'];
    $birthdate = $userInfo['Birthdate'];
    $favoriteColor = $userInfo['FavoriteColor'];
    $favoritePlace = $userInfo['FavoritePlace'];
    $nickname = $userInfo['Nickname'];
}
else if ($update) {
    //Update existing personal information
    $fullName = $_POST['fullname'];
    $birthdate = $_POST['birthdate'];
    $favoriteColor = $_POST['color'];
    $favoritePlace = $_POST['place'];
    $nickname = $_POST['nickname'];

    $stmt = mysqli_prepare($conn, "UPDATE personal_info SET
        FullName = ?, Birthdate = ?, FavoriteColor = ?,
        FavoritePlace = ?, Nickname = ?
        WHERE UserNo = ?");

    mysqli_stmt_bind_param($stmt, "sssssi",
        $fullName, $birthdate, $favoriteColor,
        $favoritePlace, $nickname, $userNo);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    //Clear the fields
    $userNo = -1;
    $fullName = "";
    $birthdate = "";
    $favoriteColor = "";
    $favoritePlace = "";
    $nickname = "";
}
else if ($delete) {
    //Delete the selected user
    $delQuery = "DELETE FROM personal_info WHERE UserNo = $userNo";
    mysqli_query($conn, $delQuery);
    $userNo = -1;
}

//Query for all users
$query = "SELECT * FROM personal_info";
$result = mysqli_query($conn, $query);
?>

<style>
    table {
        border-spacing: 5px;
    }
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
    th, td {
        padding: 15px;
        text-align: center;
    }
    th {
        background-color: lightskyblue;
    }
    tr:nth-child(even) {
        background-color: whitesmoke;
    }
    tr:nth-child(odd) {
        background-color: lightgray;
    }
</style>

<html>
<head>
    <title>Dustin Pulu Wk 3 Performance Assessment</title>
</head>

<body>
    <h2>Current Users:</h2>

    <table>
        <tr style="font-size:large;">
            <th>User #</th>
            <th>Name</th>
            <th>Date of Birth</th>
            <th>Favorite Color</th>
            <th>Favorite Place To Visit</th>
            <th>Nickname</th>
            <th></th>
            <th></th>
        </tr>

        <?php while ($row = mysqli_fetch_array($result)): ?>
        <tr>
            <td><?php echo $row["UserNo"]; ?></td>
            <td><?php echo htmlspecialchars($row["FullName"]); ?></td>
            <td><?php echo htmlspecialchars($row["Birthdate"]); ?></td>
            <td><?php echo htmlspecialchars($row["FavoriteColor"]); ?></td>
            <td><?php echo htmlspecialchars($row["FavoritePlace"]); ?></td>
            <td><?php echo htmlspecialchars($row["Nickname"]); ?></td>

            <td>
                <form method="POST">
                    <input type="submit" value="Edit" name="edit">
                    <input type="hidden"
                        value="<?php echo $row["UserNo"]; ?>"
                        name="user_no">
                </form>
            </td>

            <td>
                <form method="POST">
                    <input type="submit" value="Delete" name="delete">
                    <input type="hidden"
                        value="<?php echo $row["UserNo"]; ?>"
                        name="user_no">
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <form method="POST">
        <input type="hidden"
            value="<?php echo $userNo; ?>" name="user_no">

        <h3>Enter your name:
            <input type="text" name="fullname"
                value="<?php echo htmlspecialchars($fullName); ?>">
        </h3>

        <h3>Enter your birthdate:
            <input type="text" name="birthdate"
                value="<?php echo htmlspecialchars($birthdate); ?>">
        </h3>

        <h3>Enter your favorite color:
            <input type="text" name="color"
                value="<?php echo htmlspecialchars($favoriteColor); ?>">
        </h3>

        <h3>Enter your favorite place to visit:
            <input type="text" name="place"
                value="<?php echo htmlspecialchars($favoritePlace); ?>">
        </h3>

        <h3>Enter your nickname:
            <input type="text" name="nickname"
                value="<?php echo htmlspecialchars($nickname); ?>">
        </h3>

        <?php if (!$edit): ?>
            <input type="submit" value="Add User" name="add">
        <?php else: ?>
            <input type="submit" value="Update User" name="update">
        <?php endif; ?>
    </form>
</body>
</html>