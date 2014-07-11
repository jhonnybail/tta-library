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
    TemTudoAqui\Generic,
	TemTudoAqui\SystemException,
	TemTudoAqui\InvalidArgumentException,
	TemTudoAqui\Utils\Net\NetException,
	TemTudoAqui\Utils\Net\URLRequestHeader,
	TemTudoAqui\Utils\Net\URLRequest,
	TemTudoAqui\Utils\Net\CURL,
	TemTudoAqui\Events\Event,
    TemTudoAqui\Events\IObjectEventManager,
    TemTudoAqui\Events\ObjectEventManager;

/**
 * Classe que trabalham com arquivos estruturados em DOM.
 */
class DOMFile extends ArrayObject implements
    IFileObject,
    IObjectEventManager
{

    use ObjectEventManager;

	/**
	 * Respons�vel para acessar as Annotations da classe.
     * @var \ReflectionClass
     */
	protected $reflectionClass;
	
	/**
	 * URL informada.
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
	 * Extens�o do arquivo.
     * @var string
     */
	protected $extension;
	
	/**
	 * Nome do arquivo.
     * @var string
     */
	protected $fileName;
	
	/**
	 * Inst�ncia do XML.
     * @var \SimpleXMLElement
     */
	private $xml;
	
	/**
	 * Texto dentro da tag, se houver.
     * @var string
     */
	private $content;
	
	/**
	 * Array com os atributos da tag.
     * @var \TemTudoAqui\Utils\Data\ArrayObject
     */
	private $attributes;
	
	/**
	 * Array com os filhos da tag.
     * @var \TemTudoAqui\Utils\Data\ArrayObject
     */
	private $childrens;
	
	/**
	 * Cabe�alho do tipo de Arquivo.
     * @var \TemTudoAqui\Utils\Data\String
     */
	protected $headString;
	
	/**
     * Constructor
     *
     * @param  \TemTudoAqui\Utils\Net\URLRequest|null	$urlRequest
     */
	public function __construct(URLRequest $urlRequest = null)
    {
		
		parent::__construct();
		
		$this->extension 	= new String();
		$this->fileName 	= new String();
		
		$this->reflectionClass = new \ReflectionClass(get_class($this));
		
		$this->attach(Event::INIT, function(Event $eve){
			$eve->getTarget()->url 		    = $eve->getTarget()->urlRequest->url;
			$info = pathinfo($eve->getTarget()->url);
			$eve->getTarget()->fileName 	= String::GetInstance($info['filename']);
			$eve->getTarget()->extension 	= String::GetInstance($info['extension']);
		});
		
		if(!empty($urlRequest)){
			$this->urlRequest = $urlRequest;
		}else
			$this->urlRequest = null;
			
		$this->attributes 			= new ArrayObject();
		$this->childrens 			= new ArrayObject();
		$this->childrens->nameSpace	= '-||-';
		
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
		
		if(!class_exists("SimpleXMLElement"))
			throw new InvalidArgumentException(20, $this->reflectionClass->getName(), 134, 'A classe SimpleXMLElement não está habilitada');
			
		if(empty($urlRequest)) $urlRequest = $this->urlRequest;
		
		if(!empty($urlRequest)){
			
			if($urlRequest->getType() == URLRequest::URLFILETYPE){
			
				$this->urlRequest = $urlRequest;
				
				if($this->urlRequest->requestHeaders[URLRequestHeader::CONTENTLENGTH] <= System::GetIni("memory_limit")){

                    $data = @file_get_contents($this->urlRequest->url);

					if(!empty($data)){
						$this->data = $data;
						$this->trigger(Event::LOAD);
					}
									
					if(empty($data)){
						$curl = new CURL($this->urlRequest);
						if($curl->load()){
							$this->data = $curl->content;
							$this->trigger(Event::LOAD);
						}
					}
				
				}else
					throw new SystemException(13, $this->reflectionClass->getName(), 134);
			
			}else
				throw new NetException(9, $this->reflectionClass->getName(), 134);
		
		}
		
		if(!empty($this->data)){
			
			try{
				$this->xml = @new \SimpleXMLElement($this->data);
			}catch(\Exception $e){
				if(!empty($this->headString)){
					try{
						$this->xml = @new \SimpleXMLElement($this->headString.$this->data);
					}catch(\Exception $e){
						throw new InvalidArgumentException(20, $this->reflectionClass->getName(), 134, 'Estrutura DOM não está correta, sendo assim, não é possível instanciar objeto SimpleXMLElement');
					}
				}else
					throw new InvalidArgumentException(20, $this->reflectionClass->getName(), 134, 'Estrutura DOM não está correta, sendo assim, não é possível instanciar objeto SimpleXMLElement');
			}
			
		}
		
		$this->commitData();
		
		//Atributos
		$this->createAttributesArray();
		//
		
		//Filhos
		//$this->createChildrensArray();
		//
		
		
	}

    /**
     * Retorna um node de acordo com a posição passada.
     *
     * @param  int	                            $index
     * @return \TemTudoAqui\Utils\Data\DOMFile
     */
	public function offsetGet($index)
    {
		return $this->xml[$index];
	}

    /**
     * Insere um novo valor para a node de acordo com a posição passada.
     *
     * @param  int	                            $index
     * @param  mixed	                        $newval
     * @return \TemTudoAqui\Utils\Data\DOMFile
     */
	public function offsetSet($index, $newval)
    {
		return $this->xml[$index] = $newval;
	}
	
	/**
     * Cria um objeto apartir de uma String em DOM válido.
     *
     * @param  string	$data
     * @return \TemTudoAqui\Utils\Data\DOMFile
     */
	public static function CreateDOMFileByString($data)
    {
		
		$data = (string) $data;
		
		$fileXML = new DOMFile;
		$fileXML->data = $data;
		$fileXML->open();
		
		return $fileXML;
			
	}
	
	/**
     * Cria um objeto apartir de um objeto SimpleXMLElement válido.
     *
     * @param  \SimpleXMLElement	            $data
     * @return \TemTudoAqui\Utils\Data\DOMFile
     */
	protected static function CreateDOMFileBySimpleXMLElement(\SimpleXMLElement $data)
    {
		
		$fileXML = new DOMFile;
		$fileXML->xml = $data;
		$fileXML->open();
		
		return $fileXML;
			
	}
	
	/**
     * Grava as informa��es passadas para o arquivo DOM.
     *
     * @return void
     */
	protected function commitData()
    {
		$this->data = $this->xml->asXML();
	}
	
	/**
     * Retorna o nome da tag.
     *
     * @throws  \TemTudoAqui\Utils\Net\NetException
     * @return  \TemTudoAqui\Utils\Data\String
     */
	public function getName()
    {
		if(($this->xml instanceof \SimpleXMLElement))
			return new String($this->xml->getName());
		else
			throw new NetException(15, $this->reflectionClass->getName(), 251);
	}
	
	/**
     * Retorna o nome da namespace.
     *
     * @throws  \TemTudoAqui\Utils\Net\NetException
     * @return  \TemTudoAqui\Utils\Data\String
     */
	public function getNamespace()
    {
		if(($this->xml instanceof \SimpleXMLElement)){
			$array = ArrayObject::GetInstance($this->xml->getNamespaces());
			if($array->count() > 0)
				return new String(@$array->getIterator()->current());
		}else
			throw new NetException(15, $this->reflectionClass->getName(), 263);
        return new String;
	}
	
	/**
     * Retorna uma lista com os namespaces usado no documento.
     *
     * @throws  \TemTudoAqui\Utils\Net\NetException
     * @return  \TemTudoAqui\Utils\Data\ArrayObject
     */
	public function getAllNamespaces()
    {
		if(!($this->xml instanceof \SimpleXMLElement))
			throw new NetException(15, $this->reflectionClass->getName(), 275);
			
		return new ArrayObject($this->xml->getNamespaces(true));
	}
	
	/**
     * Verifica se há nodes filhos com determinada Namespace.
     *
     * @param   string	$prefix
     * @throws  \TemTudoAqui\Utils\Net\NetException
     * @return  Boolean
     */
	public function hasChildrensNameSpace($prefix)
    {
		if(!($this->xml instanceof \SimpleXMLElement))
			throw new NetException(15, $this->reflectionClass->getName(), 275);
		
		if($prefix){
			$str = $this->getData();
			return $str->search("<".$prefix.":");
		}else
			return false;
		
	}
	
	/**
     * Retorna o numero de elementos filhos da tag.
     *
     * @throws  \TemTudoAqui\Utils\Net\NetException
     * @return  int
     */
	public function length()
    {
		if(!($this->xml instanceof \SimpleXMLElement))
			throw new NetException(15, $this->reflectionClass->getName(), 287);
		
		return $this->xml->count() ? $this->xml->count() : 0;
	}
	
	/**
     * Adiciona conteúdo em texto na tag.
     *
     * @param   string	$value
     * @throws  \TemTudoAqui\Utils\Net\NetException
     * @return  \TemTudoAqui\Utils\Data\DOMFile
     */
	 private function addContent($value)
     {
		if(!($this->xml instanceof \SimpleXMLElement))
			throw new NetException(15, $this->reflectionClass->getName(), 300);
	 	
		$this->content = $value;
		$this->xml->{0} = $this->content;
		$this->commitData();
		return $this;
	 }
	 
	/**
     * Adiciona um atributo na tag.
     *
     * @param   string	                                $name
     * @param   string	                                $value
     * @param   string|null                             $nameSpace
     * @throws  \TemTudoAqui\Utils\Net\NetException
     * @return  \TemTudoAqui\Utils\Data\DOMFile
     */
	public function addAttribute($name, $value, $nameSpace = null)
    {
		if(!($this->xml instanceof \SimpleXMLElement))
			throw new NetException(15, $this->reflectionClass->getName(), 318);
		
		$this->xml->addAttribute($name, $value, $nameSpace);
		$this->createAttributesArray();
		$this->commitData();
		return $this;
	}
	
	/**
     * Adiciona um filho a tag e retorna-o.
     *
     * @param   string	                                $name
     * @param   string	                                $value
     * @param   string|null                             $nameSpace
     * @throws  \TemTudoAqui\Utils\Net\NetException
     * @throws  \TemTudoAqui\InvalidArgumentException
     * @return  \TemTudoAqui\Utils\Data\DOMFile
     */
	public function addChild($name, $value = '', $nameSpace = null)
    {
		
		if(empty($name))
			throw new InvalidArgumentException(2, $this->reflectionClass->getName(), 336);
			
		if(!($this->xml instanceof \SimpleXMLElement))
			throw new NetException(15, $this->reflectionClass->getName(), 336);

		$this->xml->addChild($name, $value, $nameSpace);
		$this->createChildrensArray();
		$this->commitData();
		
		$arrayChilds 	= $this->getChildrens();
		$child 			= $arrayChilds[$name]->end();
		
		return $child;
		
	}
	
	/**
     * Adiciona um filho a tag de acordo com o índice e retorna-o.
     *
     * @param   string	                                $name
     * @param   string	                                $value
     * @param   string|null	                            $index
     * @param   string|null	                            $nameSpace
     * @throws  \TemTudoAqui\Utils\Net\NetException
     * @throws  \TemTudoAqui\InvalidArgumentException
     * @return  \TemTudoAqui\Utils\Data\DOMFile
     */
	public function addChildAt($name, $value = '', $index = null, $nameSpace = null)
    {
		
		if(empty($name))
			throw new InvalidArgumentException(2, $this->reflectionClass->getName(), 336);
			
		if(!($this->xml instanceof \SimpleXMLElement))
			throw new NetException(15, $this->reflectionClass->getName(), 336);
			
		$parent = dom_import_simplexml($this->xml);
		if($nameSpace)
			$child  = $parent->ownerDocument->createElementNS($nameSpace, $name, $value);
		else
	    	$child  = $parent->ownerDocument->createElement($name, $value);
	    	
	    $target = $parent->getElementsByTagname('*')->item($index);
	    if ($target === null) {
	        $parent->appendChild($child);
	    } else {
	        $parent->insertBefore($child, $target);
	    }
				
		$this->createChildrensArray();
		$this->commitData();
		
		$arrayChilds 	= $this->getChildrens();
		$child			= $arrayChilds[$name][$index];
		
		return $child;
		
	}
	
	/**
     * Remove um filho da tag e retorna-o.
     *
     * @param   \TemTudoAqui\Utils\Data\DOMFile	    $node
     * @throws  \TemTudoAqui\Utils\Net\NetException
     * @return  \TemTudoAqui\Utils\Data\DOMFile
     */
	public function removeChild(DOMFile $node)
    {
		
		if(!($this->xml instanceof \SimpleXMLElement))
			throw new NetException(15, $this->reflectionClass->getName(), 366);
		
		$dom = dom_import_simplexml($node->xml);
		$child = $dom->parentNode->removeChild($dom);
		
		$this->commitData();
		
		//Atributos
		$this->createAttributesArray();
		//
		
		//Filhos
		$this->createChildrensArray();
		//
		
		return $child;
		
	}
	
	/**
     * Remove um filho da tag de acordo com seu índice e retorna-o.
     *
     * @param   string	                                $name
     * @param   int 	                                $index
     * @param   string|null	                            $nameSpace
     * @throws  \TemTudoAqui\Utils\Net\NetException
     * @throws  \TemTudoAqui\InvalidArgumentException
     * @return  \TemTudoAqui\Utils\Data\DOMFile
     */
	public function removeChildAt($name, $index = 0, $nameSpace = null)
    {
		
		if(!($this->xml instanceof \SimpleXMLElement))
			throw new NetException(15, $this->reflectionClass->getName(), 366);
		
		$arrayChilds 	= $this->getChildrens($nameSpace);
		$index			= (int)(string) $index;
		
		if(!empty($arrayChilds[$name])){
			
			if($arrayChilds[$name]->count() > 0){
				if(!empty($arrayChilds[$name][$index])){
					$dom = dom_import_simplexml($arrayChilds[$name][$index]->xml);
					$child = $dom->parentNode->removeChild($dom);
				}else
					throw new InvalidArgumentException(3, $this->reflectionClass->getName(), 366, 'Indíce '.$index.' de nome '.$name.' não existe no arquivo');
			}else{
				$dom = dom_import_simplexml($arrayChilds[$name]->xml);
        		$child = $dom->parentNode->removeChild($dom);
			}
			
		}else
			throw new InvalidArgumentException(3, $this->reflectionClass->getName(), 366, $name.' não existe no arquivo');
		
		$this->commitData();
		
		//Atributos
		$this->createAttributesArray();
		//
		
		//Filhos
		$this->createChildrensArray();
		//
		
		return $child;
		
	}
	
	/**
     * Troca um node filho por outro.
     *
     * @param   \TemTudoAqui\Utils\Data\DOMFile	    $node
     * @param   \TemTudoAqui\Utils\Data\DOMFile	    $newNode
     * @throws  \TemTudoAqui\Utils\Net\NetException
     * @return  \TemTudoAqui\Utils\Data\DOMFile
     */
	public function replaceChild(DOMFile $node, DOMFile $newNode)
    {
		
		if(!($this->xml instanceof \SimpleXMLElement))
			throw new NetException(15, $this->reflectionClass->getName(), 366);
		
		$dom 	= dom_import_simplexml($node->xml);
		$newDom = dom_import_simplexml($newNode->xml);
		$newDom = $dom->ownerDocument->importNode($newDom, true);
		$dom->parentNode->replaceChild($newDom, $dom);
		
		$this->commitData();
		
		//Atributos
		$this->createAttributesArray();
		//
		
		//Filhos
		$this->createChildrensArray();
		//
		
		return $node;
		
	}
	
	/**
     * Retorna um Array dos atributos da tag.
     *
     * @param   string|null	                        $nameSpace
     * @throws  \TemTudoAqui\Utils\Net\NetException
     * @return  \TemTudoAqui\Utils\Data\ArrayObject
     */
	public function getAttributes($nameSpace = null)
    {
		
		if(!($this->xml instanceof \SimpleXMLElement))
			throw new NetException(15, $this->reflectionClass->getName(), 410);
		
		if($this->attributes->nameSpace != $nameSpace)
			$this->createAttributesArray($nameSpace);
		
		return $this->attributes->attributes;
		
	}
	
	/**
     * Cria um Array dos atributos da tag.
     *
     * @param  string|null	$nameSpace	Optional.
     * @return void
     */
	protected function createAttributesArray($nameSpace = null)
    {
		
		//if(empty($this->xml))
			//throw new NetException(15, $this->reflectionClass->getName(), 428);
		
		$this->attributes->attributes = new ArrayObject();
		$this->attributes->nameSpace = $nameSpace;
		foreach($this->xml->attributes($nameSpace) as $key => $value){
			$this->attributes->attributes->$key = $value;
			//$this[$key] = $value;
		}

	}
	
	/**
     * Retorna um Array dos filhos da tag.
     *
     * @param   string|null	                        $nameSpace
     * @throws  \TemTudoAqui\Utils\Net\NetException
     * @return  \TemTudoAqui\Utils\Data\ArrayObject
     */
	public function getChildrens($nameSpace = null)
    {
		
		if(!($this->xml instanceof \SimpleXMLElement))
			throw new NetException(15, $this->reflectionClass->getName(), 448);
		
		if($this->childrens->nameSpace != $nameSpace)
			$this->createChildrensArray($nameSpace);
		
		return $this->childrens->childrens;
		
	}
	
	/**
     * Cria um Array dos filhos da tag.
     *
     * @param  string|null	$nameSpace
     * @return void
     */
	private function createChildrensArray($nameSpace = null)
    {
		
		//if(empty($this->xml))
			//throw new NetException(15, $this->reflectionClass->getName(), 466);
		
		$this->childrens->childrens = new ArrayObject();
		$this->childrens->nameSpace = $nameSpace;
		
		foreach($this->xml->children($nameSpace) as $key => $value){
			
			$value = static::CreateDOMFileBySimpleXMLElement($value);
			
			if(!empty($this->childrens->childrens[$key])){

				//if(!($this->childrens->childrens->$key->count())){
					//$old = $this->childrens->childrens->$key;
					//$this->childrens->childrens->$key = new ArrayObject();
					//$this->childrens->childrens->$key->append($old);
				//}
				
				$this->childrens->childrens->$key->append($value);

			}else{
				$this->childrens->childrens->$key = new ArrayObject();	
				$this->childrens->childrens->$key->append($value);
				//$this->childrens->childrens->$key = $value;
			}	
		}
		
		
	}

    /**
     * Retorna propriedades protegidas do objeto.
     *
     * @param   string                              $property
     * @throws  \TemTudoAqui\Utils\Net\NetException
     * @return  mixed
     */
	public function __get($property)
    {

		if($property == 'content')
			return $this->content;
		elseif($property == 'xml'){
			
			if(!($this->xml instanceof \SimpleXMLElement))
				throw new NetException(15, $this->reflectionClass->getName(), 498);
			
			return $this->xml;
		}elseif(isset($this->xml->$property)){
			
			if(empty($this->xml))
				throw new NetException(15, $this->reflectionClass->getName(), 498);
			
			return static::CreateDOMFileBySimpleXMLElement($this->xml->$property);
		}else
			return $this->$property;
		
	}

    /**
     * Insere valor nas propriedades protegidas.
     *
     * @param   string                                  $property
     * @param   mixed                                   $value
     * @throws  \TemTudoAqui\InvalidArgumentException
     */
	public function __set($property, $value)
    {
		if($property == 'content')
			$this->addContent($value);
		elseif($property == 'xml'){			
			if(!($value instanceof \SimpleXMLElement))
				throw new InvalidArgumentException(19, $this->reflectionClass->getName(), 519, 'O Argumento não é um SimpleXMLElement válido');
			$this->xml = $value;
		}else
			$this->$property = $value;
	}

	/**
     * Implementa as modificações e obtém os dados do arquivo.
     *
     * @return \TemTudoAqui\Utils\Data\String
     */
	public function getData()
    {
		$this->commitData();
		return new String($this->data);
	}
	
	/**
     * Retorna a classe espelho da classe.
     *
     * @param   string           $class
     * @return  \ReflectionClass
     */
	public static function GetReflection($class)
    {
		$c = __CLASS__;
		return new \ReflectionClass(!empty($class) ? new $class : new $c);
	}
	
	/**
     * Compara o próprio objeto com o objeto passado por parâmetro.
     *
     * @param  \TemTudoAqui\Generic $obj
     * @return bool
     */
	public function equals(Generic $obj)
    {
		if($this === $obj) return true;
		else return false;
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
     * Usada para serialização do objeto.
     *
     * @return \TemTudoAqui\Utils\Data\ArrayObject
     */	
	public function __sleep()
    {
		return new ArrayObject(array('reflectionClass','urlRequest','events','attributes','childrens'));
	}
	
	/**
     * Retorna o conteúdo do arquivo.
     *
     * @return string
     */
	public function __toString()
    {
		return (string) $this->getData();
	}
	
	/**
     * Retorna um objeto String da função mágica __toString.
     *
     * @return \TemTudoAqui\Utils\Data\String
     */
	public function toString()
    {
		return new String($this);
	}
	
	/**
     * Chamado quando � destruido o objeto.
     *
     * @return void
     */
	public function __destruct()
    {
		parent::__destruct();
	}

}