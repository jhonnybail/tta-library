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

use TemTudoAqui\InvalidArgumentException,
    TemTudoAqui\Generic,
    TemTudoAqui\Events\Event,
    TemTudoAqui\Events\IObjectEventManager,
    TemTudoAqui\Events\ObjectEventManager;

/**
 * Classe para requisições POST/GET.
 */
class CURL extends Generic implements IObjectEventManager
{

    use ObjectEventManager;

	/**
	 * Objeto URLRequest informada.
     * @var \TemTudoAqui\Utils\Net\URLRequest
     */
	private     $urlRequest;

	/**
	 * User-Agent que está gerando a requesição.
     * @var string
     */
	private     $userAgent;

	/**
	 * O servidor HTTP proxy pelo qual passar as requisições.
     * @var string
     */
	private     $proxy;
	
	/**
	 * Conte�do retornado pela requisição.
     * @var string
     */
	protected   $content;

	/**
     * Construtor
     *
     * @param   \TemTudoAqui\Utils\Net\URLRequest	$urlRequest
     * @throws  \TemTudoAqui\InvalidArgumentException
     */
	public function __construct(URLRequest $urlRequest)
    {
		
		if(!function_exists("curl_init"))
			throw new InvalidArgumentException(4, __CLASS__, 50);
		
		parent::__construct();
		
		$this->urlRequest = $urlRequest;
		
		if(empty($this->urlRequest->requestHeaders["Accept"]))
			$this->urlRequest->requestHeaders["Accept"] = "image/gif, image/x-bitmap, image/jpeg, image/pjpeg";
		
		if(empty($this->urlRequest->requestHeaders["Connection"]))
			$this->urlRequest->requestHeaders["Connection"] = "Keep-Alive";

		if(empty($this->urlRequest->requestHeaders["Content-type"]))
			$this->urlRequest->requestHeaders["Content-type"] = "application/x-www-form-urlencoded";

		$this->userAgent = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)";
	
		$this->proxy = '';
	
	}
		
	/**
     * Envia a requisiçõo e carrega resultado
     *
     * @return bool
     */
	public function load()
    {

		$this->trigger(Event::LOAD);
			
		if($this->urlRequest->method == URLRequestMethod::GET){
				
			if($this->urlRequest->data != '')
				$process = \curl_init($this->urlRequest->url."?".$this->urlRequest->data);
			else
				$process = \curl_init($this->urlRequest->url);
					
			//\curl_setopt($process, CURLOPT_REFERER, $refer);
			\curl_setopt($process, CURLOPT_HTTPHEADER, $this->urlRequest->requestHeaders);
			\curl_setopt($process, CURLOPT_USERAGENT, $this->userAgent);
				
			//\curl_setopt($process,CURLOPT_ENCODING , $this->compression);
			\curl_setopt($process, CURLOPT_TIMEOUT, 30);
				
			if(!empty($this->proxy))
				\curl_setopt($process, CURLOPT_PROXY, 'proxy_ip:proxy_port');
				
			\curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
			$return = \curl_exec($process);
			\curl_close($process);
			$this->trigger(Event::COMPLETE);
			$this->content = $return;
				
		}elseif($this->urlRequest->method == URLRequestMethod::POST){
				
			$process = \curl_init();
			\curl_setopt($process, CURLOPT_URL, $this->urlRequest->url);
			\curl_setopt($process, CURLOPT_POST, true);
			\curl_setopt($process, CURLOPT_POSTFIELDS, $this->urlRequest->data->toArrayObject());
			\curl_setopt($process, CURLOPT_FOLLOWLOCATION  ,true);
			\curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
			//\curl_setopt($process, CURLOPT_HTTPHEADER, $this->urlRequest->requestHeaders);
			\curl_setopt($process, CURLOPT_TIMEOUT, 30);
				
			//\curl_setopt($process, CURLOPT_REFERER, $refer);
			\curl_setopt($process, CURLOPT_USERAGENT, $this->userAgent);
			//\curl_setopt($process, CURLOPT_FOLLOWLOCATION, true);
			//\curl_setopt($process, CURLOPT_ENCODING , $this->compression);
				
			if(!empty($this->proxy))
				\curl_setopt($process, CURLOPT_PROXY, 'proxy_ip:proxy_port');
	
			$return = \curl_exec($process);
			\curl_close($process);
			$this->trigger(Event::COMPLETE);
			$this->content = $return;
			
		}
		
		if(!empty($this->content))
			return true;
		else
			return false;
		
	}
	
	/**
     * Usada para serializa��o do objeto.
     *
     * @return \TemTudoAqui\Utils\Data\ArrayObject
     */
	public function __sleep(){
		return parent::__sleep()->concat(array('urlRequest', 'userAgent', 'proxy', 'content'));
	}

}