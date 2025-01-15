<?PHP

    require "../init.inc";

    $SOUND_PATH = APP_DATA_PATH . DIRECTORY_SEPARATOR . "sound";

    $fileName = filter_input(INPUT_GET, "fn")??"";
    $fileName = strip_tags($fileName);    
    
    $originalFilename = pathinfo($fileName, PATHINFO_FILENAME);
    $orioriFilename = explode("|", $originalFilename)[2];
    $originalFileExt = pathinfo($fileName, PATHINFO_EXTENSION);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    $filePath = $SOUND_PATH . DIRECTORY_SEPARATOR . $fileName;
       
    if (filesize($filePath) <= APP_FILE_MAX_SIZE) { 
      header("Content-Type: audio/" . $fileExt);
      header("Content-Disposition: attachment; filename=" . $orioriFilename . ".$fileExt");
      echo(file_get_contents($filePath));
      exit(0);
    } else {
      die("file size over app limits.");
    }
