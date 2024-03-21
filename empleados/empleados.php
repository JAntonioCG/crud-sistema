<?php
  include('../conexion/conexion.php');
  $txtID = (isset($_POST['txtID'])) ? $_POST['txtID'] : '';
  $txtNombre = (isset($_POST['txtNombre'])) ? $_POST['txtNombre'] : '';
  $txtApaterno = (isset($_POST['txtApaterno'])) ? $_POST['txtApaterno'] : '';
  $txtAmaterno = (isset($_POST['txtAmaterno'])) ? $_POST['txtAmaterno'] : '';
  $txtcorreo = (isset($_POST['txtcorreo'])) ? $_POST['txtcorreo'] : '';
  $txtFoto = (isset($_FILES['txtFoto']['name'])) ? $_FILES['txtFoto']['name'] : '';
  $accion = (isset($_POST['accion'])) ? $_POST['accion'] : '';
  $error = array();
  $accionAgregar = '';
  $accionModificar = $accionEliminar = $accionCancelar = "disabled";
  $mostrarModal = false;
  switch ($accion) {
    case 'btnAgregar':
      if ($txtNombre == '') {
        $error['Nombre'] = 'Nombre no puede ir vacio';
      }
      if ($txtApaterno == '') {
        $error['Apaterno'] = 'Apaterno no puede ir vacio';
      }
      if ($txtAmaterno == '') {
        $error['Amaterno'] = 'Amaterno no puede ir vacio';
      }
      if ($txtcorreo == '') {
        $error['correo'] = 'correo no puede ir vacio';
      }
      if (count($error) > 0) {
        $mostrarModal = true;
        break;
      }
      $qwery = $pdo->prepare('INSERT INTO empleados(id, nombre, Apaterno, Amaterno, correo, foto) VALUES(null, :nombre, :Apaterno, :Amaterno, :correo, :foto)');
      // echo $qwery;
      // die;
      $qwery->bindParam(':nombre', $txtNombre);
      $qwery->bindParam(':Apaterno', $txtApaterno);
      $qwery->bindParam(':Amaterno', $txtAmaterno);
      $qwery->bindParam(':correo', $txtcorreo);
      $fecha = new DateTime();
      $nombreFoto = ($txtFoto != '') ? $fecha->getTimestamp() . '_' . $_FILES['txtFoto']['name'] : 'image.jpg';
      $tmpFoto = $_FILES['txtFoto']['tmp_name'];
      if ($tmpFoto != '') {
        move_uploaded_file($tmpFoto, '../imagenes/'.$nombreFoto);
      }
      $qwery->bindParam(':foto', $nombreFoto);
      // echo $qwery;
      // die;
      $qwery->execute();
      header('Location: index.php');
    break;
    case 'btnEliminar':
      $qweryEliminar = $pdo->prepare('SELECT foto FROM
        empleados WHERE id=:id');
      $qweryEliminar->bindParam(':id', $txtID);
      $qweryEliminar->execute();
      $empleado = $qweryEliminar->fetch(PDO::FETCH_LAZY);
      if(isset($empleado['foto']) && $empleado['foto'] != 'image.jpg') {
        if(file_exists('../imagenes/'.$empleado['foto'])) {
          unlink('../images/'.$empleado['foto']);
        }
      }
      $qweryDelete = $pdo->prepare('DELETE FROM empleados
        WHERE id=:id');
      $qweryDelete->bindParam(':id', $txtID);
      $qweryDelete->execute();
      header('Location: index.php');
      break;
    case 'btnModificar':
      $qweryUpdate = $pdo->prepare("UPDATE empleados SET
      nombre=:nombre,
      Apaterno=:Apaterno,
      Amaterno=:Amaterno,
      correo=:correo
      WHERE id=:id
    ");
    $qweryUpdate->bindParam(':nombre', $txtNombre);
    $qweryUpdate->bindParam(':Apaterno', $txtApaterno);
    $qweryUpdate->bindParam(':Amaterno', $txtAmaterno);
    $qweryUpdate->bindParam(':correo', $txtcorreo);
    $qweryUpdate->bindParam(':id', $txtID);
    $qweryUpdate->execute();
    $fecha = new DateTime();
    $nombreFoto = ($txtFoto != '') ? $fecha->getTimestamp() . "_" . $_FILES["txtFoto"]["name"] : "image.jpg";
    $tmpFoto = $_FILES['txtFoto']['tmp_name'];
    if ($tmpFoto != '') {
      move_uploaded_file($tmpFoto, "../imagenes/".$nombreFoto);//igual
      $qweryEliminar = $pdo->prepare('SELECT foto FROM
        empleados WHERE id=:id');
      $qweryEliminar->bindParam(':id', $txtID);
      $qweryEliminar->execute();
      $empleado = $qweryEliminar->fetch(PDO::FETCH_LAZY);
      if(isset($empleado['foto']) && $empleado['foto'] != 'image.jpg') {
        if(file_exists('../imagenes/'.$empleado['foto'])) {
          unlink('../images/'.$empleado['foto']);
        }
      }
      $qweryUpdateFoto = $pdo->prepare('UPDATE empleados SET foto=:foto WHERE id=:id');
      $qweryUpdateFoto->bindParam(':foto', $nombreFoto);
      $qweryUpdateFoto->bindParam(':id', $txtID);
      $qweryUpdateFoto->execute();
    }
    header('Location: index.php');
    break;
    case 'btnCancelar':
      header('Location: index.php');
    break;
    case 'Seleccionar':
      $accionAgregar = "disabled";
      $accionModificar = $accionEliminar = $accionCancelar = '';
      $mostrarModal = true;
      $qweryEmpleado = $pdo->prepare('SELECT * FROM empleados WHERE id=:id');
      $qweryEmpleado->bindParam(':id', $txtID);
      $qweryEmpleado->execute();
      $empleado = $qweryEmpleado->fetch(PDO::FETCH_LAZY);
      $txtNombre = $empleado['nombre'];
      $txtApaterno = $empleado['Apaterno'];
      $txtAmaterno = $empleado['Amaterno'];
      $txtcorreo = $empleado['correo'];
      $txtFoto = $empleado['foto'];
    break;
  }
  $qwerySelect = $pdo->prepare('SELECT * FROM empleados');
  $qwerySelect->execute();
  $listaEmpleados = $qwerySelect->fetchAll(PDO::FETCH_ASSOC);
?>