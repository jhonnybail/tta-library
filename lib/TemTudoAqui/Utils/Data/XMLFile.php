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

use TemTudoAqui\Events\Event,
	TemTudoAqui\Utils\Net\URLRequest;

/**
 * Classe usada para trabalhar com XML.
 */
final class XMLFile extends DOMFile
{

	public function __construct(URLRequest $urlRequest = null)
    {
		$this->headString = '<?xml version="1.0"?>';
		parent::__construct($urlRequest);
		
		$this->attach(Event::LOAD, function(Event $eve){
			$eve->getTarget()->loadXML($eve->getTarget()->data);
		});
		
	}

	public static function CreateDOMFileByString($data)
    {
		
		$data = (string) $data;
		
		$fileXML = new XMLFile;
		$fileXML->data = $data;
		$fileXML->open();
		
		return $fileXML;
			
	}
	
	protected static function CreateDOMFileBySimpleXMLElement(\SimpleXMLElement $data)
    {
		
		$fileXML = new XMLFile;
		$fileXML->xml = $data;
		$fileXML->open();
		
		return $fileXML;
			
	}

	/**
     * Retorna em formato de string.
     *
     * @return string
     */
	public function __toString()
    {
		return (string) $this->toString();
	}
	

	/**
     * Retorna em formato de string.
     *
     * @return \TemTudoAqui\Utils\Data\String
     */
	public function toString()
    {
		return new String($this->getData());
	}
		
}