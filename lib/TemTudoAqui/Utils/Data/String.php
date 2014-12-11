<?php

/**
 * TemTudoAqui (http://library.temtudoaqui.info/)
 *
 * @package     TemTudoAqui\Utils\Data
 * @link        http://github.com/jhonnybail/tta-library para o repositório de origem
 * @copyright   Copyright (c) 2014 Tem Tudo Aqui. (http://www.temtudoaqui.info)
 * @license     http://license.temtudoaqui.info
 */
namespace TemTudoAqui\Utils\Data;

/**
 * Classe substituta de String.
 */
class String
{
	
	/**
     * Guarda a string passada.
     * @var string
     */
	private $string;
	
	/**
     * Cria uma string com o valor passado.
     *
     * @param  string	$string
     */
	public function __construct($string = '')
    {
		$this->string = (string) $string;
	}
	
	/**
     * Retorna a quantidade de caracteres da string.
     *
     * @return int
     */
	public function length()
    {
		return strlen($this->string);
	}
	
	/**
     * Retorna o caracter da posição especificada.
     *
     * @param  int	    $pos
     * @return string
     */
	public function charAt($pos = 0)
    {
		$pos = (int)(string) $pos;
		return $this->string[$pos];
	}
	
	/**
     * Retorna o código Unicode do caracter da posição especificada.
     *
     * @param  int  $pos
     * @return int
     */
	public function charCodeAt($pos = 0)
    {
		$pos = (int)(string) $pos;
		return ord($this->string[$pos]);
	}
	
	/**
     * Concatena duas strings.
     *
     * @param  string	                        $string
     * @return \TemTudoAqui\Utils\Data\String
     */
	public function concat($string)
    {
		$this->string = $this->string.($string);
		return $this;
	}

    /**
     * Divide a string de acordo com um delimitador e retorna uma array.
     *
     * @param   string	                            $delimiter
     * @return  \TemTudoAqui\Utils\Data\ArrayObject
     */
    public function split($delimiter)
    {
        $array = new ArrayObject(explode($delimiter, $this->string));
        foreach($array as $k => $v)
            $array[$k] = new String($v);
        return $array;
    }

    /**
     * Extrai uma parte delimitada da string e retorna uma nova string.
     *
     * @param  int	                            $start
     * @param  int	                            $end
     * @return \TemTudoAqui\Utils\Data\String
     */
    public function slice($start, $end)
    {
        $start = (int)(string) $start;
        $end = (int)(string) $end;
        return new String(substr($this->string, $start, $end-$start+1));
    }
		
	/**
     * Retorna uma quantidade de caracteres em um nova string apartir do começo definido.
     *
     * @param  int	                            $start
     * @param  int	                            $length
     * @return \TemTudoAqui\Utils\Data\String
     */
	public function substr($start, $length)
    {
		$start = (int)(string) $start;
		$end = (int)(string) $length;
		return new String(substr($this->string, $start, $end));
	}
	
	/**
     * Substitui o parte espicificada pela a nova parte passada.
     *
     * @param   string	                        $pattern
     * @param   string	                        $replace
     * @param   string|null	                    $delimiter	Delimitador para an�lise da express�o regular
     * @return  \TemTudoAqui\Utils\Data\String
     */
	public function replace($pattern, $replace, $delimiter = "|")
    {
		return new String(preg_replace($delimiter.((string) $pattern).$delimiter, (string) $replace, $this->string));
	}
	
	/**
     * Procura se existe determinada parte passada por parametros na string.
     *
     * @param  string   $search
     * @param  string	$delimiter	Delimitador para an�lise da express�o regular
     * @return bool
     */
	public function search($search, $delimiter = "|")
    {
        if(preg_match($delimiter.((string) $search).$delimiter, $this->string))
			return true;
		else
			return false;
	}

	/**
     * Procura casos relacionada a expressão regular passada.
     *
     * @param   string	                            $pattern
     * @return  \TemTudoAqui\Utils\Data\ArrayObject
     */
	public function match($pattern)
    {
		
		preg_match_all((string) $pattern, $this->string, $array, PREG_SET_ORDER);
		
		return new ArrayObject($array);
		
	}
	
	/**
     * Retorna a string com os caracteres em maiúsculo.
     * @return \TemTudoAqui\Utils\Data\String
     */
	public function toUpperCase()
    {
		$this->string = strtoupper($this->string);
		return $this;
	}
	
	/**
     * Retorna a string com os caracteres em minúsculos.
     * @return  \TemTudoAqui\Utils\Data\String
     */
	public function toLowerCase()
    {
		$this->string = strtolower($this->string);
		return $this;
	}
	
	/**
     * Retorna a string com os primeiros caracteres das palavras em maiúsculo.
     * @return \TemTudoAqui\Utils\Data\String
     */
	public function toUpperCaseFirstChars()
    {
		$ex = explode(' ', strtolower($this->string));
		$this->string = '';
		foreach($ex as $v)
			if(strlen($v) > 3)
				$this->string .= ucfirst($v).' ';
			else
				$this->string .= $v.' ';
		$this->string = trim(ucfirst($this->string));
		return $this;
	}
	
	/**
     * Retorna verdadeiro se a string estiver vazia.
     *
     * @return bool
     */
	public function isEmpty()
    {
		return empty($this->string);
	}

    /**
     * Usada para serialização do objeto.
     *
     * @return \TemTudoAqui\Utils\Data\ArrayObject
     */
	public function __sleep()
    {
		return new ArrayObject(array('string'));
	}
	
	/**
     * Retorna a string guardada.
     * @return string
     */
	public function __toString()
    {
		return $this->string;
	}
	
	/**
     * Retorna a string guardada.
     * @return \TemTudoAqui\Utils\Data\String
     */
	public function toString()
    {
		return $this->__toString();
	}
	
	/**
     * Chamado quando é destruído o objeto.
     * @return void
     */
	public function __destruct()
    {
    }

    /**
     * Cria uma instancia estáticamente.
     *
     * @param  string                           $string
     * @return \TemTudoAqui\Utils\Data\String
     */
	public static function GetInstance($string)
    {
		return new String($string);
	}
	
}