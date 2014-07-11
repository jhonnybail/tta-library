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

use TemTudoAqui\Generic;

/**
 * Classe para armazenamento do método do requerimento.
 */
class URLRequestMethod extends Generic
{
	
	/**
     * @const string Constante definida para indicar metódo HTTP GET.
     */
	const GET = 'get';
	
	/**
     * @const string Constante definida para indicar metódo HTTP POST.
     */
	const POST = 'post';
	
}