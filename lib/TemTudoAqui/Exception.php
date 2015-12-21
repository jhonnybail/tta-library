<?php

/**
 * TemTudoAqui (http://library.temtudoaqui.info/)
 *
 * @package     TemTudoAqui
 * @link        http://github.com/jhonnybail/tta-library para o repositório de origem
 * @copyright   Copyright (c) 2014 Tem Tudo Aqui. (http://www.temtudoaqui.info)
 * @license     http://license.temtudoaqui.info
 */
namespace TemTudoAqui;

use TemTudoAqui\Utils\Data\String,
	TemTudoAqui\Utils\Data\ArrayObject;

/**
 * Classe padrão de Excessão.
 */
class Exception extends \Exception
{
    
	/**
     * Lista de mensagens para cada código de excessão de erro.
     * @var \TemTudoAqui\Utils\Data\ArrayObject
     */
	private $messages;
	
    /**
     * Construtor
     *
     * @param  int      $code       Código do erro
     * @param  string	$file       Arquivo do erro
     * @param  int	    $line       Linha do erro
     * @param  string	$message    Messagem gerada no erro
     */
    public function __construct($code, $file = '', $line = 0, $message = ''){
    	
    	$this->messages = new ArrayObject();
    	$this->createMessagesArray();
    	
    	if(empty($message))
    		$message = $this->message($code);
    	
        parent::__construct($message, $code);
        
    }    
    
    /**
     * Função estática para formatar o código de excessão.
     *
     * @param  int		$code
     * @return \TemTudoAqui\Utils\Data\String
     */
    protected static function formatCode($code){
    	
    	$code = new String($code);
 
    	if((int) $code->length() == 1)
    		return "0000".$code;
    	elseif((int) $code->length() == 2)
    		return "000".$code;
    	elseif((int) $code->length() == 3)
    		return "00".$code;
    	elseif((int) $code->length() == 4)
    		return "0".$code;
    	else
    		return $code;
    	
    }
    
    /**
     * Retorna a mensagem de erro completa, juntos com código do erro, linha e arquivo.
     *
     * @return \TemTudoAqui\Utils\Data\String
     */
    public function showError(){
    	return new String($this->getMessage()." (Error: #".self::formatCode($this->getCode())." on line ".$this->line." in file ".$this->file.")");
    }
    
    /**
     * Cria uma lista de mensagens apropriadas de acordo com o código do erro.
     *
     * @return null
     */
    protected function createMessagesArray(){
    	
    	$this->messages[1] = 'Não é um número válido';
    	$this->messages[2] = 'Argumento está em branco';
    	$this->messages[3] = 'Array inválida';
    	$this->messages[4] = 'A função curl não está habilitada';
    	$this->messages[5] = 'O valor passado para o atributo data não é um objeto URLVariables válido';
    	$this->messages[6] = 'URL não existe';
    	$this->messages[7] = 'O caminho não existe';
    	$this->messages[8] = 'O caminho deve ser local, não deve ser HTTP';
    	$this->messages[9] = 'Requisição não é um arquivo';
    	$this->messages[10] = 'A classe finfo não está habilitada';
    	$this->messages[11] = 'Não foi possível alterar a permissão do arquivo ou diretório';
    	$this->messages[12] = 'Requesição não é um diretório';
    	$this->messages[13] = 'A Requesição ultrapassa o limite de memória do sistema';
    	$this->messages[14] = 'Tipo de imagem não suportada';
    	$this->messages[15] = 'Arquivo não está aberto ou não foi criado';
    	$this->messages[16] = 'Não foi possível mover';
    	$this->messages[17] = 'Não foi possível renomear';
    	$this->messages[18] = 'O Argumento não é um booleano';
    	$this->messages[19] = 'O Argumento não é um DOM válido';
		$this->messages[20] = 'Classe não está habilitada';
		$this->messages[21] = 'Valor informado inválido';
		$this->messages[22] = 'Categoria com filhos cadastrados';
		$this->messages[23] = 'Pontos requeridos maior que a quantidade de pontos no pré-pago';
		$this->messages[24] = 'Não exitem créditos disponiveis para o cliente informado';
		$this->messages[25] = 'Não há saldo para realizar esta operação';
        $this->messages[26] = 'Objeto não encontrado';
        $this->messages[27] = 'Registro já existe';
    	
    }
    
    /**
     * Retorna a mensagem de acordo com o código informado por parâmetro.
     *
     * @param  integer	$code
     * @return \TemTudoAqui\Utils\Data\String
     */
    public function message($code){
    	
    	$code = (int)(string) $code;
    	
    	return new String($this->messages[$code]);
    	
    }
    
}