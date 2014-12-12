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

use TemTudoAqui\System,
    TemTudoAqui\Object,
	TemTudoAqui\SystemException,
	TemTudoAqui\Utils\Net\NetException,
	TemTudoAqui\Utils\Net\URLRequestHeader,
	TemTudoAqui\Utils\Net\URLRequest,
	TemTudoAqui\Utils\Net\CURL,
	TemTudoAqui\Events\Event,
    TemTudoAqui\Events\IObjectEventManager,
    TemTudoAqui\Events\ObjectEventManager;

/**
 * Classe que mantém arquivos de qualquer tipo.
 */
class File extends Object implements
    IFileObject,
    IObjectEventManager
{

    use ObjectEventManager;

	/**
	 * URLRequest informada.
     * @var \TemTudoAqui\Utils\Net\URLRequest
     */
	protected $urlRequest;

	/**
	 * Dados do arquivo em formato de string.
     * @var string
     */
	protected $data;
	
	/**
	 * URL informada pela URLRequest.
     * @var string
     */
	protected $url;
	
	/**
	 * Extensão do arquivo.
     * @var string
     */
	protected $extension;
	
	/**
	 * Nome do arquivo.
     * @var string
     */
	protected $fileName;
		
	/**
	 * Verificador se o arquivo está aberto ou não.
     * @var string
     */
	protected $isOpen = false;
	
	/**
     * Constructor
     *
     * @param  \TemTudoAqui\Utils\Net\URLRequest	$urlRequest
     */
	public function __construct(URLRequest $urlRequest = null)
    {
		
		parent::__construct();
		
		$this->extension 	= new String();
		$this->fileName 	= new String();
		$this->data			= null;

		$this->attach(Event::LOAD, function(Event $eve){
            $eve->getTarget()->url 		    = $eve->getTarget()->urlRequest->url;
            $name                           = explode(".", basename($this->urlRequest->url));
			$eve->getTarget()->fileName 	= new String(str_replace(".".$name[count($name)-1], "", basename($this->urlRequest->url)));
			$eve->getTarget()->extension 	= new String($name[count($name)-1]);
		});
		
		if(!empty($urlRequest)){
			$this->urlRequest = $urlRequest;
			$this->trigger(Event::LOAD);
		}else
			$this->urlRequest = null;
		
	}

    /**
     * Retorna propriedades protegidas do objeto.
     *
     * @param   string                              $property
     * @return   mixed
     */
	public function __get($property)
    {
		
		if($property == 'name')
			return $this->fileName.'.'.$this->extension;
		if($property == 'size')
			return $this->urlRequest->requestHeaders[URLRequestHeader::CONTENTLENGTH];
			
		return parent::__get($property);
		
	}

    /**
     * Insere valor nas propriedades protegidas.
     *
     * @param   string                                  $property
     * @param   mixed                                   $value
     */
	public function __set($property, $value)
    {
		
		if($property != 'size')
		    parent::__set($property, $value);
			
	}

    /**
     * Abre o arquivo.
     *
     * @param   \TemTudoAqui\Utils\Net\URLRequest|null	$urlRequest
     * @throws  \TemTudoAqui\InvalidArgumentException
     * @throws  \TemTudoAqui\Utils\Net\NetException
     * @throws  \TemTudoAqui\SystemException
     * @return  void
     */
	public function open(URLRequest $urlRequest = null)
    {
		
		if(empty($urlRequest)) $urlRequest = $this->urlRequest;
		
		if(!empty($urlRequest)){
			
			if(!$this->isOpen){
				
				if($urlRequest->getType() == URLRequest::URLFILETYPE){
					
					$this->urlRequest = $urlRequest;
					
					if($this->urlRequest->requestHeaders[URLRequestHeader::CONTENTLENGTH] <= System::GetIni("memory_limit")){
							
						$data = $data = @file_get_contents($this->urlRequest->url);
						
						if(!empty($data)){
							$this->data = $data;
							$this->isOpen = true;
							$this->trigger(Event::LOAD);
						}
											
						if(empty($data)){
							$curl = new CURL($this->urlRequest);
							if($curl->load()){
								$this->isOpen = true;
								$this->data = $curl->content;
								$this->trigger(Event::LOAD);
							}
						}
						
					}else
						throw new SystemException(13, $this->reflectionClass->getName(), 105);
				
				}else
					throw new NetException(9, $this->reflectionClass->getName(), 105);
				
			}
		
		}
		
	}
	
	/**
     * Cria um Objeto apartir de um caminho temporário.
     *
     * @param  mixed	$temp
     * @return \TemTudoAqui\Utils\Data\File
     */
	public static function OpenFileByTEMP($temp)
    {
		  
	 	$urlR = new URLRequest($temp['tmp_name']);
		$file = new File($urlR);
		
		$ext = explode('.', $temp['name']);
	    $file->extension = $ext[count($ext)-1];
		  
		$file->fileName = '';
		for($i = 0; $i < count($ext)-1; $i++) $file->fileName .= $ext[$i];
		  
		$file->fileName = new String(preg_replace('! !', '_', preg_replace('!,!', '', $file->fileName)));
		$file->url = preg_replace('! !', '_', preg_replace('!,!', '', $file->url));
		 
		return $file;
		  
	}
	 
	/**
     * Cria um arquivo apartir de dados em formato de string.
     *
     * @param  string	                    $str
     * @return \TemTudoAqui\Utils\Data\File
     */
	public static function CreateFileByString($str)
    {
		$file = new File;
		$file->data = new String((string) $str);
		return $file;
	}

    /**
     * Implementa as modificações e obtém os dados do arquivo.
     *
     * @return \TemTudoAqui\Utils\Data\String
     */
	public function getData()
    {
		return new String((string) $this->data);
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
     * Usada para serialização do objeto.
     *
     * @return \TemTudoAqui\Utils\Data\ArrayObject
     */
	public function __sleep()
    {
		return parent::__sleep()->concat(array('urlRequest', 'url'));
	}

    /**
     * Retorna o conteúdo do arquivo.
     *
     * @return string
     */
	public function __toString()
    {
		return !is_null($this->urlRequest) ? (string) $this->urlRequest->url : (string) "";
	}
	
}