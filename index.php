<?php
  if(isset($_POST['ASC'])){
    $ascending_query = "SELECT * FROM books ORDER BY ID ASC";
    $result = executeQuery($ascending_query);
  }

  elseif(isset($_POST['DESC'])){
    $descending_query = 'SELECT * FROM books ORDER BY ID DESC';
    $result = executeQuery($descending_query);
  }

  elseif (isset($_POST['SEARCH'])) {
    $search_box = $_POST['searchbox'];
    $search_query = "SELECT * FROM books WHERE name LIKE '%$search_box%' OR description LIKE '%$search_box%' OR ID LIKE '%$search_box%'";
    $result = executeQuery($search_query);
  }

  elseif (isset($_POST['CREATE_NEW'])) {
    $name_value = $_POST['name_value'];
    $desc_value = $_POST['desc_value'];
    
    if (empty($name_value) || empty($desc_value)) {
        echo "Please fill in all text fields.";
    } else {
        $image_value = $_POST['insert_image'];
        if (isset($_FILES['image_upload'])) {
            $target = "uploads/" . basename($_FILES['image_upload']['name']);
            if (move_uploaded_file($_FILES['image_upload']['tmp_name'], $target)) {
                $image_value = $target;
            } else {
                echo "Failed to upload image.";
                header('Location: index.php');
            }
        }

        $insert_query = "INSERT INTO `books` (`name`, `description`, `image_href`) VALUES ('$name_value', '$desc_value', '$image_value')";
        executeQuery($insert_query);
        header('Location: index.php');
    }
  }

  elseif(isset($_POST['DELETE_RECORD'])){
    $id_to_delete = $_POST['delete_id'];
    
    $delete_query = "DELETE FROM books WHERE ID = $id_to_delete";
    executeQuery($delete_query);
    
    header('Location: index.php');
  }

  elseif (isset($_POST['EDIT_RECORD'])) {
    $id_to_edit = $_POST['edit_id'];
    $edit_query = "SELECT * FROM books WHERE ID = $id_to_edit";
    $edit_result = executeQuery($edit_query);
    $record_to_edit = mysqli_fetch_array($edit_result);

    ?>
    <form action="index.php" method="post">
        <input type="hidden" name="edit_id" value="<?php echo $record_to_edit[0]; ?>">
        <input type="text" name="edit_name" value="<?php echo $record_to_edit[1]; ?>" placeholder="Name">
        <input type="text" name="edit_description" value="<?php echo $record_to_edit[2]; ?>" placeholder="Description">
        <input type="text" name="edit_image" value="<?php echo $record_to_edit[3]; ?>" placeholder="Image Link">
        <input type="submit" name="SUBMIT_EDIT" value="Submit Edit">
    </form>
    <?php
  }

  elseif (isset($_POST['SUBMIT_EDIT'])) {
    $id = $_POST['edit_id'];
    $name = $_POST['edit_name'];
    $description = $_POST['edit_description'];
    $image = $_POST['edit_image'];
    $update_query = "UPDATE books SET name = '$name', description = '$description', image_href = '$image' WHERE ID = $id";
    executeQuery($update_query);
    header('Location: index.php');
    $record_to_edit = array(null, null, null, null);
  }

  else{
    $default_query = "SELECT * FROM books";
    $result = executeQuery($default_query);
  }

  function executeQuery($query){
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "Inventory";
  
    $connect = mysqli_connect($servername, $username, $password, $database);
  
    if (!$connect) {
      die("Connection failed: " . mysqli_connect_error());
    }
  
    $result = mysqli_query($connect, $query);
  
    if (!$result) {
      die("Query failed: " . mysqli_error($connect));
    }
  
    mysqli_close($connect);
    return $result;
  }
?>

<!DOCTYPE html>
  <head>
    <link rel = "stylesheet" type = "text/css" href = "stylesheet_main.css">
    <title>CRUD Operations</title>
  </head>
  <h1>Online Inventory Sheet</h1>
  <body>
    <form class="search_class" action="index.php" method="post">
      <input type="text" name="searchbox" id="search_text" placeholder="Search by Name or Description">
      <input type="submit" name="SEARCH" value="Search"><br>
    </form>

    <form action="index.php" method="post" enctype="multipart/form-data">
      <br><label id="filter_label">Filters : </label>
      <input type="submit" name="ASC" value="Ascending">
      <input type="submit" name="DESC" value="Descending"><br><br>
      <label id="filter_label">Add New Book : </label>
      <input type="text" name="name_value" placeholder="Name">
      <input type="text" name="desc_value" placeholder="Description">
      <input type="text" name="insert_image" placeholder="Image Link">
      <input type="submit" name="CREATE_NEW" value="Create New Entry"><br><br>
    </form>
    
    <table class="table1">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Description</th>
          <th>Image</th>
          <th>Operations</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_array($result)): ?>
          <tr>
            <td><?php echo $row[0]; ?></td>
            <td><?php echo $row[1]; ?></td>
            <td><?php echo $row[2]; ?></td>
            <td><img src="<?php echo $row[3]; ?>" width="100" height="100" alt="No Image"></td>
            <td>
              <form action="index.php" method="post" style="display: inline;">
                <input type="hidden" name="edit_id" value="<?php echo $row[0]; ?>">
                <input type="submit" name="EDIT_RECORD" value="Edit">
              </form>
              <form action="index.php" method="post" style="display: inline;">
                <input type="hidden" name="delete_id" value="<?php echo $row[0]; ?>">
                <input type="submit" name="DELETE_RECORD" value="Delete">
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </body>
</html>