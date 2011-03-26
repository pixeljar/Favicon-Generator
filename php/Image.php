<?php
//image class by Reinis Veips
//requires at least GD2

class Image {
  var $imageType;
  var $imageExtension;
  var $imageHandle;
  var $originalPath;
  var $jpegQuality=100;
  function __construct($imgpath=false) {
    if ($imgpath) $this->openImage($imgpath);
  }
  // ADDED PHP4 CONSTRUCT FOR BACKWARDS COMPATIBILITY
  function Image($imgpath=false) {
    if ($imgpath) $this->openImage($imgpath);
  }
  function openImage($imgpath) {
	// CHANGED EXCEPTION TO RETURN FALSE
    if (!file_exists($imgpath)) return false; // throw new Exception('File doesn\'t exist!');
    $imageData=getimagesize($imgpath);
    if (!$imageData) {
	  //CHANGED EXCEPTION TO RETURN FALSE
      return false; // throw new Exception('Unknown image format!');
    } else {
      $this->imageType=$imageData[2];
      switch ($this->imageType) {
        case IMAGETYPE_GIF:
          $this->imageHandle=imagecreatefromgif($imgpath);
          $this->imageExtension='gif';
          break;
        case IMAGETYPE_PNG:
          $this->imageHandle=imagecreatefrompng($imgpath);
          $this->imageExtension='png';
          break;
        case IMAGETYPE_JPEG:
          $this->imageHandle=imagecreatefromjpeg($imgpath);
          $this->imageExtension='jpeg';
          break;
        case IMAGETYPE_BMP:
          $this->imageHandle=imagecreatefrombmp($imgpath);
          $this->imageExtension='bmp';
          break;
        // CHANGED EXCEPTION TO RETURN FALSE
        default: return false; // throw new Exception('Unknown image format!');
      }
      $this->originalPath=$imgpath;
    }
  }
  
  function saveImage($imgpath=false,$type=false) {
    if (!$imgpath) $imgpath=$this->originalPath;
    if (!$type) {
      $ext=strtolower(substr($imgpath, strrpos($imgpath, '.') + 1));
      switch ($ext) {
        case 'jpeg':
        case 'jpg':
          $type=IMAGETYPE_JPEG;
          break;
        case 'bmp':
          $type=IMAGETYPE_BMP;
          break;
        case 'gif':
          $type=IMAGETYPE_GIF;
          break;
        case 'png':
          $type=IMAGETYPE_PNG;
        default:
          $type=IMAGETYPE_JPEG;
      }
    }
    
    switch ($type) {
      case IMAGETYPE_JPEG:
        return imagejpeg($this->imageHandle,$imgpath,$this->jpegQuality);
        break;
      case IMAGETYPE_GIF:
        return imagegif($this->imageHandle,$imgpath);
        break;
      case IMAGETYPE_PNG:
        return imagepng($this->imageHandle,$imgpath);
        break;
      case IMAGETYPE_BMP:
        return imagebmp($this->imageHandle,$imgpath);
        break;
      default:
        return imagejpeg($this->imageHandle,$imgpath);
    }
  }
  
  function resizeImage($maxwidth,$maxheight,$preserveAspect=true) {
  //function resizes an image
  //it doesn't enlarge the image
    $width=imagesx($this->imageHandle);
    $height=imagesy($this->imageHandle);
    if ($width>$maxwidth && $height>$maxheight) {
      $oldprop=round($width/$height,2);
      $newprop=round($maxwidth/$maxheight,2);
      $preserveAspectx=round($width/$maxwidth,2);
      $preserveAspecty=round($height/$maxheight,2);
      
      if ($preserveAspect)
      {
          if ($preserveAspectx<$preserveAspecty)
          {
              $newwidth=$width/($height/$maxheight);
              $newheight=$maxheight;
          }
          else 
          {
              $newwidth=$maxwidth;
              $newheight=$height/($width/$maxwidth);
          }

          $dest=imagecreatetruecolor($newwidth,$newheight);
          // CHANGED EXCEPTION TO RETURN FALSE
          if (imagecopyresampled($dest,$this->imageHandle,0,0,0,0,$newwidth,$newheight,$width,$height)==false) return false; // throw new Exception('Couldn\'t resize image!');
      }
      else
      {
          $dest=imagecreatetruecolor($maxwidth,$maxheight);
          // CHANGED EXCEPTION TO RETURN FALSE
          if (imagecopyresampled($dest,$this->imageHandle,0,0,0,0,$maxwidth,$maxheight,$width,$height)==false) return false; // throw new Exception('Couldn\'t resize image!') ;
      }
      $this->imageHandle=$dest;
  }
}

  function outputImage() {
    //function outputs all the headers and image data
   // var_dump($this);
    header('Content-type: '.image_type_to_mime_type($this->imageType));
    switch ($this->imageType) {
      case IMAGETYPE_GIF:
        return imagegif($this->imageHandle);
        break;
      case IMAGETYPE_JPEG:
        return imagejpeg($this->imageHandle,'',$this->jpegQuality);
        break;
      case IMAGETYPE_BMP:
        return imagebmp($this->imageHandle);
        break;
      case IMAGETYPE_PNG:
        return imagepng($this->imageHandle);
        break;
    }
  }
  }
  /*
  Usage example:
  try {
    $img= new Image('C:\logo4.jpg');
    $img->resizeImage(50,30);
    $img->outputImage();
  } catch ( Exception $e) {
    echo $e;
  }
  */
?>