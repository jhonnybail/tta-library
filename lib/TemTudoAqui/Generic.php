<?php

/**
 * TemTudoAqui (http://library.temtudoaqui.info/)
 *
 * @package     TemTudoAqui
 * @link        http://github.com/jhonnybail/tta-library para o repositório de origem
 * @copyright   Copyright (c) 2014 Tem Tudo Aqui. (http://www.temtudoaqui.info)
 * @license     http://license.temtudoaqui.info
 */
namespace TemTudoAqui;

use TemTudoAqui\Utils\Data\ArrayObject,
	TemTudoAqui\Utils\Data\String;

/**
 * Classe Abstrata com as caracteristicas e métodos genéricos para qualquer classe do FrameWork.
 */
abstract class Generic
{
	
	/**
     * Classe de reflexão.
     * @var \ReflectionClass
     */
	protected $reflectionClass;
	
	/**
     * Constructor
     */
	public function __construct()
    {
		$this->reflectionClass = new \ReflectionClass(get_class($this));
	}
	
	/**
     * Retorna o espelho da classe.
     *
     * @param  string $class
     * @return \ReflectionClass
     */
	public static function GetReflection($class = null)
    {
		$c = get_called_class();
		return new \ReflectionClass(!empty($class) ? $class : $c);
	}
	
	/**
     * Compara o próprio objeto com o objeto passado por parâmetro.
     *
     * @param  \TemTudoAqui\Generic  $obj
     * @return bool
     */
	public function equals(Generic $obj)
    {
		if($this === $obj) return true;
		else return false;
	}

    /**
     * Retorna propriedades protegidas do objeto.
     *
     * @param  string  $property
     * @return mixed
     */
	public function __get($property)
    {
		return @$this->$property;
	}

    /**
     * Insere valor nas propriedades protegidas.
     *
     * @param  string   $property
     * @param  mixed    $value
     */
	public function __set($property, $value)
    {
		if(is_int($value))
			$this->$property = $value;
		elseif(is_float($value))
			$this->$property = $value;
		elseif(is_string($value) && !empty($value))
			$this->$property = new String($value);
		else
			$this->$property = $value;	
	}

    /**
     * Dispara métodos protegidos do objeto.
     *
     * @param  string   $name
     * @param  array    $arguments
     * @return void
     */
	public function __call($name, $arguments)
    {

    }

    /**
     * Dispara métodos estáticos protegidos do objeto.
     *
     * @param  string   $name
     * @param  array    $arguments
     * @return void
     */
	public static function __callStatic($name, $arguments)
    {

    }
	
	/**
     * Retorna o nome da classe.
     *
     * @param  null
     * @return string
     */
	public function __toString()
    {
		return $this->reflectionClass->getName();
	}

	/**
     * Retorna o nome da classe.
     *
     * @return \TemTudoAqui\Utils\Data\String
     */
	public function toString()
    {
		return new String($this->reflectionClass->getName());
	}

    /**
     * Função mágica para serialização do objeto.
     *
     * @return \TemTudoAqui\Utils\Data\ArrayObject
     */
    public function __sleep(){
        return new ArrayObject();
    }

	/**
     * Chamado quando é destruido o objeto.
     *
     * @return void
     */
	public function __destruct()
    {

    }
	
}