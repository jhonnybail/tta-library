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

use TemTudoAqui\Utils\Net\URLRequest;

/**
 * InterFace para implementação de classes que abstraem arquivos.
 */
interface IFileObject
{
	
	/**
     * Constructor
     */
	public function __construct();
	
	/**
     * Abre o arquivo.
     *
     * @param  \TemTudoAqui\Utils\Net\URLRequest|null	$urlRequest
     * @return void
     */
	public function open(URLRequest $urlRequest = null);
	
	/**
     * Retorna os dados do objeto.
     *
     * @return string
     */
	public function getData();

	
}