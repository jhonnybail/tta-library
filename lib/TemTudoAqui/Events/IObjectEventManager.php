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
 * Interface para gerenciamento de Eventos.
 */
Interface IObjectEventManager {

    /**
     * Adiciona um evento no gerenciador.
     *
     * @param  string 	$event
     * @param  callback $callback
     * @param  int      $priority
     * @return void
     */
    public function attach($event, $callback, $priority = 99);

    /**
     * Remove um evento.
     *
     * @param  string 	$event
     * @param  callback $callback
     * @return void
     */
    public function detach($event, $callback);

    /**
     * Dispara um evento.
     *
     * @param  string   $event
     * @return void
     */
    public function trigger($event);

} 