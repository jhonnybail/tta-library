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

use TemTudoAqui\Generic,
    TemTudoAqui\Utils\Data\String,
	TemTudoAqui\Utils\Data\HTMLFile,
	TemTudoAqui\Utils\Data\XMLFile,
	TemTudoAqui\Utils\Data\ImageFile,
	TemTudoAqui\Utils\Data\File,
	TemTudoAqui\Utils\Data\IFileObject,
	TemTudoAqui\InvalidArgumentException;

/**
 * Classe para trabalhar com arquivos, como salvar e deletar.
 */
class FileReference extends Generic
{

	/**
     * Salva o arquivo passado por referencia.
     *
     * @param   \TemTudoAqui\Utils\Data\IFileObject	    $file
     * @param   string								    $newPath
     * @param   string								    $newName
     * @param   string								    $newExtension
     * @throws  \TemTudoAqui\InvalidArgumentException
     * @throws  \TemTudoAqui\Utils\Net\NetException
     * @return  \TemTudoAqui\Utils\Data\IFileObject
     */
	public static function Save(IFileObject $file, $newPath = '', $newName = '', $newExtension = '')
    {

		if(!empty($newName))
			$file->fileName = new String($newName);
		else
			if($file->fileName == '')
				throw new InvalidArgumentException(2, __CLASS__, 31, 'O nome do arquivo está em branco');

		if(!empty($newExtension))
			$file->extension = new String($newExtension);
		else
			if($file->extension == '')
				throw new InvalidArgumentException(2, __CLASS__, 31, 'A extensão do tipo arquivo está em branco');

		$newPath = new String((string) $newPath);
		$dirName = new String(dirname($file->url));

		if($newPath->search("http://") || $dirName->search("http://"))
			throw new NetException(8, $file->reflectionClass->getName(), 31);
		elseif($file->getData()->toString() == '')
			throw new NetException(15, $file->reflectionClass->getName(), 31);
		elseif($file->urlRequest != null){

			if($file->urlRequest->getType() != URLRequest::URLFILETYPE)
				throw new NetException(9, __CLASS__, 31);
			else{

				if($newPath->toString() == ""){
					 
					if(!file_exists(dirname($file->urlRequest->url)))
						throw new NetException(7, __CLASS__, 31);
					else{
						
						$f = fopen(dirname($file->urlRequest->url)."/".$file->fileName.".".$file->extension, 'w');
						fwrite($f, (string) $file->getData());
						fclose($f);
						
						$urlR = new URLRequest(dirname($file->urlRequest->url)."/".$file->fileName.".".$file->extension);
						
						if($file->extension == 'html' || $file->extension == 'htm' || $file->extension == 'xhtml')
							return new HTMLFile($urlR);
						elseif($file->extension == 'xml')
							return new XMLFile($urlR);
						elseif($file->extension->toLowerCase() == ImageFile::IMAGETYPEJPEG || $file->extension->toLowerCase() == ImageFile::IMAGETYPEJPG || $file->extension->toLowerCase() == ImageFile::IMAGETYPEPNG || $file->extension->toLowerCase() == ImageFile::IMAGETYPEGIF)
							return new ImageFile($urlR);
						else
							return new File($urlR);
						 
					}

				}elseif(!file_exists((string) $newPath))
					throw new NetException(7, __CLASS__, 31);
				else{

					$f = fopen(((string) $newPath)."/".$file->fileName.".".$file->extension, 'w');
					fwrite($f, (string) $file->getData());
					fclose($f);

					$urlR = new URLRequest(((string) $newPath)."/".$file->fileName.".".$file->extension);

					if($file->extension == 'html' || $file->extension == 'htm' || $file->extension == 'xhtml')
						return new HTMLFile($urlR);
					elseif($file->extension == 'xml')
						return new XMLFile($urlR);
					elseif($file->extension->toLowerCase() == ImageFile::IMAGETYPEJPEG || $file->extension->toLowerCase() == ImageFile::IMAGETYPEJPG || $file->extension->toLowerCase() == ImageFile::IMAGETYPEPNG || $file->extension->toLowerCase() == ImageFile::IMAGETYPEGIF)
						return new ImageFile($urlR);
					else
						return new File($urlR);
						
				}

			}
				
		}else{

			if(empty($newPath)){

				if(!file_exists(dirname($file->urlRequest->url)))
					throw new NetException(7, __CLASS__, 31);
				else{

					$f = fopen(dirname($file->urlRequest->url)."/".$file->fileName.".".$file->extension, 'w+');
					fwrite($f, (string) $file->getData());
					fclose($f);

					$urlR = new URLRequest(dirname($file->urlRequest->url)."/".$file->fileName.".".$file->extension);

					if($file->extension == 'html' || $file->extension == 'htm' || $file->extension == 'xhtml')
						return new HTMLFile($urlR);
					elseif($file->extension == 'xml')
						return new XMLFile($urlR);
					elseif($file->extension->toLowerCase() == ImageFile::IMAGETYPEJPEG || $file->extension->toLowerCase() == ImageFile::IMAGETYPEJPG || $file->extension->toLowerCase() == ImageFile::IMAGETYPEPNG || $file->extension->toLowerCase() == ImageFile::IMAGETYPEGIF)
						return new ImageFile($urlR);
					else
						return new File($urlR);

				}
				 
			}elseif(!file_exists($newPath))
                throw new NetException(7, __CLASS__, 31);
            else{

				$f = fopen($newPath."/".$file->fileName.".".$file->extension, 'w+');
				fwrite($f, (string) $file->getData());
				fclose($f);

				$urlR = new URLRequest($newPath."/".$file->fileName.".".$file->extension);

				if($file->extension == 'html' || $file->extension == 'htm' || $file->extension == 'xhtml')
					return new HTMLFile($urlR);
				elseif($file->extension == 'xml')
					return new XMLFile($urlR);
				elseif($file->extension->toLowerCase() == ImageFile::IMAGETYPEJPEG || $file->extension->toLowerCase() == ImageFile::IMAGETYPEJPG || $file->extension->toLowerCase() == ImageFile::IMAGETYPEPNG || $file->extension->toLowerCase() == ImageFile::IMAGETYPEGIF)
					return new ImageFile($urlR);
				else
					return new File($urlR);

			}
			 
		}

	}

	/**
     * Deleta o arquivo passado por refêrencia.
     *
     * @param   mixed						            $file
     * @throws  \TemTudoAqui\Utils\Net\NetException
     * @return  \TemTudoAqui\Utils\Data\IFileObject
     */
	public static function Delete($file)
    {

		$returnFile = '';
        $path       = '';
		if($file instanceof IFileObject){
			$path = $file->urlRequest->url;
			$returnFile = $file;
		}elseif($file instanceof String)
			$path = $file->toString();
		elseif(!empty($file))
			$path = $file;
			
		$path = new String((string) $path);	
		
		if(!empty($path)){
				
			$urlR = new URLRequest($path);
				
			if($path->search("http://"))
				throw new NetException(8, __CLASS__, 160);
			elseif($urlR->getType() != URLRequest::URLFILETYPE)
				throw new NetException(9, __CLASS__, 160);
			elseif(file_exists($path)){
				
				if(!($file instanceof File)){
					
					$urlR 		= new URLRequest($path);
					$div1 		= explode(".", $path);
					$extension 	= new String($div1[count($div1)-1]);
					
					if($extension->toString() == 'html' || $extension->toString() == 'htm' || $extension->toString() == 'xhtml')
						$returnFile = new HTMLFile($urlR);
					elseif($extension->toString() == 'xml')
						$returnFile = new XMLFile($urlR);
					elseif($extension->toLowerCase()->toString() == ImageFile::IMAGETYPEJPEG || $extension->toLowerCase()->toString() == ImageFile::IMAGETYPEJPG || $extension->toLowerCase()->toString() == ImageFile::IMAGETYPEPNG || $extension->toLowerCase()->toString() == ImageFile::IMAGETYPEGIF)
						$returnFile = new ImageFile($urlR);
					else
						$returnFile = new File($urlR);
					
					$returnFile->open();
						
				}else{
					$returnFile = $file;
					$returnFile->open();
				}
				
				@unlink($path);

				return $returnFile;

			}else
				throw new NetException(7, __CLASS__, 160);
				
		}

        return $returnFile;

	}

	/**
     * Define a permissão do arquivo.
     *
     * @param   mixed                                    $file
     * @param   string                                   $mode
     * @throws  \TemTudoAqui\Utils\Net\NetException
     * @throws  \TemTudoAqui\InvalidArgumentException
     * @return  void
     */
	public static function Permission($file, $mode = '755')
    {
        $path = '';
		if($file instanceof IFileObject)
			$path = $file->urlRequest->url;
		elseif($file instanceof String)
			$path = $file->toString();
		elseif(!empty($file))
			$path = $file;

		$path = new String((string) $path);	

		if(!empty($path)){

			$urlR = new URLRequest($path);

			if($path->search("http://"))
				throw new NetException(8, __CLASS__, 220);
			elseif($urlR->getType() != URLRequest::URLFILETYPE)
				throw new NetException(9, __CLASS__, 220);
			elseif(file_exists($path)){
					
				if(!empty($mode)){
					if(!chmod($path, $mode)){
						throw new NetException(11, __CLASS__, 220);
					}
				}else
					throw new InvalidArgumentException(2, __CLASS__, 220);
					
			}else
				throw new NetException(7, __CLASS__, 220);
				
		}

	}

	/**
     * Move o arquivo.
     *
     * @param   mixed	                            $file
     * @param   string	                            $newPath
     * @param   bool	                            $overwrite
     * @throws  \TemTudoAqui\Utils\Net\NetException
     * @return  void
     */
	public static function Move($file, $newPath, $overwrite = false)
    {

        if($file instanceof File)
            if(!is_null($file->urlRequest)){
                $path   = new String((string) $file->urlRequest->url);
            }
        else
            $path 		= new String((string) $file);

		$newPath 	= new String((string) $newPath);

		if(!empty($path) && !empty($newPath)){
				
			$urlR 	= new URLRequest((string) $path);
				
			try{
				$urlNR	= new URLRequest(dirname((string) $newPath));
			}catch(NetException $e){
				throw new NetException(6, __CLASS__, 263, "Caminho de destino inválido");
			}

			if($path->search("http://") && $newPath->search("http://"))
				throw new NetException(8, __CLASS__, 263);
			elseif($urlR->getType() != URLRequest::URLFILETYPE)
				throw new NetException(9, __CLASS__, 263);
			elseif(file_exists((string) $path)){

				try{
						
					if($newPath instanceof File){
						if($overwrite)
							self::Delete($newPath);
						else
							throw new NetException(16, __CLASS__, 263, "Não é possível mover o arquivo pois o caminho de destino já existe");
					}
						
				}catch(NetException $e){
					if($e->getCode() != 6)
						throw new NetException($e->getCode(), $e->getFile(), $e->getLine(), $e->getMessage());
				}

				if($urlNR->getType() == URLRequest::URLFILETYPE || $urlNR->url != ((string) $newPath)){
					if(!@rename((string) $path, (string) $newPath))
						throw new NetException(16, __CLASS__, 263, "Não foi possível mover o arquivo");
				}elseif($urlNR->getType() == URLRequest::URLDIRECTORYTYPE){
					if(!@rename((string) $path, ((string) $newPath).basename($path)))
						throw new NetException(16, __CLASS__, 263, "Não foi possível mover o arquivo");
				}

			}else
				throw new NetException(7, __CLASS__, 263);
				
		}

	}

	/**
     * Renomeia o arquivo.
     *
     * @param   mixed	$file
     * @param   string	$newName
     * @throws  \TemTudoAqui\Utils\Net\NetException
     * @return  void
     */
	public static function Rename($file, $newName)
    {

        $path = '';
		if($file instanceof IFileObject)
			$path = $file->urlRequest->url;
		elseif($file instanceof String)
			$path = $file->toString();
		elseif(!empty($file))
			$path = $file;
		
		$path = new String((string) $path);
		
		if(!empty($path)){
				
			$urlR 	= new URLRequest($path);
				
			try{
				new URLRequest(dirname($path)."/".$newName);
				throw new NetException(17, __CLASS__, 320, "O novo nome escolhido já existe");
			}catch(NetException $e){

				if($e->getCode() != 6)
					throw new NetException($e->getCode(), $e->getFile(), $e->getLine(), $e->getMessage());
					
				if($path->search("http://"))
					throw new NetException(8, __CLASS__, 320);
				elseif($urlR->getType() != URLRequest::URLFILETYPE)
					throw new NetException(9, __CLASS__, 320);
				elseif(file_exists($path)){
						
					if(!@rename($path, dirname($path)."/".$newName))
						throw new NetException(17, __CLASS__, 320, "Não foi possível renomear o arquivo");
						
				}else
					throw new NetException(7, __CLASS__, 320);

			}

				
		}

	}

}