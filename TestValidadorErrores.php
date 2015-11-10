<?php

	require_once 'Libreria/Validaciones.php';
	require_once 'coleccion/Alumno.php';
	
	//var_dump($_POST);
	
	
	// INICIAR SI EXISTE POST
	if (!empty($_POST)) {
		
		// CREACIÓN DE CONSTRUCTORES:
		
		// Almaceno las variables recogidas en el POST
		$_dni=$_POST['dni'];
		$_nombre=$_POST['nombre'];
		$_edad=$_POST['edad'];
		
		// Instancio un objeto de validador.
		$ValidaPrueba=new Validacion("Alumno", $_POST);

		
	// COMPROBACIÓN DE ERRORES (Esta función sería la encargada de generar de nuevo los formularios)
	if(count($ValidaPrueba->getErrores())>0) {
		echo "<form action='' method='post'>";
		foreach ($_POST as $indice_campo=>$valor) {
			$label = ucfirst($indice_campo); // Para que la primera letra del índice sea mayúscula
			
			// NO HAY ERROR:
			if(!isset($ValidaPrueba->getErrores()[$indice_campo])) {
				echo "<label for='$indice_campo'>$label:</label>";
				echo "<input type='text' name='$indice_campo' id='$indice_campo' value='$valor'/>";
				echo "<br>";	
			}
			// HAY ERROR:
			else {
				// var_dump($ValidaPrueba->getErrores()[$indice_campo]);
				$mensaje_errores = "";
				foreach ($ValidaPrueba->getErrores()[$indice_campo] as $error) { // Cada campo puede tener varios errores!
					$mensaje_errores.= "<span class='error'>".$error->getMensaje()."</span>";
				}
				echo "<label for='$indice_campo'>$label:</label>";
				echo "<input type='text' class='campoerroneo' name='$indice_campo'
						id='$indice_campo'/>{$mensaje_errores}";
				echo "<br>";

			}
		}
		echo "<input type='submit' value='Enviar'/>";
		echo "</form>";
	}
	
} // FIN DE SI EXISTE CONTENIDO EN POST
else 
{
?>

<!-- HTML INICIAL ANTES DE HACER EL PRIMER POST -->
<form action="" method="post">
	<label for="dni">Dni:</label>
	<input type="text" name="dni" id="dni" />
	<br/>
	<label for="nombre">Nombre:</label>
	<input type="text" name="nombre" id="nombre" />
	<br/>
	<label for="edad">Edad:</label>
	<input type="text" name="edad" id="edad" />
	<br/>
	<input type="submit" value="Enviar" />
</form>

<?php 
}
?>