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
	 * Clase Validaci�n. 
	 * @author Javier Latorre -> jlalovi@gmail.com
	 *
	 */
	class Validacion {
		private $errores=array(); // Colecci�n de objetos error fuardados en el objeto de tipo 'Validacion' instanciado
		private $_clase=""; // string del nombre de la clase NO el objeto en s�.
		private $_valores=array(); // Array asociativo de los valores pasados por par�metro a validar
		
		/**
		 * CONSTRUCTOR
		 * Valida el array asociativo de '$valores' pasados por par�metro en funci�n de la documentaci�n de las propiedades del
		 * nombre de la $clase pasada por par�metro.
		 * 
		 * @param string $clase -> �OJO! Se pasa por par�metro el NOMBRE de la clase, NO el objeto.
		 * @param array $valores -> valores (array asociativo) que se quieren validar. 
		 */
		public function Validacion($clase="",$valores=array()) {
			$this->_clase=$clase;
			$this->_valores=$valores;
			$this->ValidaPropiedades();
		}
		
		/**
		 * Crea un objeto de tipo 'ReflectionClass' a partir del nombre de la $clase pasada por par�metro en Validacion($clase, $valores),
		 * desp�es obtiene un array de las propiedades de dicha clase, y se analiza una a una a partir de la funci�n 'ValidaPropiedad'
		 */
		private function ValidaPropiedades() {
			$clase=new ReflectionClass($this->_clase);
			$propiedades=$clase->getProperties();
			foreach ($propiedades as $propiedad) {
				$this->ValidaPropiedad($propiedad);
			}
		}
		
		/**
		 * <p>Busca en el contenido de los comentarios de la propiedad de la clase analizada indicadores y condiciones para la validaci�n
		 * de los valores pasados por par�metro en Validacion($clase,$valores).
		 * <p>Es importante por tanto que el nombre de la propiedad de la clase sea igual al �ndice asociativo del array $valores
		 * pasados por par�metro en Validacion($clase,$valores).
		 * <p>En este m�todo se escribir�n las condiciones que deben estar escritas en los comentarios PHPdocs de cada uno de las propiedades
		 * a evaluar. Si se cumple la condici�n, se llamar� a la funci�n de validaci�n correspondiente autom�ticamente.
		 * 
		 * @param object $propiedad -> Objetos de tipo 'Property', que contienen la informaci�n de cada una de las propiedades de la clase analizada
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
				$patron = '/@rango=(\d{1,9}),(\d{1,9})�/';
				preg_match($patron, $documentacion, $doc_rango); // Busco en el comentario la coincidencia del patr�n y almaceno el array de par�metros.
				
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
		 * 'errores' con el �ndice asociativo '$campo'['requerido']
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
		 * 'errores' con el �ndice asociativo '$campo'['requerido']
		 * @param string $campo -> nombre del campo
		 * @param string $valor -> valor del campo
		 * @param number $max -> valor m�ximo en la condici�n
		 * @param number $min -> valor m�nimo en la condici�n
		 */
		public function validaRango($campo,$valor,$min=0,$max=0) {
			if(!is_numeric($valor) || $valor>$max || $valor<$min) {
				$this->errores[$campo]["rango_incorrecto"]=new error(" El campo '$campo' debe ser n�merico y entre $min y $max.", $campo);
				return FALSE;
			}
			return TRUE;
		}
		
		/**
		 * Comprueba si el $valor cumple unas condiciones, y en caso contrario crea y almacena un objeto 'Error' en el array
		 * 'errores' con el �ndice asociativo '$campo'['requerido']
		 * @param string $campo -> nombre del campo
		 * @param string $valor -> valor del campo
		 */
		public function validaDni($campo,$valor) {
			if(preg_match('/[0-9]{7,8}[A-Za-z]/', $valor)!=1) {
				$this->errores[$campo]["dni_incorrecto"]=new error(" El campo '$campo' no es un DNI v�lido.", $campo);
				return  FALSE;
			}
			return TRUE;
		}
		
		/**
		 * Devuelve el array de errores almacenados en la colecci�n.
		 */
		public function getErrores() {
			return $this->errores;
		}
	}
?>