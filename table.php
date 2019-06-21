<!doctype html>
<html lang="ru">
  <head>
    <title>Courses Table</title>
  </head>
<body>
  <?php
    $host = 'localhost'; $user = 'crm'; $pass = '6QjgPjxQ'; $db_name = 'crm';
    $cont = mysqli_connect($host, $user, $pass, $db_name); //connect to the database, cont is the connection handle
    echo "<meta charset=\"utf8\">";
    header('Content-Type: text/html; charset = utf-8');
    mysqli_query($cont, 'SET NAMES utf8');


    //error massage if failed to connect
    if (!$cont) {
      echo 'Can not connect with DB.: code of mistake' . mysqli_connect_errno() . ', mistake: ' . mysqli_connect_error();
      exit;
    }

    //Если переменная name передана
    if (isset($_POST["name"])) {
      //Если это запрос на обновление, то обновляем  
      if (isset($_GET['red'])) {
        $ifActive = $_POST['ifActive'] == 'true' ? 1 : 0;
        $query = 
          "UPDATE `Courses` 
          SET 
            `name` = '{$_POST['name']}', 
            `description` = '{$_POST['description']}', 
            `ifActive` ={$ifActive}, 
            `idBuilding` = '{$_POST['idBuilding']}', 
            `idTeachers` = '{$_POST['idTeachers']}' 
          WHERE `id` ='{$_GET['red']}'";
        $sql = mysqli_query($cont, $query);
      } else {
        //Иначе добаляем данные, подставляя их в запрос
        $ifActive = $_POST['ifActive'] == 'true' ? 1 : 0;
        $query = 
        "INSERT INTO `Courses` 
        (`name`, `description`, `ifActive`, `idBuilding`, `idTeachers`) 
        VALUES (
          '{$_POST['name']}', 
          '{$_POST['description']}', 
          {$ifActive}, 
          '{$_POST['idBuilding']}', 
          '{$_POST['idTeachers']}'
        )";
        $sql = mysqli_query($cont, $query);
      }

      //Если данные добавлены успешно прошла успешно
      if (!$sql) {
        echo '<p>Error: ' . mysqli_error($cont) . '</p>';
      }
    }

    //Удаляем строку с данными через id
    if (isset($_GET['del'])) {
      $sql = mysqli_query($cont, "DELETE FROM `Courses` WHERE `id` = '{$_GET['del']}'");
      if (!$sql) {
        echo '<p>Error: ' . mysqli_error($cont) . '</p>';
      }
    }

    //Если передана переменная red передана, то обновляем данные. (Достаем их из БД и формируем массив)
    if (isset($_GET['red'])) {
      $sql = mysqli_query($cont, "SELECT *  FROM `Courses` WHERE `id`='{$_GET['red']}'");
      $product = mysqli_fetch_array($sql);
    }
  ?>

<?php
  //Получаем данные   
  $sql = mysqli_query($cont, "SELECT * FROM `Courses`"); 

	function  get_teacher_name($id,$cont)
	{
		$sql = mysqli_query($cont, "SELECT `firstname`, `name`, `surname` from `Teachers` where `id`='".$id."'");
		while($result = mysqli_fetch_array($sql))
		{
			return $result = $result['firstname']." ".$result['name']." ".$result['surname'];
		}
	}
 
	function get_building_val($id,$cont)
	{
		$sql = mysqli_query($cont, "SELECT `name` from `Building` where `id`='".$id."'");
		while($result = mysqli_fetch_array($sql))
		{
			return $result['name'];
		}
	}
  echo '<h2>Courses Table</h2>';
  //выводим таблицу
  echo '<table border="2">';
  echo '<tr>
      <td> id </td><td> name </td> 
     <td> description </td><td> ifActive </td>
     <td> Building </td><td> Teacher </td>
     <td>Edit </td>
     <td> Delete </td>';
    while($result = mysqli_fetch_array($sql))
    {
      echo "<tr>
     <td> {$result['id']} </td><td> {$result['name']} </td> 
     <td> {$result['description']}</td><td>{$result['ifActive']}</td> 
     <td>". get_building_val($result['idBuilding'],$cont )."</td><td>".get_teacher_name($result['idTeachers'],$cont )."</td>
     <td><a href='?red={$result['id']}'>Edit</a> </td>
     <td> <a href='?del={$result['id']}'> Delete</a></td>";
    }
    echo "</TABLE>";

    ?>
   
  <!-- поля для создания новой заметки -->
  <!--<textarea name="description">-->
  <p></p>
  <form action="" method="post">
    <table>
        <tr>
        	 <td>Name:</td>
        	 <td><input type="text" name="name" value="<?= isset($_GET['red']) ? $product['name'] : ''; ?>"></td>
      	</tr>
      	<tr>
        	 <td>Description:</td>
        	 <td><input type="text" name="description" value="<?=  isset($_GET['red']) ? $product['description'] : ''; ?>"></textarea></td>
      	</tr>
      	<tr>
      		 <td>ifActive:</td>
      		 <td><select name="ifActive">
            <option><? if(isset($_GET['red'])) {echo $product['ifActive'] == 1 ? 'true' : 'false';} else {echo 'true';} ?> </option>
            <option><? if(isset($_GET['red'])) {echo $product['ifActive'] == 1 ? 'false' : 'true';} else {echo 'false';} ?> </option>
          </select></td></tr>
        </tr>
      	<tr>
        	 <td>idBuilding:</td>
             <td>
              <select name="idBuilding">
                <?
                  $currentName = mysqli_fetch_array(mysqli_query($cont, 'SELECT name FROM Building WHERE id='.$product['idBuilding']))['name'];
                  $sql = mysqli_query($cont, "SELECT id, name FROM Building"); 
                  
                  echo '<option>'.$currentName.'</option>';

                  while($result = mysqli_fetch_array($sql))
                  {
                    if ($result['id'] != $product['idBuilding']) {
                      echo '<option>'.$result['name'].'</option>';
                    }
                  } 
                ?>
              </select>
            </td>
      	</tr>
      	<tr>
        	 <td>idTeachers:</td>
        	 <td><select name="idTeachers">
              <? $sql = mysqli_query($cont, "SELECT firstname, name, surname FROM Teachers"); 
              while($result = mysqli_fetch_array($sql))
              {
                $result = $result['firstname']." ".$result['name']." ".$result['surname'];
                echo "<option>".$result."</option>";
              } ?></select></td>
      	</tr>
      	<tr>
        	 <td colspan="2"><input type="submit" value="ADD"></td>
      	</tr>
    </table>
  </form>
  <p><a href="?add=new">Add new note</a></p>
</body>
</html>