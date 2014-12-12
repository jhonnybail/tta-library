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

use TemTudoAqui\Utils\Data\String;

/**
 * Tratamento de datas.
 */
class DateTime extends \DateTime {
	
	const	DAY 				= 'd';
	const	DAY_SHORT			= 'j';
	const	WEEKDAY				= 'w';
	const	WEEKDAY_NAME		= 'l';
	const	WEEKDAY_NAME_SHORT	= 'D';
	const	YEARDAY				= 'z';
	const	WEEK 				= 'W';
	const	MONTH 				= 'm';
	const	MONTH_SHORT			= 'n';
	const	MONTH_NAME			= 'F';
	const	MONTH_NAME_SHORT	= 'M';
	const	MONTH_DAYS			= 't';
	const	YEAR 				= 'Y';
	const	YEAR_SHORT 			= 'y';
	const	LEAPYEAR 			= 'L';
	const	MERIDIAM			= 'a';
	const	HOUR				= 'H';
	const	HOUR_M				= 'h';
	const	HOUR_SHORT			= 'G';
	const	HOUR_M_SHORT		= 'g';
	const	MINUTES				= 'i';
	const	SECONDS				= 's';
	const	TIMEZONE			= 'e';
	const	DATETIME_FULL		= 'c';
    const	DATETIME_LONG		= 'r';

    const	EXT 		        = 'Y-m-d H:i:s';

    /**
     * Constructor
     *
     * @param   string|null         $time
     * @param   string              $format
     * @param   \DateTimeZone|null   $timezone
     */
	public function __construct($time = "", $format = "Y-m-d", \DateTimeZone $timezone = null)
    {
		$temp = parent::createFromFormat($format, (string)$time, is_null($timezone) ? new \DateTimeZone(date("e")) : $timezone);
        parent::__construct($temp ? $temp->format("Y-m-d H:i:s") : 'now');
	}

    /**
     * Cria objeto estáticamente.
     *
     * @param   string              $format
     * @param   string              $time
     * @param   \DateTimeZone|null  $timezone
     * @return  \TemTudoAqui\Utils\Data\DateTime
     */
	public static function createFromFormat($format, $time = "", \DateTimeZone $timezone = null)
    {
		return new DateTime($time, $format, $timezone);
	}

    /**
     * Incrementa data.
     *
     * @param   int  $year
     * @param   int  $month
     * @param   int  $day
     * @return  \TemTudoAqui\Utils\Data\DateTime
     */
	public function addDate($year, $month = 0, $day = 0)
    {
		
		if($year > 0)
			$this->add(new \DateInterval("P".$year."Y"));
		if($month > 0)
			$this->add(new \DateInterval("P".$month."M"));
		if($day > 0)
			$this->add(new \DateInterval("P".$day."D"));
			
		return $this;
		
	}

    /**
     * Subtrai data.
     *
     * @param   int  $year
     * @param   int  $month
     * @param   int  $day
     * @return  \TemTudoAqui\Utils\Data\DateTime
     */
	public function subDate($year, $month = 0, $day = 0)
    {
		
		if($year > 0)
			$this->sub(new \DateInterval("P".$year."Y"));
		if($month > 0)
			$this->sub(new \DateInterval("P".$month."M"));
		if($day > 0)
			$this->sub(new \DateInterval("P".$day."D"));
			
		return $this;
		
	}

    /**
     * Incrementa hora.
     *
     * @param   int  $hour
     * @param   int  $minute
     * @param   int  $second
     * @return  \TemTudoAqui\Utils\Data\DateTime
     */
	public function addTime($hour, $minute = 0, $second = 0)
    {
		
		if($hour > 0)
			$this->add(new \DateInterval("PT".$hour."Y"));
		if($minute > 0)
			$this->add(new \DateInterval("PT".$minute."M"));
		if($second > 0)
			$this->add(new \DateInterval("PT".$second."D"));
			
		return $this;
		
	}

    /**
     * Subtrai hora.
     *
     * @param   int  $hour
     * @param   int  $minute
     * @param   int  $second
     * @return  \TemTudoAqui\Utils\Data\DateTime
     */
	public function subTine($hour, $minute = 0, $second = 0)
    {
		
		if($hour > 0)
			$this->sub(new \DateInterval("PT".$hour."Y"));
		if($minute > 0)
			$this->sub(new \DateInterval("PT".$minute."M"));
		if($second > 0)
			$this->sub(new \DateInterval("PT".$second."D"));
			
		return $this;
		
	}

    /**
     * Retorna a diferença entre datas.
     *
     * @param   \TemTudoAqui\Utils\Data\DateTime    $data
     * @return  \TemTudoAqui\Utils\Data\String
     */
	public function differenceDate(DateTime $data)
    {
		return (string)$this->diff($data);
	}

    /**
     * Retorna a data segundo o formado desejado.
     *
     * @param   string  $format
     * @return  \TemTudoAqui\Utils\Data\String
     */
	public function toString($format = 'd/m/Y')
    {
		return new String($this->format((string) $format));
	}

    /**
     * Retorna a data segundo o formado desejado.
     *
     * @param   string  $format
     * @return  \TemTudoAqui\Utils\Data\String
     */
	public function get($format = 'd/m/Y')
    {
		return new String($this->format((string) $format));
	}

    /**
     * Função mágica para serialização do objeto.
     *
     * @return \TemTudoAqui\Utils\Data\ArrayObject
     */
	public function __sleep()
    {
		return new ArrayObject(array('date'));
	}

    /**
     * Retorna o nome da classe.
     *
     * @param  null
     * @return string
     */
	public function __toString()
    {
		return (string) $this->toString("U");
	}

    /**
     * Chamado quando é destruido o objeto.
     *
     * @return void
     */
	public function __destruct()
    {

    }
	
}