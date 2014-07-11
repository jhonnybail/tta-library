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

use TemTudoAqui\System,
    TemTudoAqui\Generic,
    TemTudoAqui\Events\ObjectEventManager,
	TemTudoAqui\Utils\Data\ArrayObject,
	TemTudoAqui\Utils\Data\String,
	TemTudoAqui\InvalidArgumentException;

/**
 * Captura todas as informações de uma requisição HTTP.
 */
class URLRequest extends Generic
{

    use ObjectEventManager;
	
	const URLFILETYPE = 'file';
	const URLDIRECTORYTYPE = 'dir';
	const URLFIFOTYPE = 'fifo';
	const URLBLOCKTYPE = 'block';
	const URLCHARTYPE = 'char';
	const URLLINKTYPE = 'link';
	const URLSOCKETTYPE = 'socket';
	const URLUNKNOWNTYPE = 'unknown';

	/**
	 * O tipo do dado requerido.
     * @var string
     */
	protected $method;
	
	/**
	 * Um objeto que contém dados a serem transmitidos com a solicita��o de URL.
     * @var \TemTudoAqui\Utils\Net\URLVariables
     */
	protected $data;
	
	/**
	 * Lista de cabeçalhos para a requisição.
     * @var \TemTudoAqui\Utils\Data\ArrayObject
     */
	protected $requestHeaders;
	
	/**
	 * URL da requisião.
     * @var string
     */
	protected $url;
	
	/**
	 * Lista de possíveis arquivos.
     * @var \TemTudoAqui\Utils\Data\ArrayObject
     */
	protected $listFile;
	
	/**
     * Constructor
     *
     * @param   string	$url
     * @throws  \TemTudoAqui\InvalidArgumentException
     * @throws  \TemTudoAqui\Utils\Net\NetException
     */
	public function __construct($url)
    {
		
		parent::__construct();
		
		$this->listFile = new ArrayObject(array('jpg', 'jpeg', 'gif', 'png', 'bmp', 'txt', 'wav', 'mp3', 'wma', 'midi', 'pdf', 'php', 'html', 'js', 'json', 'xls', 'xlsx', 'doc', 'docx', 'otf', 'sql', 'ppt', 'pptx', 'psd', 'ai', 'cdr', 'ttf', 'wmv', 'avi', 'mpg', 'mpeg', 'mov', 'mkv', 'rmvb', 'swf', 'swc', 'fla', 'as', 'rar', 'zip', '7z'));
		
		if(empty($url))
			throw new InvalidArgumentException(2, $this->reflectionClass->getName(), 67);
		
		$this->method 			= URLRequestMethod::GET;
		$this->requestHeaders 	= URLRequestHeader::GetHeader();
		$this->url 				= new String($url);
		$this->data 			= null;
        $headers                = $this->getHeaders();
		if(!$headers){
			if(!($this->getLocalFileHeaders()))
				throw new NetException(6, $this->reflectionClass->getName(), 67, "URL não existe: ".$url);
		}else
			$this->requestHeaders = $this->requestHeaders->concat((array) $headers);
					
	}

    /**
     * Insere valor nas propriedades protegidas.
     *
     * @param   string   $property
     * @param   mixed    $value
     * @throws  \TemTudoAqui\InvalidArgumentException
     */
	public function __set($property, $value)
    {
		
		if($property == 'data'){
			if(!($value instanceof URLVariables))
				throw new InvalidArgumentException(5, __CLASS__, 89);
			else
				$this->data = $value;
		}else
			parent::__set($property, $value);
		
	}
	
	/**
     * Retorna o cabeçalho da url requerida.
     *
     * @return \TemTudoAqui\Utils\Data\ArrayObject|false
     */
	private function getHeaders()
    {
		
		$headers = new ArrayObject((array)@get_headers($this->url, 1));
		if($headers->length() > 0){
			if(preg_match('/^HTTP\/\d\.\d\s+(200|301|302)/', $headers[0]))
				return $headers; 
			else 
				return false;
		}else
			return false;
		
	}
	
	/**
     * Carrega o cabeçalho da url local requerida.
     *
     * @return bool
     */
	private function getLocalFileHeaders()
    {
    	
		if(!file_exists($this->url))
			return false;
		
		if(System::GetVariable('SERVER_PROTOCOL')->search("HTTP"))
			$protocol = 'http';
		elseif(System::GetVariable('SERVER_PROTOCOL')->search("HTTPS"))
			$protocol = 'https';
        else
            $protocol = System::GetVariable('SERVER_PROTOCOL');
			
		$url 		= $this->url;
		$newURL		= $url->replace(System::GetVariable('DOCUMENT_ROOT'), $protocol."://".System::GetVariable('HTTP_HOST'));
		$this->url	= $newURL;
		$this->requestHeaders = $this->requestHeaders->concat((array) $this->getHeaders());
		$this->url	= $url;

		$this->requestHeaders->offsetSet(URLRequestHeader::CONTENTLENGTH, filesize($this->url));
		
		return true;

	}
	
	/**
     * Retorna o caminho url no protocólo http.
     *
     * @return \TemTudoAqui\Utils\Data\String
     */
	public function getHTTPUrl()
    {
		$url = new String($this->url);
		return $url->replace(System::GetVariable('directory_root'), System::GetVariable('protocol')."://".System::GetVariable('host'));
	}
	
	/**
     * Verifica e retorna o tipo do retorno da requisição.
     *
     * @return string
     */
	public function getType()
    {
		
		$type = @filetype($this->url);
		
		$verifyExtension = function($value, $key, $array, $oB){
            if(!empty($key) && count($array) > 0){
                if($oB->url->search('\.'.$value))
                    return false;
                if(String::GetInstance($oB->requestHeaders['Content-Type'][1])->search($value))
                    return false;
            }
            return true;
		};
		
		if(!empty($type))
			return $type;
		elseif(is_file($this->url) || !$this->listFile->every($verifyExtension, $this))
			return self::URLFILETYPE;
		elseif(is_dir($this->url))
			return self::URLDIRECTORYTYPE;
		elseif(is_link($this->url))
			return self::URLLINKTYPE;
		else
			return self::URLUNKNOWNTYPE;
		
	}
	
	/**
     * Retorna o caminho da pasta do arquivo ou pasta atual.
     *
     * @return \TemTudoAqui\Utils\Net\URLRequest
     */
	public function directoryPath()
    {
		return new URLRequest(dirname($this->url));
	}

	/**
     * Retorna o nome do arquivo ou pasta atual.
     *
     * @return string
     */
	public function baseName()
    {
		return basename($this->url);
	}

    /**
     * Função mágica para serialização do objeto.
     *
     * @return \TemTudoAqui\Utils\Data\ArrayObject
     */
	public function __sleep()
    {
		return parent::__sleep()->concat(array('method', 'data', 'requestHeaders', 'url'));
	}

    /**
     * Retorna a url do arquivo.
     *
     * @return \TemTudoAqui\Utils\Data\String
     */
    public function toString()
    {
        return new String($this);
    }

    /**
     * Retorna a url do arquivo.
     *
     * @param  null
     * @return string
     */
	public function __toString()
    {
		return (string) $this->url;
	}
	
}

?>