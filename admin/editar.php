<?php session_start();

require 'config.php';
require '../functions.php';

comprobarSession();

$conexion = conexion($bd_config);

if (!$conexion) {
	header('Location: ../error.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$titulo = limpiarDatos($_POST['titulo']);
	$extracto = limpiarDatos($_POST['extracto']);
	$texto = $_POST['texto'];
	$id = limpiarDatos($_POST['id']);
	$thumb_guardada = $_POST['thumb-guardada'];
	$thumb = $_FILES['thumb'];

	// checamos si se agrego una nueva foto
	if (empty($thumb['name'])) {
		$thumb = $thumb_guardada;
	} else {
		$archivo_subido = '../' . $blog_config['carpeta_imagenes'] . $_FILES['thumb']['name'];
		move_uploaded_file($_FILES['thumb']['tmp_name'], $archivo_subido);
		$thumb = $_FILES['thumb']['name'];

	}


	$statement = $conexion->prepare(
		'UPDATE articulos SET titulo = :titulo, extracto = :extracto, texto = :texto, thumb = :thumb WHERE id = :id'
		);

	$statement->execute(array(
		':titulo'   => $titulo,
		':extracto' => $extracto,
		':texto'    => $texto,
		':thumb'    => $thumb,
		':id'       =>$id

		));

	header('Location: ' . RUTA . '/admin');

} else {
	$id_articulo = id_articulo($_GET['id']);

	if (empty($id_articulo)) {
		header('Location: ' . RUTA . '/admin');
	}

	$post = obtener_post_por_id($conexion, $id_articulo);

	if (!$post) {
		header('Location: ' . RUTA . '/admin');
	}

	//print_r($post);

	$post = $post[0];
}



require '../views/editar.view.php';

?>