<?php
/**
 *	@final class  Models\File
 *	@extend Models\Model
 *	@author Ramone Burrell <burrellramone@gmail.com>
 *	@link http://burrellramone.com
 */
namespace Models;

use \Models;
use \Helpers;
use \Config;
use \Exception;

final class File extends Models\Model {
	public $thumb_width = 320;
	public $thumb_height = 240;
	/**
	 * Don't cache this shit. Videos are too dam big
	 * @var boolean
	 */
	protected $cache_me = false;
	
	public static $video_mime_types = array (
		"video/x-flv" => "",
		"video/mp4" => "",
		"application/x-mpegURL" => "",
		"video/MP2T" => "",
		"video/3gpp" => "",
		"video/quicktime" => "",
		"video/x-msvideo" => "",
		"video/x-ms-wmv" => ""			
	);
	
	public static $image_mime_types = array (
		"image/gif",
		"image/jpeg" => "jpeg",
		"image/pjpeg",
		"image/png" => "png",
		"image/svg+xml",
		"image/tiff",
		"image/vnd.djvu",
		"image/example"		
	);
	
	/**
	 * 
	 * @param string $name
	 * @param string $path
	 */
	public function __construct($name, $path = "", $ext = "", $mime_type = "") {
		parent::__construct(get_class());
		
		$this->setName($name);
		$this->setPath($path);
		$this->setExtension($ext);
		$this->setMimeType($mime_type);
	
		$this->init();
	}
	
	public static function create($name, $contents, $extension = "txt") {
		if (empty($contents)) {
			throw new Exception("Cannot create file '{$name}' with no content.");
		}
		
		$file = str_replace(" ", "_", DOCUMENT_ROOT . "resources/tmp/{$name}.{$extension}");
		
		if(!file_put_contents($file, $contents)) {
			throw new Exception("Could not put contents '{$contents}' to file '{$file}'");	
		}
		
		return new Models\File("$name.{$extension}", $file);
	}
	
	/**
	 * 
	 */
	public function postInit () {
		$this->tmp_path = DOCUMENT_ROOT . "resources/tmp/{$this->getUnderscoredName()}";
		
		if (!file_exists($this->tmp_path)) {
			file_put_contents($this->tmp_path, base64_decode($this->data));
			if (!file_exists($this->tmp_path)) {
				throw new \Exception("Unable to create temporary file '{$this->tmp_path}'");
			}
		}
		$this->real_tmp_path = realpath($this->tmp_path);
		$this->tmp_path = preg_replace("/" . preg_quote(DOCUMENT_ROOT, "/") . "/", "", $this->tmp_path);
		
		if (empty($this->thumbnail)) {
			$this->thumbnail = "/resources/img/thumbnail.svg";
		}
	}
	
	/**
	 * 
	 * @param string $id
	 * @return NULL
	 */
	public static function findById ($id) {
		if (empty($id)) return NULL;
		self::$class = get_called_class();
		self::$qry = "SELECT file.*,
				SUBSTRING(file.id, 1, 4) as subid
				FROM file
				WHERE file.id = '$id'";
		
		return self::getObjectFromQry();
	}
	
	/**
	 * 
	 * @param unknown $name
	 * @param string $path
	 */
	public function reconstruct ($name, $path = "") {
		$this->setName($name);
		$this->setPath($path);
		$this->init();
	}
	
	/**
	 * 
	 * @param string $filename
	 */
	public function setName ($name) {
		$this->name = $name;
		return $this;
	}
	
	public function getName () {
		return $this->name;
	}
	
	public function getUnderscoredName () {
		return str_replace(" ", "_", $this->getName());
	}
	
	public function getExtension () {
		return $this->ext;
	}
	
	public function setExtension ($ext = "") {
		$this->ext =  $ext;
	}

	/**
	 * 
	 * @param string $path
	 * @return \Models\File
	 */
	public function setPath ($path) {
		$this->path = $path;
		return $this;
	}
	
	public function getPath () {
		return $this->path;
	}
	
	public function getMimeType () {
		return $this->mime_type;
	}
	
	public function setMimeType ($mime_type = "") {
		$this->mime_type = $mime_type;
	}
	
	public function getExt () {
		return $this->ext;
	}

	public function getURL () {
		return $this->url;
	}
	
	public function getSize () {
		return $this->size;
	}
	
	public function getThumbnail () {
		return $this->thumbnail;	
	}
	
	/**
	 * 
	 * @param unknown $mime_type
	 */
	public static function getExtFromMime ($mime_type) {
		if (empty($mime_type)) return null;
		return self::$image_mime_types[$mime_type];
	}
	
	public function getMimeFromExt ($ext) {
		if (empty($ext)) return null;
		foreach (self::$image_mime_types as $mime_type => $extension) {
			if ($extension == $ext) {
				return $mime_type;
			}
		}
	}
	
	private function init () {
		if (!empty($this->path) && file_exists($this->path)) {
			
			//$this->mime_type = '';
			//$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
			//$this->mime_type = finfo_file($finfo, $this->path);
			//finfo_close($finfo);
			
			$this->data = base64_encode(file_get_contents($this->path));
			$this->size = filesize($this->path);
			
			if (empty($this->ext)) {
				$this->ext = pathinfo($this->path, PATHINFO_EXTENSION);
				
				if (empty($this->ext) && !empty($this->mime_type)) {
					$this->ext = self::getExtFromMime($this->mime_type);
				}
				
				if (empty($this->ext)) {
					throw new \Exception("Could not determine extension of file '{$this->path}'");
				}
			}
			
			if (empty($this->mime_type)) {
				$this->mime_type = self::getMimeFromExt($this->ext);
			}
			
			if ($this->isVideo() && $this->getMimeType() === 'video/mp4') {
				$this->createMP4VideoThumb();
			} else if ($this->isImage() && $this->getMimeType() === 'image/jpeg') {
				$this->createJPEGThumb();
			}
		}
	}
	
	public function getCompany() {
		if (empty($this->company)) {
			$this->company = Models\Company::findById($this->company_id);
		}
		return $this->company;
	}
	
	public function delete () {
		$this->status_id = Models\FileStatus::findByLabel('Deleted')->id;
		return $this->save();
	}
	
	public function isVideo () {
		return array_key_exists($this->mime_type, self::$video_mime_types);
	}
	
	public function isImage () {
		return array_key_exists($this->mime_type, self::$image_mime_types);
	}
	
	private function createMP4VideoThumb () {
		if (empty($this->path)) return false;
		
		if (!file_exists($this->path)) {
			throw new \Exception("Could not create video thumbnail. File '{$this->path}' does not exist");
		}
		
		$this->thumbnail_mime_type = "image/jpeg";
		$thumbnail_path = $this->path . ".jpeg";
		$cmd = "ffmpeg  -y -itsoffset -4  -i \"{$this->path}\" -vcodec mjpeg -vframes 1 -an -f rawvideo -s {$this->thumb_width}x{$this->thumb_height} \"{$thumbnail_path}\" 2>&1";

		exec($cmd, $output, $return_var);
		
		if (!file_exists($thumbnail_path)) {
			throw new \Exception("Could not create thumbnail for video. File '{$thumbnail_path}' does not exist");
		}
		
		$this->thumbnail = "data:image/jpeg;base64," . base64_encode(file_get_contents($thumbnail_path));
		unlink($thumbnail_path);
	}
	
	private function createJPEGThumb() {
		if (empty($this->path)) return false;
	
		$thumbWidth = 200;
		$this->thumbnail_mime_type = "image/jpeg";
	
		// parse path for the extension
		$info = pathinfo($this->path);
	
		// continue only if this is a JPEG image
		if ( strtolower($info['extension']) == 'jpg' ||
				strtolower($info['extension']) == 'jpeg') {
						
			// load image and get image size
			$img = imagecreatefromjpeg($this->path);
			$width = imagesx( $img );
			$height = imagesy( $img );

			// calculate thumbnail size
			$new_width = $thumbWidth;
			$new_height = floor( $height * ( $thumbWidth / $width ) );

			// create a new temporary image
			$tmp_img = imagecreatetruecolor( $new_width, $new_height );

			// copy and resize old image into new image
			imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
				
			//OB that shit
			ob_start();
				
			// dump that shit
			imagejpeg($tmp_img);
				
			//Cleans up memory
			imagedestroy( $img );
			$this->thumbnail = "data:image/jpeg;base64," . base64_encode(ob_get_clean());
		}
	}
	
	public function uncache () {
		if(!empty($this->path) && file_exists($this->path)) {
			if(!unlink($this->path)) {
				throw new \Exception("Unable to unlink file with path {$this->path}");
			}
		}
		
		if(!empty($this->real_tmp_path) && file_exists($this->real_tmp_path)) {
			if(!unlink($this->real_tmp_path)) {
				throw new \Exception("Unable to unlink file with path {$this->real_tmp_path}");
			}
		}
		
		return parent::uncache();
	}
}