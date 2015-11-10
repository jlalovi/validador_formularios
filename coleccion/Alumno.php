<?php

class Alumno {
	
	// DECLARACIÓN VARIABLES
	
	/**
	 * @validaDni
	 * @requerido
	 */
	private $dni;
	/**
	 * @requerido
	 */
	private $nombre;
	/**
	 * @rango=18,60¬
	 * @requerido
	 */
	private $edad;

	// CONSTRUCTOR Y MÉTODOS
	
	/**
	 * Constructor de la clase Persona
	 * @param string $dni
	 * @param string $nombre
	 * @param number $edad
	 */
	public function Persona($dni="0000", $nombre="", $edad=0) { // Como no se puede hacer sobrecarga de las funciones, paso todos los parámetros por defecto.
		$this->setDni($dni); // Es una buena práctica inicializar los valores con los setters.
		$this->setNombre($nombre);
		$this->setEdad($edad);
	}
	
	//Setters y getters
	public function setDni($dni) {
		$this->dni=$dni;
	}
	public function getDni() {
		return $this->dni;
	}
	public function setNombre($nombre) {
		$this->nombre=$nombre;
	}
	public function getNombre() {
		return $this->nombre;
	}
	public function setEdad($edad) {
		$this->edad=$edad;
	}
	public function getEdad() {
		return $this->edad;
	}
	
	//métodos
	public function toString() {
		return "Soy la persona con DNI: $this->dni, Nombre: $this->nombre y Edad: $this->edad"; // Sin el '$this->variable' NO se reconocen las variables de la propia clase en PHP!!!
	}

}