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

use TemTudoAqui\Utils\Data\ArrayObject;

/**
 * Trait para gerenciamento de Eventos.
 */
trait ObjectEventManager {

    /**
     * Lista de eventos.
     * @var array
     */
    private  $events = [];

    /**
     * Adiciona um evento no gerenciador.
     *
     * @param  string 	$event
     * @param  callback $callback
     * @param  int      $priority
     * @return void
     */
    public function attach($event, $callback, $priority = 99)
    {

        $e = new Event($event, $this);

        if(!isset($this->events[$event]))
            $this->events[$event] = [];

        $listener = [];
        $listener['event']      = $e;
        $listener['callback']   = $callback;
        $listener['priority']   = $priority;

        $this->events[$event][] = $listener;

        $this->events[$event]   = ArrayObject::ArrayOrderBy($this->events[$event], 'priority', 'ASC');

    }

    /**
     * Remove um evento.
     *
     * @param  string 	$event
     * @param  callback $callback
     * @return void
     */
    public function detach($event, $callback)
    {
        if(!empty($this->events[$event])){
            foreach($this->events[$event] as $k => $listener){
                if($listener['callback'] === $callback)
                    unset($this->events[$k]);
            }
        }
    }

    /**
     * Dispara um evento.
     *
     * @param  string   $event
     * @return void
     */
    public function trigger($event)
    {
        if(!empty($this->events[$event])){
            foreach($this->events[$event] as $listener){
                if(!call_user_func($listener['callback'], $listener['event']))
                    return false;
            }
        }
    }

} 