<?php

/**
 * TemTudoAqui (http://library.temtudoaqui.info/)
 *
 * @package     TemTudoAqui\Events
 * @link        http://github.com/jhonnybail/tta-library para o repositório de origem
 * @copyright   Copyright (c) 2014 Tem Tudo Aqui. (http://www.temtudoaqui.info)
 * @license     http://license.temtudoaqui.info
 */
namespace TemTudoAqui\Events;

/**
 * Classe genérica para os diversos eventos.
 */
class Event {
	
	/**
     * @const string Constante definida para evento de mudança no Objeto.
     */
	const   CHANGE = 'change';
	
	/**
     * @const string Constante definida para evento de completado no Objeto.
     */
	const   COMPLETE = 'complete';
	
	/**
     * @const string Constante definida para evento de inicio carregamento no Objeto.
     */
	const   LOAD = 'load';
	
	/**
     * @const string Constante definida para evento de conexão executada no Objeto.
     */	
	const   CONNECT = 'connect';
	
	/**
     * @const string Constante definida para evento de inicialização no Objeto.
     */
	const   INIT = 'init';
	
	/**
     * @const string Constante definida para evento de destruição no Objeto.
     */
	const   DESTROY = 'destroy';

    /**
     * Definição do tipo de evento.
     * @var string
     */
    public  $type;

    /**
     * Ponteiro para o objeto alvo do evento.
     * @var \TemTudoAqui\Generic
     */
    public  $currentTarget;

    /**
     * Construtor
     *
     * @param  string                                   $type   tipo de evento
     * @param  \TemTudoAqui\Events\IObjectEventManager  $target objeto do evento
     */
    public function __construct($type, IObjectEventManager $target){
        $this->type 			= $type;
        $this->currentTarget 	= $target;
    }

    /**
     * Retorna o tipo do evento.
     *
     * @return  string
     */
    public function getType(){
        return $this->type;
    }

    /**
     * Retorna o objeto alvo do evento.
     *
     * @return  mixed
     */
    public function getTarget(){
        return $this->currentTarget;
    }

}

?>