<?php

/**
 * @author		biziying <baiziying@camera360.com>
 * @description	文件上传类组件
 */
class Upload extends CApplicationComponent
{
	private $mimes = array(
			'hqx'	=>	'application/mac-binhex40',
			'cpt'	=>	'application/mac-compactpro',
			'csv'	=>	array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel'),
			'bin'	=>	'application/macbinary',
			'dms'	=>	'application/octet-stream',
			'lha'	=>	'application/octet-stream',
			'lzh'	=>	'application/octet-stream',
			'exe'	=>	array('application/octet-stream', 'application/x-msdownload'),
			'class'	=>	'application/octet-stream',
			'psd'	=>	'application/x-photoshop',
			'so'	=>	'application/octet-stream',
			'sea'	=>	'application/octet-stream',
			'dll'	=>	'application/octet-stream',
			'oda'	=>	'application/oda',
			'pdf'	=>	array('application/pdf', 'application/x-download'),
			'ai'	=>	'application/postscript',
			'eps'	=>	'application/postscript',
			'ps'	=>	'application/postscript',
			'smi'	=>	'application/smil',
			'smil'	=>	'application/smil',
			'mif'	=>	'application/vnd.mif',
			'xls'	=>	array('application/excel', 'application/vnd.ms-excel', 'application/msexcel'),
			'ppt'	=>	array('application/powerpoint', 'application/vnd.ms-powerpoint'),
			'wbxml'	=>	'application/wbxml',
			'wmlc'	=>	'application/wmlc',
			'dcr'	=>	'application/x-director',
			'dir'	=>	'application/x-director',
			'dxr'	=>	'application/x-director',
			'dvi'	=>	'application/x-dvi',
			'gtar'	=>	'application/x-gtar',
			'gz'	=>	'application/x-gzip',
			'php'	=>	'application/x-httpd-php',
			'php4'	=>	'application/x-httpd-php',
			'php3'	=>	'application/x-httpd-php',
			'phtml'	=>	'application/x-httpd-php',
			'phps'	=>	'application/x-httpd-php-source',
			'js'	=>	'application/x-javascript',
			'swf'	=>	'application/x-shockwave-flash',
			'sit'	=>	'application/x-stuffit',
			'tar'	=>	'application/x-tar',
			'tgz'	=>	array('application/x-tar', 'application/x-gzip-compressed'),
			'xhtml'	=>	'application/xhtml+xml',
			'xht'	=>	'application/xhtml+xml',
			'zip'	=>  array('application/x-zip', 'application/zip', 'application/x-zip-compressed', 'application/octet-stream'),
			'mid'	=>	'audio/midi',
			'midi'	=>	'audio/midi',
			'mpga'	=>	'audio/mpeg',
			'mp2'	=>	'audio/mpeg',
			'mp3'	=>	array('audio/mpeg', 'audio/mpg', 'audio/mpeg3', 'audio/mp3'),
			'aif'	=>	'audio/x-aiff',
			'aiff'	=>	'audio/x-aiff',
			'aifc'	=>	'audio/x-aiff',
			'ram'	=>	'audio/x-pn-realaudio',
			'rm'	=>	'audio/x-pn-realaudio',
			'rpm'	=>	'audio/x-pn-realaudio-plugin',
			'ra'	=>	'audio/x-realaudio',
			'rv'	=>	'video/vnd.rn-realvideo',
			'wav'	=>	array('audio/x-wav', 'audio/wave', 'audio/wav'),
			'bmp'	=>	array('image/bmp', 'image/x-windows-bmp'),
			'gif'	=>	'image/gif',
			'jpeg'	=>	array('image/jpeg', 'image/pjpeg', 'application/stream', 'application/octet-stream'),
			'jpg'	=>	array('image/jpeg', 'image/pjpeg', 'application/stream', 'application/octet-stream'),
			'jpe'	=>	array('image/jpeg', 'image/pjpeg', 'application/stream', 'application/octet-stream'),
			'png'	=>	array('image/png',  'image/x-png', 'application/octet-stream'),
			'tiff'	=>	'image/tiff',
			'tif'	=>	'image/tiff',
			'css'	=>	'text/css',
			'html'	=>	'text/html',
			'htm'	=>	'text/html',
			'shtml'	=>	'text/html',
			'txt'	=>	'text/plain',
			'text'	=>	'text/plain',
			'log'	=>	array('text/plain', 'text/x-log'),
			'rtx'	=>	'text/richtext',
			'rtf'	=>	'text/rtf',
			'xml'	=>	'text/xml',
			'xsl'	=>	'text/xml',
			'mpeg'	=>	'video/mpeg',
			'mpg'	=>	'video/mpeg',
			'mpe'	=>	'video/mpeg',
			'qt'	=>	'video/quicktime',
			'mov'	=>	'video/quicktime',
			'avi'	=>	'video/x-msvideo',
			'movie'	=>	'video/x-sgi-movie',
			'doc'	=>	'application/msword',
			'docx'	=>	array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/octet-stream', 'application/msword'),
			'xlsx'	=>	array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip', 'application/octet-stream'),
			'word'	=>	array('application/msword', 'application/octet-stream'),
			'xl'	=>	'application/excel',
			'eml'	=>	'message/rfc822',
			'json'	=>	array('application/json', 'text/json'),
			'rar'	=>	array('application/octet-stream'),
			'7z'	=>	array('application/octet-stream','application/x-7z-compressed')
		);

	private $checkMimes		= false;		//是否检查文件的MIME类型
	private $allowedMimes	= array();		//允许的文件MIME类型
	private $isEncriptname	= true;			//是否加密文件名
	private $encriptname	= '';			//自定义加密后的文件名
	private $checkSize		= false;		//是否检查文件大小
	private $allowedSize	= 0;			//允许的最大文件大小。单位：字节bit

	public function setAllowType($typeStr)
	{
		$this->checkMimes = true;
		if (is_string($typeStr)&&$typeStr) {
			$types = explode('|', $typeStr);
			foreach ($types as $type) {
				$type = strtolower($type);
				if (isset($this->mimes[$type])) {
					if (is_string($this->mimes[$type])&&!in_array($this->mimes[$type], $this->allowedMimes)) {
						$this->allowedMimes[] = $this->mimes[$type];
					} else if (is_array($this->mimes[$type])) {
						foreach ($this->mimes[$type] as $mime) {
							if (!in_array($mime, $this->mimes)) {
								$this->allowedMimes[] = $mime;
							}
						}
					}
				}
			}
		}
	}

	public function setAllowSize($size)
	{
		$size = intval($size);
		if ($size&&($size>0)) {
			$this->allowedSize	= $size;
			$this->checkSize	= true;
		}
	}

	public function setEncriptname($isEncript=true, $cusName='')
	{
		$isEncript = (bool)$isEncript;
		$this->isEncriptname = $isEncript;
		if ($this->isEncriptname&&$cusName) {
			$this->encriptname = $cusName;
		}
	}

	private function reset()
	{
		$this->checkMimes		= false;
		$this->allowedMimes		= array();
		$this->isEncriptname	= true;
		$this->checkSize		= false;
		$this->allowedSize		= 0;
	}

	public function doUpload($savePath, $filename)
	{
		if (!$savePath||!$filename) {
			$this->reset();
			return false;
		}
		if (!isset($_FILES[$filename])) {
			$this->reset();
			return false;
		}
		if (!$_FILES[$filename]['size']) {
			$this->reset();
			return false;
		}
		if (isset($_FILES[$filename]['error'])&&$_FILES[$filename]['error']) {
			$this->reset();
			return false;
		}
		if (!is_uploaded_file($_FILES[$filename]['tmp_name'])) {
			$this->reset();
			return false;
		}

		$originalFileName = $_FILES[$filename]['name'];
		if ($this->isEncriptname) {
			$time	= time();
			$originalFileNameArr = explode('.', $originalFileName);
			$len	= count($originalFileNameArr);
			if ($len==1) {
				
				if ($this->encriptname) {
					$originalFileName = $this->encriptname;
				} else {
					$originalFileName = md5($originalFileNameArr[0].$time);
				}
			} else {
				if ($this->encriptname) {
					$originalFileNameArr[0] = $this->encriptname;
				} else {
					$originalFileNameArr[0] = md5($originalFileNameArr[0].$time);
				}
				$originalFileName = implode('.', $originalFileNameArr);
			}
		}

		if ($this->checkMimes&&$_FILES[$filename]['type']) {
			if (!in_array($_FILES[$filename]['type'], $this->allowedMimes)) {
				$this->reset();
				return false;
			}
		}

		if ($this->checkSize) {
			$size = $_FILES[$filename]['size'];
			if ($size>$this->allowedSize) {
				$this->reset();
				return false;
			}
		}

		$savePath = str_replace("\\", '/', $savePath);
		$savePath = str_replace('//', '/', $savePath);
		if (file_exists($savePath)) {
			if (!is_dir($savePath)) {
				$this->reset();
				return false;
			}
			if (!is_writable($savePath)) {
				$this->reset();
				return false;
			}
		} else {
			if (!mkdir($savePath, 0777, true)) {
				$this->reset();
				return false;
			}
			chmod($savePath, 0777);
		}

		$file = $savePath.'/'.$originalFileName;
		$file = str_replace('//', '/', $file);
		
		if (move_uploaded_file($_FILES[$filename]['tmp_name'], $file)) {
			$this->reset();
			return $file;
		} else {
			$this->reset();
			return false;
		}
	}
}
