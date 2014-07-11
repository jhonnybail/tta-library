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
	TemTudoAqui\Utils\Data\String,
	Zend\Session\Container;
	
/**
 *  Trata e resgata variáveis do sistema.
 */
class System extends Object
{
	
	/**
     * Array com as variáveis do sistema
     * @var \Zend\Session\Container
     */
	private static	$variablesSystem;
	
	/**
     * Constructor
     */
	public function __construct()
    {
		parent::__construct();
	}
	
	/**
     * Registra variáveis do sistema.
     *
     * @param  array	$variables
     * @return void
     */
	public static function SetSystem(array $variables)
    {
		
		if(empty(self::$variablesSystem))
			self::$variablesSystem = new Container("system");
		
		foreach($variables as $k => $v)
			self::$variablesSystem->offsetSet($k, $v);
		
	}
	
	/**
     * Retorna a variável do sistema armazenada.
     *
     * @param  string	                        $data
     * @return \TemTudoAqui\Utils\Data\String
     */
	public static function GetVariable($data)
    {
		$value = new String;
        if(self::$variablesSystem->offsetGet((string) $data) != "")
			$value = String::GetInstance(self::$variablesSystem->offsetGet((string) $data));
		elseif(!empty($_SERVER[(string) $data]))
            $value = String::GetInstance($_SERVER[(string) $data]);
        return $value;
	}
	
	/**
     * Retorna valor da variável de requisição POST passada por parâmetro.
     *
     * @param  string	                        $varName
     * @return \TemTudoAqui\Utils\Data\String
     */
	public function requestPost($varName)
    {
		return new String($_POST[$varName]);
	}
	
	/**
     * Retorna o arquivo enviado por requisição FILES passada por parâmetro.
     *
     * @param  string	                            $varName
     * @return \TemTudoAqui\Utils\Data\ArrayObject
     */
	public function requestFile($varName)
    {
		return new ArrayObject($_FILES[$varName]);
	}
	
	/**
     * Retorna valor da variável de requesição GET passada por parâmetro.
     *
     * @param  string	                        $varName
     * @return \TemTudoAqui\Utils\Data\String
     */
	public function requestGet($varName)
    {
		return new String($_GET[$varName]);
	}
	
	/**
     * Retorna informação da variável do sistema contido no php.ini requerida por parâmetro.
     *
     * @param  string	                        $varName
     * @return \TemTudoAqui\Utils\Data\String
     */	
	public static function GetIni($varName)
    {

		if($varName == 'memory_limit'){
			if(String::GetInstance(ini_get($varName))->search("M"))
				return (float)String::GetInstance(ini_get($varName))->replace("M", "")->toString()*1048576;
			elseif(String::GetInstance(ini_get($varName))->search("K"))
				return (float)String::GetInstance(ini_get($varName))->replace("K", "")->toString()*1024;
			elseif(String::GetInstance(ini_get($varName))->search("G"))
				return (float)String::GetInstance(ini_get($varName))->replace("G", "")->toString()*1073741824;
		}
			
		return new String(ini_get($varName));
	
	}
	
}