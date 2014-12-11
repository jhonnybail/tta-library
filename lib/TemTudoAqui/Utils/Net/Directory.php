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

use TemTudoAqui\Generic,
    TemTudoAqui\Utils\Data\File,
	TemTudoAqui\Utils\Data\HTMLFile,
	TemTudoAqui\Utils\Data\XMLFile,
	TemTudoAqui\Utils\Data\ImageFile,
	TemTudoAqui\Utils\Data\String;

/**
 *Classe que mantém diretórios.
 */
class Directory extends Generic
{
	
	/**
	 * URLRequest informada.
     * @var \TemTUdoAqui\Utils\Net\URLRequest
     */
	protected $urlRequest;
	
	/**
	 * URL informada pela URLRequest.
     * @var string
     */
	protected $url;
	
	/**
	 * Ponteiro para o diretório informado.
     * @var Dir
     */
	protected $dir;
	
	/**
     * Construtor
     *
     * @param   \TemTudoAqui\Utils\Net\URLRequest	$rootPath
     * @throws  \TemTudoAqui\Utils\Net\NetException
     */
	public function __construct(URLRequest $rootPath)
    {
		
		parent::__construct();
		
		if(URLRequest::URLDIRECTORYTYPE == $rootPath->getType()){
			$this->urlRequest 	= $rootPath;
			$this->url 			= $rootPath->url;
			$this->dir			= dir($this->url);
		}else
			throw new NetException(12, $this->reflectionClass->getName(), 45);
			
			
	}
	
	/**
     * Atualisa o diretório.
     *
     * @return void
     */
	public function refresh()
    {
		$this->dir = dir($this->url);
	}
	
	/**
     * Destrói o objeto.
     *
     * @return void
     */
	public function __destruct()
    {
		
	}

	/**
     * Retorna o caminho do diret�rio atual.
     *
     * @return \TemTudoAqui\Utils\Net\URLRequest|bool
     */
	public function getPath()
    {
		
		if(isset($this->urlRequest))
			$this->urlRequest;
		
		return false;
		
	}
	
	/**
     * Abre e percorre o diretório.
     *
     * @return \TemTudoAqui\Generic|null
     */
	public function read()
    {
		
		while($d = $this->dir->read()){
				
			if($d != '.' && $d != '..'){

				$pathD = $this->url.$d;
					
				$urlR 	= new URLRequest($pathD);
				
				if($urlR->getType() == URLRequest::URLFILETYPE){
					
					$file = new File($urlR);
					
					if($urlR->extension == 'html' || $urlR->extension == 'htm' || $urlR->extension == 'xhtml')
						return new HTMLFile($urlR);
					elseif($urlR->extension == 'xml')
						return new XMLFile($urlR);
					elseif($file->extension->toLowerCase() == ImageFile::IMAGETYPEJPEG || $file->extension->toLowerCase() == ImageFile::IMAGETYPEJPG || $file->extension->toLowerCase() == ImageFile::IMAGETYPEPNG || $file->extension->toLowerCase() == ImageFile::IMAGETYPEGIF)
						return new ImageFile($urlR);
					else
						return $file;
						
				}elseif($urlR->getType() == URLRequest::URLDIRECTORYTYPE)
					return new Directory($urlR);
				
			}

		}

		return null;

	}
	
	/**
     * Executa uma função de teste em cada item do diretório, se extendendo aos diretórios filhos.
     *
     * @param  callable	    $function
     * @param  mixed|null	$thisObject
     * @return void
     */
	public function forEachRecursive(callable $function, $thisObject = null)
    {
		while($o = $this->read()){
			$function($o, $this, $thisObject);
			
			if($o->urlRequest->getType() == URLRequest::URLDIRECTORYTYPE)
				$o->forEachRecursive($function, $thisObject);
			
		}
	}
	
	/**
     * Usada para serialização do objeto.
     *
     * @return \TemTudoAqui\Utils\Data\ArrayObject
     */
	public function __sleep()
    {
		return parent::__sleep()->concat(array('urlRequest', 'url', 'dir'));
	}
	
	/**
     * Retorna o nome do diretório.
     *
     * @return \TemTudoAqui\Utils\Data\String
     */
	public function toString()
    {
		return new String($this);
	}
	
	/**
     * Retorna o nome do diretório.
     *
     * @return string
     */
	public function __toString()
    {
		return (string) $this->urlRequest->url;
	}

}