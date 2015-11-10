<?php
	/**
	 * Clase Error. Contiene un mensaje de error y el campo al que hace referencia.
	 * @author Javier Latorre -> jlalovi@gmail.com
	 *
	 */
	class Error	{
		private $mensaje_error;
		private $campo;
		
		/**
		 * Constructor del objeto de tipo 'Error'
		 * @param string $msj_error
		 * @param string $campo
		 */
		public function Error($msj_error,$campo) {
			$this->campo=$campo;
			$this->mensaje_error=$msj_error;
		}
		public function getMensaje() {
			return $this->mensaje_error;
		}
		public function getCampo() {
			return $this->campo;
		}
	}
	
	/**
	 * Clase Validación. 
	 * @author Javier Latorre -> jlalovi@gmail.com
	 *
	 */
	class Validacion {
		private $errores=array(); // Colección de objetos error fuardados en el objeto de tipo 'Validacion' instanciado
		private $_clase=""; // string del nombre de la clase NO el objeto en sí.
		private $_valores=array(); // Array asociativo de los valores pasados por parámetro a validar
		
		/**
		 * CONSTRUCTOR
		 * Valida el array asociativo de '$valores' pasados por parámetro en función de la documentación de las propiedades del
		 * nombre de la $clase pasada por parámetro.
		 * 
		 * @param string $clase -> ¡OJO! Se pasa por parámetro el NOMBRE de la clase, NO el objeto.
		 * @param array $valores -> valores (array asociativo) que se quieren validar. 
		 */
		public function Validacion($clase="",$valores=array()) {
			$this->_clase=$clase;
			$this->_valores=$valores;
			$this->ValidaPropiedades();
		}
		
		/**
		 * Crea un objeto de tipo 'ReflectionClass' a partir del nombre de la $clase pasada por parámetro en Validacion($clase, $valores),
		 * despúes obtiene un array de las propiedades de dicha clase, y se analiza una a una a partir de la función 'ValidaPropiedad'
		 */
		private function ValidaPropiedades() {
			$clase=new ReflectionClass($this->_clase);
			$propiedades=$clase->getProperties();
			foreach ($propiedades as $propiedad) {
				$this->ValidaPropiedad($propiedad);
			}
		}
		
		/**
		 * <p>Busca en el contenido de los comentarios de la propiedad de la clase analizada indicadores y condiciones para la validación
		 * de los valores pasados por parámetro en Validacion($clase,$valores).
		 * <p>Es importante por tanto que el nombre de la propiedad de la clase sea igual al índice asociativo del array $valores
		 * pasados por parámetro en Validacion($clase,$valores).
		 * <p>En este método se escribirán las condiciones que deben estar escritas en los comentarios PHPdocs de cada uno de las propiedades
		 * a evaluar. Si se cumple la condición, se llamará a la función de validación correspondiente automáticamente.
		 * 
		 * @param object $propiedad -> Objetos de tipo 'Property', que contienen la información de cada una de las propiedades de la clase analizada
		 */
		private function ValidaPropiedad($propiedad) {
			$documentacion=$propiedad->getDocComment();
			$nombre_propiedad = $propiedad->getName();
			$valor_propiedad = $this->_valores[$propiedad->getName()];
			//var_dump($documentacion);
			
			if(strpos($documentacion, "@requerido")) {
				$this->validaRequerido($nombre_propiedad, $valor_propiedad);
			}
			
			if(($pos=substr_count($documentacion, "@rango"))) {
				$patron = '/@rango=(\d{1,9}),(\d{1,9})¬/';
				preg_match($patron, $documentacion, $doc_rango); // Busco en el comentario la coincidencia del patrón y almaceno el array de parámetros.
				
				$min = $doc_rango[1];
				$max = $doc_rango[2];
				
				$this->validaRango($nombre_propiedad, $valor_propiedad, $min, $max);
			}
			
			if(strpos($documentacion, "@validaDni")) {
				$this->validaDni($nombre_propiedad, $valor_propiedad);
			}
		}
		
		/**
		 * Comprueba si el $valor cumple unas condiciones, y en caso contrario crea y almacena un objeto 'Error' en el array
		 * 'errores' con el índice asociativo '$campo'['requerido']
		 * @param string $campo -> nombre del campo
		 * @param string $valor -> valor del campo
		 */
		public function validaRequerido($campo,$valor)
		{
			if($valor==NULL || trim($valor)=="")
			{
				$this->errores[$campo]["requerido"]=new error(" El campo '$campo' no puede estar vacio.", $campo);
				return FALSE;
			}
			return TRUE;
		}
		
		/**
		 * Comprueba si el $valor cumple unas condiciones, y en caso contrario crea y almacena un objeto 'Error' en el array
		 * 'errores' con el índice asociativo '$campo'['requerido']
		 * @param string $campo -> nombre del campo
		 * @param string $valor -> valor del campo
		 * @param number $max -> valor máximo en la condición
		 * @param number $min -> valor mínimo en la condición
		 */
		public function validaRango($campo,$valor,$min=0,$max=0) {
			if(!is_numeric($valor) || $valor>$max || $valor<$min) {
				$this->errores[$campo]["rango_incorrecto"]=new error(" El campo '$campo' debe ser númerico y entre $min y $max.", $campo);
				return FALSE;
			}
			return TRUE;
		}
		
		/**
		 * Comprueba si el $valor cumple unas condiciones, y en caso contrario crea y almacena un objeto 'Error' en el array
		 * 'errores' con el índice asociativo '$campo'['requerido']
		 * @param string $campo -> nombre del campo
		 * @param string $valor -> valor del campo
		 */
		public function validaDni($campo,$valor) {
			if(preg_match('/[0-9]{7,8}[A-Za-z]/', $valor)!=1) {
				$this->errores[$campo]["dni_incorrecto"]=new error(" El campo '$campo' no es un DNI válido.", $campo);
				return  FALSE;
			}
			return TRUE;
		}
		
		/**
		 * Devuelve el array de errores almacenados en la colección.
		 */
		public function getErrores() {
			return $this->errores;
		}
	}
?>