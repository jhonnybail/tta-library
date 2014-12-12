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

use TemTudoAqui\InvalidArgumentException,
	TemTudoAqui\Utils\Net\URLRequest,
	TemTudoAqui\Events\Event;

/**
 * Classe que mantém imagens do tipo JPG, GIF e PNG.
 */
class ImageFile extends File
{
	
	const IMAGETYPEJPG 	= 'jpg';
	const IMAGETYPEJPEG = 'jpeg';
	const IMAGETYPEGIF 	= 'gif';
	const IMAGETYPEPNG 	= 'png';
	
	/**
	 * Instância da imagem criada.
     * @var mixed
     */
	private	$image;
	
	/**
	 * Instância original da imagem criada.
     * @var mixed
     */
	private	$originalImage;
	
	/**
	 * Largura da imagem.
     * @var int
     */
	private	$width;
	
	/**
	 * Altura da imagem.
     * @var int
     */
	private	$height;
	
	/**
	 * Qualidade da imagem.
     * @var int
     */
	private	$quality;
	
	/**
     * Construtor
     *
     * @param  mixed	    $arg1
     * @param  float|null	$arg2
     */
	public function __construct($arg1 = null, $arg2 = null)
    {
		
		if(Number::VerifyNumber($arg1) && Number::VerifyNumber($arg2)){
			
			parent::__construct();
			
			$arg1 = Number::VerifyNumber($arg1);
			$arg2 = Number::VerifyNumber($arg2);
			
			$this->image	= imagecreatetruecolor($arg1, $arg2);
			$this->width 	= new Number($arg1);
			$this->height 	= new Number($arg2);
			$this->generateData();
			
		}elseif(is_object($arg1)){
			
			if($arg1 instanceof URLRequest){
				
				parent::__construct($arg1);
				
			}else
				parent::__construct();
				
			$this->image	= null;
			$this->width 	= new Number(0);
			$this->height 	= new Number(0);
			
		}elseif(is_resource($arg1)){
			
			parent::__construct();
			
			$this->image 			= $arg1;
			$this->generateData();
			$this->width 	= new Number(imagesx($this->image));
			$this->height 	= new Number(imagesy($this->image));
			
		}else{
			
			parent::__construct();
			
			$this->image	= null;
			$this->width 	= new Number(0);
			$this->height 	= new Number(0);
			
		}
		
		$this->originalImage	= $this->image;
		$this->quality			= new Number(100);
		
		$this->attach(Event::LOAD, function(Event $eve){
			
			//if($eve->getTarget()->extension == ImageFile::IMAGETYPEGIF || $eve->getTarget()->extension == ImageFile::IMAGETYPEPNG || $eve->getTarget()->extension == ImageFile::IMAGETYPEJPEG || $eve->getTarget()->extension == ImageFile::IMAGETYPEJPG){ 
			
				$eve->getTarget()->image 	= $eve->getTarget()->originalImage = @imagecreatefromstring($eve->getTarget()->data);
				$eve->getTarget()->width 	= new Number(@imagesx($eve->getTarget()->image));
				$eve->getTarget()->height 	= new Number(@imagesy($eve->getTarget()->image));
				if($eve->getTarget()->extension->toLowerCase()->toString() == ImageFile::IMAGETYPEPNG)
					$eve->getTarget()->alpha();
				
			//}else
				//throw new InvalidArgumentException(14, $eve->getTarget()->reflectionClass->getName(), 346);
				
		});
		
	}
	
	/**
	 * Ativa a transparência na imagem.
     *
     * @throws  \TemTudoAqui\InvalidArgumentException
     * @return  \TemTudoAqui\Utils\Data\ImageFile
	 */ 
	public function alpha()
    {
		if(is_resource($this->image))
			self::SetAlpha($this->image);
		else
			throw new InvalidArgumentException(15, $this->reflectionClass->getName(), 131);
		return $this;
	}
	
	 /**
	 * Ativa a transparência na imagem passada por parâmetro.
     * 
	 * @param  resource	                            $image
     * @return void
	 */ 
	public static function SetAlpha($image)
    {
		  
	  	//$background = @imagecolorallocatealpha($image, 0, 0, 0, 127); 
        //@imagecolortransparent($image, $background);
        //@imagefill($image, 0, 0, $background); 
        @imagealphablending($image, false);
        @imagesavealpha($image, true);
		
		
	}
	
	 /**
	 * Retorna o objeto Image redimensionado de acordo com os parâmetros passado.
     * 
	 * @param   int	                                    $width
	 * @param   int|null	                            $height
	 * @param   bool	                                $perspective
	 * @throws  \TemTudoAqui\InvalidArgumentException
     * @return  \TemTudoAqui\Utils\Data\ImageFile
	 */ 
	public function resize($width, $height = null, $perspective = true)
    {
		
		if(is_resource($this->image)){
		
			if(Number::VerifyNumber($width))
                $width = Number::VerifyNumber($width);
			elseif($width == null)
                $width = Number::VerifyNumber($this->width);
			else
				throw new InvalidArgumentException(1, __CLASS__, 156);
			
			if(Number::VerifyNumber($height))
                $height = Number::VerifyNumber($height);
			elseif($height == null)
                $height = Number::VerifyNumber($this->height);
			else
				throw new InvalidArgumentException(1, __CLASS__, 156);
				
			
			$cL = Number::VerifyNumber($this->width);
			$cA = Number::VerifyNumber($this->height);
			
			if(!empty($width) && $cL > $width && $cL != $width){
				$d = $cL/$width;
				$cL /= $d;
				
				if($perspective) $cA /= $d;
			}
			
			if(!empty($height) && $cA > $height && $cA != $height){
				$d = $cA/$height;
				$cA /= $d;
				  
				if($perspective) $cL /= $d;
			}
			
			$temp = new ImageFile($cL, $cA);
			$temp->alpha();
	
			imagecopyresampled($temp->image, $this->image, 0, 0, 0, 0, $cL, $cA, Number::VerifyNumber($this->width), Number::VerifyNumber($this->height));
	
			
			return $temp;
		
		}else
			throw new InvalidArgumentException(15, $this->reflectionClass->getName(), 156);
		  		  
	}
	
	 /**
	 * Retorna o objeto Image mesclado com a imagem informada por parâmetro.
     * 
	 * @param   \TemTudoAqui\Utils\Data\ImageFile	$image
	 * @param   int	                                $x
	 * @param   int	                                $y
	 * @param   int	                                $posX
	 * @param   int	                                $posY
	 * @param   int	                                $alpha
     * @throws  \TemTudoAqui\InvalidArgumentException
     * @return  \TemTudoAqui\Utils\Data\ImageFile
	 */ 
	public function merge(ImageFile $image, $x = 0, $y = 0, $posX = 0, $posY = 0, $alpha = 100)
    {
		
		if(is_resource($this->image)){
		
			if(Number::VerifyNumber($x))
				$x = Number::VerifyNumber($x);
			elseif($x != 0)
				throw new InvalidArgumentException(1, __CLASS__, 208);
			
			if(Number::VerifyNumber($y))
				$y = Number::VerifyNumber($y);
			elseif($y != 0)
				throw new InvalidArgumentException(1, __CLASS__, 208);
				
			if(Number::VerifyNumber($posX))
				$posX = Number::VerifyNumber($posX);
			elseif($posX != 0)
				throw new InvalidArgumentException(1, __CLASS__, 208);
				
			if(Number::VerifyNumber($posY))
				$posY = Number::VerifyNumber($posY);
			elseif($posY != 0)
				throw new InvalidArgumentException(1, __CLASS__, 208);
				
			if(Number::VerifyNumber($alpha))
				$alpha = Number::VerifyNumber($alpha);
			elseif($alpha != 0)
				throw new InvalidArgumentException(1, __CLASS__, 208);
			
			$img = clone $this;
			$img->alpha();
			
			$imageT = new ImageFile($this->width, $this->height);
	        imagecopy($imageT->image, $img->image, 0, 0, 0, 0, Number::VerifyNumber($this->width), Number::VerifyNumber($this->height)); 
	        imagecopy($imageT->image, $image->image, $x, $y, $posX, $posY, Number::VerifyNumber($image->width), Number::VerifyNumber($image->height));
	        imagecopymerge($img->image, $imageT->image, 0, 0, 0, 0, Number::VerifyNumber($this->width), Number::VerifyNumber($this->height), $alpha); 
		 	
	        $img->alpha();
	        
			return $img;
		
		}else
			throw new InvalidArgumentException(15, $this->reflectionClass->getName(), 208);
		  
	}
	
	 /**
	 * Retorna o objeto Image recortado de acordo com os parâmetros informados.
     * 
	 * @param   int	                                $width
	 * @param   int	                                $height
	 * @param   int	                                $x
	 * @param   int	                                $y
     * @throws  \TemTudoAqui\InvalidArgumentException
     * @return  \TemTudoAqui\Utils\Data\ImageFile
	 */ 
	public function cut($width, $height, $x = 0, $y = 0)
    {
		
		if(is_resource($this->image)){
		
			if(Number::VerifyNumber($width))
                $width = Number::VerifyNumber($width);
			elseif($width != 0)
				throw new InvalidArgumentException(1, __CLASS__, 257);
			
			if(Number::VerifyNumber($height))
                $height = Number::VerifyNumber($height);
			elseif($height != 0)
				throw new InvalidArgumentException(1, __CLASS__, 257);
				
			if(Number::VerifyNumber($x))
				$x = Number::VerifyNumber($x);
			elseif($x != 0)
				throw new InvalidArgumentException(1, __CLASS__, 257);
			
			if(Number::VerifyNumber($y))
				$y = Number::VerifyNumber($y);
			elseif($y != 0)
				throw new InvalidArgumentException(1, __CLASS__, 257);
			
			$img = new ImageFile($width, $height);
			$img->merge($this, 0, 0, $x, $y);
			
			return $img;
		
		}else
			throw new InvalidArgumentException(15, $this->reflectionClass->getName(), 257);
	
	}
	
	 /**
	 * 
	 * Inverte na horizontal a imagem.
	 *
     * @throws  \TemTudoAqui\InvalidArgumentException
     * @return  \TemTudoAqui\Utils\Data\ImageFile
	 */ 
	public function flipHorizontal()
    {
		
		if(is_resource($this->image)){
		
	    	$img = new ImageFile($this->width, $this->height);
			
	    	for($x = 0; $x < $this->width->getValue(); $x++){
	   			imagecopy($img->image, $this->image, $x, 0, $this->width->getValue() - $x - 1, 0, 1, $this->height->getValue());
	   		}
			
	    	return $img;
    	
		}else
			throw new InvalidArgumentException(15, $this->reflectionClass->getName(), 294);

    }
    
	 /**
	 * 
	 * Inverte na vertical a imagem.
	 *
     * @throws  \TemTudoAqui\InvalidArgumentException
     * @return  \TemTudoAqui\Utils\Data\ImageFile
	 */
	public function flipVertical()
    {
		
		if(is_resource($this->image)){
		
	    	$img = new ImageFile($this->width, $this->height);
	
			for($y = 0; $y < $this->height->getValue(); $y++){
                imagecopy($img->image, $this->image, 0, $y, 0, $this->height->getValue() - $y - 1, $this->width->getValue(), 1);
            }
	
	    	return $img;
    	
		}else
			throw new InvalidArgumentException(15, $this->reflectionClass->getName(), 314);

    }
    
	 /**
	 * Gira a imagem de acordo com o ângulo informado por par�metro.
     * 
	 * @param   int	    $angle
	 * @param   string	$bgColor
	 *
     * @throws  \TemTudoAqui\InvalidArgumentException
     * @return  \TemTudoAqui\Utils\Data\ImageFile
	 */ 
	public function rotate($angle, $bgColor = '0xFFFFFF')
    {
		if(is_resource($this->image)){
    		return new ImageFile(imagerotate($this->image, $angle, $bgColor));
		}else
			throw new InvalidArgumentException(15, $this->reflectionClass->getName(), 334);
    }
	
	 /**
	 * Abre a Imagem.
     * 
	 * @param  \TemTudoAqui\Utils\Net\URLRequest|null	$urlRequest
     * @return void
	 */ 
	public function open(URLRequest $urlRequest = null)
    {
		
		if($urlRequest instanceof URLRequest || $this->urlRequest instanceof URLRequest){
		
			$this->image 	= $this->originalImage = null;
			$this->data		= null;
			
			parent::open($urlRequest);
			
		}
			
	}
	
	 /**
	 * Retorna a imagem original.
     *
     * @return void
	 */ 
	public function resetImage()
    {
		$this->image = $this->originalImage;
	}

	 /**
	 * Gera o dado binário da instância da imagem.
     *
     * @param   string  $type
     * @return  void
	 */ 
	private function generateData($type = self::IMAGETYPEPNG)
    {
		
		if($this->extension->toString() == '' && !empty($type))
			$this->extension = new String($type);
		
		if($this->extension->toString() != '' && is_resource($this->image)){
			
			ob_start();
			
			if($this->extension->toLowerCase()->toString() == ImageFile::IMAGETYPEJPG || $this->extension->toLowerCase()->toString() == ImageFile::IMAGETYPEJPEG)
				imagejpeg($this->image, null, $this->quality->getValue());
			
			if($this->extension->toLowerCase()->toString() == ImageFile::IMAGETYPEPNG)
				imagepng($this->image, null);
				
			if($this->extension->toLowerCase()->toString() == ImageFile::IMAGETYPEGIF)
				imagegif($this->image);
				
			$this->data = ob_get_contents();
			
			ob_end_clean();
			
		}
		
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
		
		if($property == 'image'){
			$this->image = $value;
			$this->generateData();
		}elseif($property == 'width'){
			if(Number::VerifyNumber($value)){
				if(is_numeric($value))
					$value = new Number($value);
				if($this->width->getValue() != $value->getValue() && $this->width->getValue() > 0)
					$this->resize($value, null, false);
				$this->width = $value;
				$this->generateData();
			}else
				throw new InvalidArgumentException(1, __CLASS__, 392);
		}elseif($property == 'height'){
			if(Number::VerifyNumber($value)){
				if(is_numeric($value))
					$value = new Number($value);
				if($this->height->getValue() != $value->getValue() && $this->height->getValue() > 0)
					$this->resize(null, $value, false);
				$this->height = $value;
				$this->generateData();
			}else
				throw new InvalidArgumentException(1, __CLASS__, 392);
		}elseif($property == 'quality'){
			if(Number::VerifyNumber($value)){
				if(is_numeric($value))
					$value = new Number($value);
				$this->quality = $value;
			}elseif($value != 0)  
				throw new InvalidArgumentException(1, __CLASS__, 392);
			else 
				$this->quality = 0;
				
			$this->generateData();
		}else
			parent::__set($property, $value);
			
	}

    /**
     * Retorna propriedades protegidas do objeto.
     *
     * @param   string  $property
     * @return  mixed
     */
	public function __get($property)
    {
		
		if($property == 'image')
			return $this->image;
		elseif($property == 'width')
			return $this->width;
		elseif($property == 'height')
			return $this->height;
		elseif($property == 'quality')
			return $this->quality;
		elseif($property == 'data'){
			$this->generateData();
			return $this->data;
		}
			
		return parent::__get($property);
			
	}

    /**
     * Implementa as modificações e obtém os dados do arquivo.
     *
     * @param   string                          $type
     * @return  \TemTudoAqui\Utils\Data\String
     */
	public function getData($type = self::IMAGETYPEPNG)
    {
		$this->generateData($type);
		return new String((string)$this->data);
	}

    /**
     * Clona o objeto.
     *
     * @return  \TemTudoAqui\Utils\Data\ImageFile
     */
	public function __clone()
    {
		
		$img = new ImageFile($this->image);
		$img->fileName = 'Copy of '.$this->fileName;
		$img->extension = $this->extension;
		
		return $img;
		
	}

    /**
     * Usada para serialização do objeto.
     *
     * @return \TemTudoAqui\Utils\Data\ArrayObject
     */
    public function __sleep()
    {
		return parent::__sleep()->concat(array('image', 'width', 'height', 'quality'));
	}

    /**
     * Retorna um objeto String da função mágica __toString.
     *
     * @return string
     */
	public function __toString()
    {
		$this->generateData();
		return (string) $this->data;
	}
	
}