<?php

/**
 * TemTudoAqui (http://library.temtudoaqui.info/)
 *
 * @package     TemTudoAqui\Utils\Net
 * @link        http://github.com/jhonnybail/tta-library para o repositório de origem
 * @copyright   Copyright (c) 2014 Tem Tudo Aqui. (http://www.temtudoaqui.info)
 * @license     http://license.temtudoaqui.info
 */
namespace TemTudoAqui\Utils\Net;

use TemTudoAqui\Utils\Data\ArrayObject,
    TemTudoAqui\Generic;

/**
 * Encarregada de criar variáveis para enviar qualquer pedido HTTP.
 */
class URLVariables extends Generic
{
	
	const TEXT = 'text';
	const FILE = 'file';
	
	/**
	 * Lista das vari�veis do objeto.
     * @var \TemTudoAqui\Utils\Data\ArrayObject
     */
	private	$data;
	
	/**
     * Construtor
     */
	public function __construct()
    {
		parent::__construct();
		$this->data = new ArrayObject();
	}
	
	/**
     * Adiciona uma variável.
     *
     * @param  string	$name
     * @param  string	$value
     * @param  string	$type
     * @return void
     */
	public function addVariable($name, $value, $type = self::TEXT)
    {
		if($type == self::TEXT)
			$this->data[$name] = $value;
		elseif($type == self::FILE)
			$this->data[$name] = '@'.$value;
	}
	
	/**
     * Retorna o total de variáveis adicionadas.
     *
     * @return int
     */
	public function length()
    {
		return $this->data->length();
	}
	
	/**
     * Retorna um ArrayObject conteúdo as variáveis inseridas no objeto.
     *
     * @return \TemTudoAqui\Utils\Data\ArrayObject
     */
	public function toArrayObject()
    {
		return $this->data;
	}
	
	/**
     * Retorna o total da variável solicidata.
     *
     * @param  string	$property
     * @return mixed
     */
	public function __get($property)
    {
		return $this->data[$property];
	}

    /**
     * Usada para serialização do objeto.
     *
     * @return \TemTudoAqui\Utils\Data\ArrayObject
     */
	public function __sleep()
    {
		return parent::__sleep()->concat(array('data'));
	}
	
	/**
     * Retorna o nome das variáveis e seus valores.
     *
     * @return string
     */
	public function __toString()
    {
		return (string) $this->data->toString()->replace(": ", '=')->replace("; ", "&");
	}
	
}
