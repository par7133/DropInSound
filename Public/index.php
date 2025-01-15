<?php

/**
 * Copyright 2021, 2026 5 Mode
 *
 * This file is part of DropInSound.
 *
 * DropInSound is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * DropInSound is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.  
 * 
 * You should have received a copy of the GNU General Public License
 * along with DropInSound. If not, see <https://www.gnu.org/licenses/>.
 *
 * index.php
 * 
 * DropInSound home page.
 *
 * @author Daniele Bonini <my25mb@aol.com>
 * @copyrights (c) 2021, 2026, 5 Mode      
 */
 
 require "init.inc";
 
 $contextType = PUBLIC_CONTEXT_TYPE;
  
 $signHistory = [];
 $cmd = PHP_STR;
 $opt = PHP_STR;
 $param1 = PHP_STR;
 $param2 = PHP_STR;
 $param3 = PHP_STR;
   
 $curLocale = APP_LOCALE;
 $lastSign = PHP_STR;

 $msgSign = filter_input(INPUT_POST, "msg-sign")??"";
 $msgSign = strip_tags($msgSign);

 $q = filter_input(INPUT_POST, "q")??"";
 $q = strip_tags($q);

 $curPath = APP_DATA_PATH;
 chdir($curPath);

 $gdate = date("Y-m-d");
 $gtime = date("H:i:s");

 $signHistory = file($curPath . DIRECTORY_SEPARATOR . ".DI_history");
 $signHistoryDateTime = $signHistory;
 foreach($signHistoryDateTime as &$el) {
   $el = left($el,21);
 }
 $captchaHistory = file($curPath . DIRECTORY_SEPARATOR . ".DI_captchahistory");

 function showHistory() {
   global $signHistory;
   global $curPath;
   global $CONFIG;
   global $curLocale;
   global $LOCALE;
   global $lastSign;
   global $password;
   global $contextType;
   global $q;
   
   $signHistoryCopy = $signHistory;
   
   rsort($signHistoryCopy);

   echo("<div id='events'>");
   
   $m = 0;
   foreach($signHistoryCopy as $val) {
     
     $val = rtrim($val, "\n");
     
     $mydate = PHP_STR; 
     $mytime = PHP_STR;
     $mydesc = PHP_STR;
     $myflag = PHP_STR;
     
     $aFields = explode(PHP_PIPE . PHP_PIPE. PHP_PIPE, $val);

     $mydate = $aFields[0]??PHP_STR;
     $mytime = $aFields[1]??PHP_STR;
     $myid = $mydate . PHP_PIPE . PHP_PIPE . PHP_PIPE . $mytime;
     $oriFilename = $aFields[2];
     $mytitle = explode("|", $aFields[2])[2]??PHP_STR;
     if ($q!=="") {
       if (mb_stripos($mytitle, $q) === false) {
         continue;
       }
     }

     $myflag = $aFields[3]??PHP_STR;
     
     if ($mydate==PHP_STR && $mytitle==PHP_STR) {
       continue;
     }
     
     // If I'm in admin
     if ($contextType === PERSONAL_CONTEXT_TYPE) {
       
       $adminFnc = PHP_STR;
       if ($myflag === "u") {
         $adminFnc = "<a href='#' onclick=\"confSign('" . $myid . "')\"><img src='/DIS_res/confirm.png' style='width:36px;'></a>";
       } else {
         $adminFnc = "<a href='#' onclick=\"delSign('" . $myid . "')\"><img src='/DIS_res/del.png' style='width:36px;'></a>";
       }    
       
       echo("<table class='table-event' align='center'>");
       echo("<tr>");
       echo("<td class='td-data-date'>");
       echo("<span class='data-date' style='font-family:".DISPLAY_DATE_FONT.";'>".$mydate."</span>");
       echo("</td>");
       echo("<td class='td-data-time'>");
       echo("<span class='data-time' style='font-family:".DISPLAY_DATE_FONT.";'>".$mytime."</span>");
       echo("</td>");
       echo("<td class='td-data-title'>");
       echo("<span class='data-title'><a href='/open/?fn=".$oriFilename."'>".$mytitle."</a></span>");
       echo("</td>");
       echo("<td class='td-admin'>");
       echo($adminFnc);
       echo("</td>");
       echo("</tr>");   
       echo("</table>");
       
     // If I'm not in admin
     } else {   
       
       if ($myflag !== "u") {

         echo("<div id='res".$m."' class='res'>");
         echo("<span class='this-title'><a href='/open/?fn=".$oriFilename."' class='this-title'>".$mytitle."</a></span>");
         if (DISPLAY_SHOW_DATETIME) {
            echo("<br>");
            echo("<span class='data-date' style='font-family:".DISPLAY_DATE_FONT.";'>".$mydate."</span>");
            echo("&nbsp;");
            echo("<span class='data-time' style='font-family:".DISPLAY_DATE_FONT.";'>".$mytime."</span>");
         }            
         echo("</span>");
         echo("</div>");

       }  
     }
     
     $m++;
   }

   if (empty($signHistoryCopy) || $m === 0) {
     echo("No sound found!");
   }  
         
   echo("</div>");
 }

 function updateHistory(&$update, $maxItems) {
   global $signHistory;
   global $curPath;
   
   // Making enough space in $signHistory for the update..
   $shift = (count($signHistory) + count($update)) - $maxItems;
   if ($shift > 0) {
     $signHistory = array_slice($signHistory, $shift, $maxItems); 
   }		  
   // Adding $signHistory update..
   if (count($update) > $maxItems) {
     $beginUpd = count($update) - ($maxItems-1);
   } else {
	   $beginUpd = 0;
   }	        
   $update = array_slice($update, $beginUpd, $maxItems); 
   foreach($update as $val) {  
	   $signHistory[] = $val;   
   }
 
   // Writing out $signHistory on disk..
   $filepath = $curPath . DIRECTORY_SEPARATOR . ".DI_history";
   file_put_contents($filepath, implode('', $signHistory));	 
 }


 function updatecaptchaHistory(&$update) {
   global $captchaHistory;
   global $curPath;
   	        
   foreach($update as $val) {  
     $captchaHistory[] = $val;     
   }
 
   // Writing out $captchaHistory on disk..
   $filepath = $curPath . DIRECTORY_SEPARATOR . ".DI_captchahistory";
   file_put_contents($filepath, implode('', $captchaHistory));	 
 }

 function upload() {

   global $curPath;
   global $signHistory;
   global $signHistoryDateTime;
   global $msgSign;

   //$t = filter_input(INPUT_POST, "t")??"";
   //$t = strip_tags($t);

   // Checking for repeated upload cause ie. caching prb..
   //$duplicateMsgs = glob($picPath . DIRECTORY_SEPARATOR . date("Ymd-H") . "*-$msgSign*.*");
   //if (!empty($duplicateMsgs)) {
   //  echo("WARNING: destination already exists");
   //  return;
   //}	   
   if (in_array($msgSign,$signHistoryDateTime)) {
       echo("WARNING: destination already exists");
       return;
   }

  if (!empty($_FILES['files']['tmp_name'][0]) ||  !empty($_FILES['filesdd']['tmp_name'][0])) {
      
     $uploads = (array)fixMultipleFileUpload($_FILES['files']);
     if ($uploads[0]['error'] === PHP_UPLOAD_ERR_NO_FILE) {
       $uploads = (array)fixMultipleFileUpload($_FILES['filesdd']);
     }   
     
     //no file uploaded
     if ($uploads[0]['error'] === PHP_UPLOAD_ERR_NO_FILE) {
       echo("WARNING: No file uploaded.");
       return;
     } 

     $google = "abcdefghijklmnopqrstuvwxyz";
     if (count($uploads)>strlen($google)) {
       echo("WARNING: Too many uploaded files."); 
       return;
     }

     $i=1;
     foreach($uploads as &$upload) {
		
       switch ($upload['error']) {
       case PHP_UPLOAD_ERR_OK:
         break;
       case PHP_UPLOAD_ERR_NO_FILE:
         echo("WARNING: One or more uploaded files are missing.");
         return;
       case PHP_UPLOAD_ERR_INI_SIZE:
         echo("WARNING: File exceeded INI size limit.");
         return;
       case PHP_UPLOAD_ERR_FORM_SIZE:
         echo("WARNING: File exceeded form size limit.");
         return;
       case PHP_UPLOAD_ERR_PARTIAL:
         echo("WARNING: File only partially uploaded.");
         return;
       case PHP_UPLOAD_ERR_NO_TMP_DIR:
         echo("WARNING: TMP dir doesn't exist.");
         return;
       case PHP_UPLOAD_ERR_CANT_WRITE:
         echo("WARNING: Failed to write to the disk.");
         return;
       case PHP_UPLOAD_ERR_EXTENSION:
         echo("WARNING: A PHP extension stopped the file upload.");
         return;
       default:
         echo("WARNING: Unexpected error happened.");
         return;
       }
      
       if (!is_uploaded_file($upload['tmp_name'])) {
         echo("WARNING: One or more file have not been uploaded.");
         return;
       }
      
       // name	 
       $name = (string)substr((string)filter_var($upload['name']), 0, 255);
       if ($name == PHP_STR) {
         echo("WARNING: Invalid file name: " . $name);
         return;
       } 
       $upload['name'] = $name;
       
       // fileType
       $fileType = substr((string)filter_var($upload['type']), 0, 30);
       $upload['type'] = $fileType;	 
       
       // tmp_name
       $tmp_name = substr((string)filter_var($upload['tmp_name']), 0, 300);
       if ($tmp_name == PHP_STR || !file_exists($tmp_name)) {
         echo("WARNING: Invalid file temp path: " . $tmp_name);
         return;
       } 
       $upload['tmp_name'] = $tmp_name;
       
       //size
       $size = substr((string)filter_var($upload['size'], FILTER_SANITIZE_NUMBER_INT), 0, 12);
       if ($size == "") {
         echo("WARNING: Invalid file size.");
         return;
       } 
       $upload["size"] = $size;

       $tmpFullPath = $upload["tmp_name"];
       
       $originalFilename = pathinfo($name, PATHINFO_FILENAME);
       $originalFileExt = pathinfo($name, PATHINFO_EXTENSION);
       $fileExt = strtolower(pathinfo($name, PATHINFO_EXTENSION));

       if ($fileExt != "mp3" && $fileExt != "wav") {
         echo("WARNING: Invalid file extension.");
         return;
       } 

       $date = date("Y-m-d");
       $time = date("H:i:s");

       $destPath = APP_DATA_PATH . DIRECTORY_SEPARATOR . "sound"; 
       $destFilename = $date . "|" . $time . "|" . $name;

       copy($tmpFullPath, $destPath . DIRECTORY_SEPARATOR . $destFilename);

       // Updating history..
       $output = [];
       
       $output[] = $date . PHP_PIPE . PHP_PIPE .  PHP_PIPE . $time  . PHP_PIPE . PHP_PIPE . PHP_PIPE  . $destFilename . PHP_PIPE . PHP_PIPE . PHP_PIPE  ."u\n";   
       updateHistory($output, HISTORY_MAX_ITEMS);
    
       // Cleaning up..
      
       // Delete the tmp file..
       unlink($tmpFullPath); 
       
       $i++;
        
     }	 
      echo("<script>");
      echo("window.open('/?up=1','_self')");
      echo("</script>");   
      exit;
   }
 }
upload();

 function parseCommand() {
   global $command;
   global $cmd;
   global $opt;
   global $param1;
   global $param2;
   global $param3;
   
   //echo($command ."<br>");
   $str = trim($command);
   
   $ipos = stripos($str, PHP_SPACE);
   if ($ipos > 0) {
     $cmd = left($str, $ipos);
     $str = substr($str, $ipos+1);
   } else {
     $cmd = $str;
      return;
   }	     
   
   if (left($str, 1) === "-") {
	 $ipos = stripos($str, PHP_SPACE);
	 if ($ipos > 0) {
	   $opt = left($str, $ipos);
	   $str = substr($str, $ipos+1);
	 } else {
	   $opt = $str;
	   return;
	 }	     
   }
   
   if (left($str, 1) === "'") {
     $ipos = stripos($str, "'", 1);
     if ($ipos > 0) {
       $param1 = substr($str, 0, $ipos+1);
       $str = substr($str, $ipos+1);
     } else {
       $param1 = $str;
       return;
     }  
   } else {   
     $ipos = stripos($str, PHP_SPACE);
     if ($ipos > 0) {
       $param1 = left($str, $ipos);
       $str = substr($str, $ipos+1);
     } else {
       $param1 = $str;
       return;
     }	     
   }
     
   $ipos = stripos($str, PHP_SPACE);
   if ($ipos > 0) {
     $param2 = left($str, $ipos);
     $str = substr($str, $ipos+1);
   } else {
	 $param2 = $str;
	 return;
   }
   
   $ipos = stripos($str, PHP_SPACE);
   if ($ipos > 0) {
     $param3 = left($str, $ipos);
     $str = substr($str, $ipos+1);
   } else {
	 $param3 = $str;
	 return;
   }	     
 	     
 }

 function signParamValidation() {
   
  global $opt;
	global $param1;
	global $param2; 
	global $param3; 
  global $date;
  global $hour;
  global $min;
  global $desc;
  global $captchacount; 
  global $captchasign;
  global $captchaHistory;
   
  //opt!=""
  if ($opt!==PHP_STR) {
	  echo("WARNING: invalid options<br>");	
    return false;
  }	
	//param1==""  
	if ($param1!==PHP_STR) {
	  echo("WARNING: invalid parameters<br>");	
    return false;
  }
	//param2==""
	if ($param2!==PHP_STR) {
    echo("WARNING: invalid parameters<br>");
    return false;
  }
  //param3==""
  if ($param3!==PHP_STR) {
    echo("WARNING: invalid parameters<br>");
    return false;
  }

  //date!=""
  if ($date===PHP_STR || strlen($date)<4) {
    //echo("WARNING: invalid date<br>");
    return false;
  }  

/*
  if (APP_MODE == CALENDAR_MODE_TYPE) {
    if ($hour===PHP_STR || strlen($hour)>2) {
      //echo("WARNING: invalid hour<br>");
      return false;
    }  
    if ($min===PHP_STR || strlen($min)>2) {
      //echo("WARNING: invalid min<br>");
      return false;
    }  
  }
*/
  
  //place!=""
  if ($desc===PHP_STR || strlen($desc)<4) {
    //echo("WARNING: invalid desc<br>");
    return false;
  }  
  
  $rescaptcha1=$captchacount>=4;
  $rescaptcha2=count(array_filter($captchaHistory, "odd")) > (APP_MAX_FROM_IP - 1);
  //if ($rescaptcha1) {
  //  echo("WARNING: captcha expired #1<br>");
  //}  
  
  //if ($rescaptcha2) {
  //  echo("WARNING: captcha expired #2<br>");
  //}  
  
  ///if ($rescaptcha1 || $rescaptcha2) {
  
  //if ($rescaptcha1) {
  //  return false;
  //}  
  
  return true;
 } 


 function odd($val) {
   
   global $captchasign;
   
   return rtrim($val,"\n") == $captchasign;   
 }   
 
  
 function myExecSignCommanddis() {
   
   global $date;
   global $hour;
   global $min;
   global $desc;
   global $curPath;
   global $lastMessage;
   global $captchacount;
   global $captchasign;
   global $captchaHistory;
   
   /*
   if (APP_MODE == EVENTS_MODE_TYPE) {
     $newSign = HTMLencodeF($date,false) . "|" . HTMLencodeF($desc,false) . "|u";
   } else {  
     $newSign = HTMLencodeF($date,false) . "|" . HTMLencodeF($hour.":".((strlen($min)==1)?"0".$min:$min)) . "|" . HTMLencodeF($desc,false) . "|u";
   }
   */
   //echo("array_filter=".count(array_filter($captchaHistory, "odd"))."<br>");
   //echo("new_sign?=".((hash("sha256", $newSign . APP_SALT, false) !== $lastMessage)?"true":"false")."<br>");

   if (hash("sha256", $newSign . APP_SALT, false) !== $lastMessage) {

     // Updating message history..
     $output = [];
     $output[] = $newSign . "\n";
     updateHistory($output, HISTORY_MAX_ITEMS);

     // Updating captcha history..
     $output = [];
     $output[] = $captchasign . "\n";
     updatecaptchaHistory($output);

     $lastMessage = hash("sha256", $newSign . APP_SALT, false);
   }
   
 }  


 function confParamValidation() {
   
  global $opt;
	global $param1;
	global $param2; 
	global $param3; 
  global $signHistory;
  global $signHistoryDateTime;
     
  //opt!=""
  if ($opt!==PHP_STR) {
	  echo("WARNING: invalid options<br>");	
    return false;
  }	
	
  $myval = trim($param1,"'");
  
  //param1!=""  
  if ($myval===PHP_STR) {
    echo("WARNING: invalid parameters<br>");	
    return false;
  }
  //param1 in $signHistory  
  //if (!in_array($myval."\n",$signHistory)) {
  if (!in_array($myval,$signHistoryDateTime)) {
    echo("WARNING: invalid parameters<br>");	
    return false;
  }  
  
  //param2==""
  if ($param2!==PHP_STR) {
    echo("WARNING: invalid parameters<br>");
    return false;
  }
  //param3==""
  if ($param3!==PHP_STR) {
    echo("WARNING: invalid parameters<br>");
    return false;
  }
  
  return true;

 } 

/*
 function myExecConfSignCommand() { 
   
   global $param1;
   global $signHistory;
   global $curPath;
   
   $mysign = trim($param1,"'");
   
   if ($signHistory) {
     
     //echo("inside myExecConfSignCommand()");
     
     $newval = left($mysign, strlen($mysign)-3) . PHP_PIPE. PHP_PIPE ."v"; 
     
     $key = array_search($mysign."\n", $signHistory);
     if ($key !== false) { 
       $signHistory[$key] = $newval . "\n"; 
       
       // Writing out $signHistory on disk..
       $filepath = $curPath . DIRECTORY_SEPARATOR . ".DI_history";
       file_put_contents($filepath, implode('', $signHistory));	        
     }
   }  
 }
 */

 function myExecConfSignCommand() { 
   
   global $param1;
   global $signHistory;
   global $curPath;
   global $signHistoryDateTime;
   
   $mysign = trim($param1,"'");
   
   if ($signHistory) {
     
     //echo("inside myExecConfSignCommand()");
     
     //$newval = left($mysign, strlen($mysign)-3) . PHP_PIPE . PHP_PIPE . PHP_PIPE  . "v"; 
     
     $key = array_search($mysign, $signHistoryDateTime);
     if ($key !== false) { 
     
       $newval = left($signHistory[$key], strlen($signHistory[$key])-5) . PHP_PIPE . PHP_PIPE . PHP_PIPE  ."v";
     
       $signHistory[$key] = $newval . "\n"; 
       
       // Writing out $signHistory on disk..
       $filepath = $curPath . DIRECTORY_SEPARATOR . ".DI_history";
       file_put_contents($filepath, implode('', $signHistory));	        
     }
   }  
 }

 function delParamValidation() {
   
  global $opt;
	global $param1;
	global $param2; 
	global $param3; 
  global $signHistory;
  global $signHistoryDateTime;
   
  //opt!=""
  if ($opt!==PHP_STR) {
	  echo("WARNING: invalid options<br>");	
    return false;
  }	
	
  $myval = trim($param1,"'");
  
  //param1!=""  
 if ($myval===PHP_STR) {
    echo("WARNING: invalid parameters<br>");	
    return false;
  }
  //param1 in $signHistory
  //if (!in_array($myval."\n",$signHistory)) {
  if (!in_array($myval,$signHistoryDateTime)) {
    echo("WARNING: invalid parameters<br>");	
    return false;
  }  
  
  //param2==""
  if ($param2!==PHP_STR) {
    echo("WARNING: invalid parameters<br>");
    return false;
  }
  //param3==""
  if ($param3!==PHP_STR) {
    echo("WARNING: invalid parameters<br>");
    return false;
  }
  
  return true;

 } 

/*
 function myExecDelSignCommand() { 
   
   global $param1;
   global $signHistory;
   global $curPath;
   
   $mysign = trim($param1,"'");
   
   if ($signHistory) {
     
     //echo("inside myExecDelSignCommand()");
     
     $newval = left($mysign, strlen($mysign)-2) . "|u"; 
     
     $key = array_search($mysign."\n", $signHistory);
     if ($key !== false) { 
       $signHistory[$key] = $newval . "\n"; 
       
       // Writing out $signHistory on disk..
       $filepath = $curPath . DIRECTORY_SEPARATOR . ".DI_history";
       file_put_contents($filepath, implode('', $signHistory));	        
     }
   }  
 }
*/

 function myExecDelSignCommand() { 
   
   global $param1;
   global $signHistory;
   global $curPath;
   global $signHistoryDateTime;
     
   $mysign = trim($param1,"'");
   
   if ($signHistory) {
     
     //echo("inside myExecDelSignCommand()");
     
     //$newval = left($mysign, strlen($mysign)-2) . "|u"; 
     
     $key = array_search($mysign, $signHistoryDateTime);
     if ($key !== false) { 
     
       $newval = left($signHistory[$key], strlen($signHistory[$key])-5) . PHP_PIPE . PHP_PIPE . PHP_PIPE  ."u";
     
       $signHistory[$key] = $newval . "\n"; 
       
       // Writing out $signHistory on disk..
       $filepath = $curPath . DIRECTORY_SEPARATOR . ".DI_history";
       file_put_contents($filepath, implode('', $signHistory));	        
     }
   }  
 }


 $password = filter_input(INPUT_POST, "Password")??"";
 $password = strip_tags($password);
 if ($password==PHP_STR) {
   $password = filter_input(INPUT_POST, "Password2")??"";
   $password = strip_tags($password);
 }  
 $command = filter_input(INPUT_POST, "CommandLine")??"";
 $command = strip_tags($command);
 
 //$pwd = filter_input(INPUT_POST, "pwd"); 
 $hideSplash = filter_input(INPUT_POST, "hideSplash")??"";
 $hideSplash = strip_tags($hideSplash);
 $hideHCSplash = filter_input(INPUT_POST, "hideHCSplash")??"";
 $hideHCSplash = strip_tags($hideHCSplash);

 $date = filter_input(INPUT_POST, "date")??"";
 $date = strip_tags($date);
 $hour = filter_input(INPUT_POST, "hour")??"";
 $hour = strip_tags($hour);
 $min = filter_input(INPUT_POST, "min")??"";
 $min = strip_tags($min);
 $desc = filter_input(INPUT_POST, "desc")??"";
 $desc = strip_tags($desc);

 $captchasign = hash("sha256", $_SERVER["REMOTE_ADDR"] . date("Y") . APP_SALT, false);
 
 $lastMessage = filter_input(INPUT_POST, "last_message")??"";
 $lastMessage = strip_tags($lastMessage);
 $totsigns = count($signHistory);
 //print_r($totsigns);
 //exit(0);
 if ($totsigns > 0) {
   $lastMessage = hash("sha256", rtrim($signHistory[$totsigns-1],"\n") . APP_SALT, false);
 }   

 $captchacount = (int)filter_input(INPUT_POST, "captcha_count")??"";
 $captchacount = strip_tags($captchacount);
 //if ($captchacount === 0) {
 //  $captchacount = 1;
 //}  

 if ($password !== PHP_STR) {	
	$hash = hash("sha256", $password . APP_SALT, false);

	if ($hash !== APP_HASH) {
	  $password=PHP_STR;	
    }	 
 } 
  
 parseCommand($command);
 //echo("cmd=" . $cmd . "<br>");
 //echo("opt=" . $opt . "<br>");
 //echo("param1=" . $param1 . "<br>");
 //echo("param2=" . $param2 . "<br>");
 
 
 if ($password !== PHP_STR) {
   
   if (mb_stripos(CMDLINE_VALIDCMDS, "|" . $command . "|")) {
 
     if ($cmd === "sign") {
       $captchacount = $captchacount + 1;
       if (signParamValidation()) {
         myExecSignCommand();
       }	     	     
     } else if ($command === "refresh") {
       // refreshing Msg Board..
     }
 
   } else if (mb_stripos(CMDLINE_VALIDCMDS, "|" . $cmd . "|")) {
     
     if ($cmd === "del") {
       if (delParamValidation()) {
         myExecDelSignCommand();
       }	     
     } else if ($cmd === "conf") {
       if (confParamValidation()) {
         myExecConfSignCommand();
       }	     	     
     }       
   } else {
     
   }
   
   $contextType = PERSONAL_CONTEXT_TYPE;
      
 } else {
 
  /*
   if (mb_stripos(CMDLINE_VALIDCMDS, "|" . $command . "|")) {
     if ($cmd === "sign") {
       $captchacount = $captchacount + 1;
       if (signParamValidation()) {
         myExecSignCommand();
       }	
     }   
   }*/
 }
 
?>

<!DOCTYPE html>
<head>
	
  <meta charset="UTF-8"/>
  
  <meta name="viewport" content="width=device-width, initial-scale=0.8"/>
  
<!--
    Copyright 2021, 2026 5 Mode

    This file is part of DropInSound.

    DropInSound is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    DropInSound is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with DropInSound. If not, see <https://www.gnu.org/licenses/>.
 -->
  
    
  <title><?php echo(APP_TITLE); ?></title>
	
  <link rel="shortcut icon" href="/favicon.ico?v=<?php echo(time()); ?>>" />
    
  <meta name="description" content="<?php echo(APP_DESCRIPTION); ?>"/>
  <meta name="keywords" content="<?php echo(APP_KEYWORDS); ?>"/>
  <meta name="author" content="5 Mode"/> 
  <meta name="robots" content="index,follow"/>
  
  <script src="/DIS_js/jquery-3.6.0.min.js" type="text/javascript"></script>
  <script src="/DIS_js/common.js" type="text/javascript"></script>
  <script src="/DIS_js/bootstrap.min.js" type="text/javascript"></script>
  
  <script src="/DIS_js/index-js.php" type="text/javascript" defer></script>
  
  <link href="/DIS_css/bootstrap.min.css" type="text/css" rel="stylesheet">
  <link href="/DIS_css/style.css?r=<?PHP echo(time());?>" type="text/css" rel="stylesheet">
  
<style>
@import url('https://fonts.googleapis.com/css2?family=<?php echo(str_ireplace(" ","+",DISPLAY_DATE_FONT));?>');
</style>
     
</head>
<body>

<?php if (file_exists(APP_PATH . DIRECTORY_SEPARATOR . "jscheck.html")): ?>
<?php include("jscheck.html"); ?> 
<?php endif; ?>

<form id="frmDI" method="POST" action="/" target="_self" enctype="multipart/form-data">

<?php if(APP_USE === "PRIVATE"): ?>
<div class="header">
   <a id="ahome" href="http://dropin.5mode-foss.eu" target="_blank" style="color:black; text-decoration: none;"><img id="logo-hmm" src="/DIS_res/DIlogo.png" style="width:32px;">&nbsp;DropIn</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="agithub" href="https://github.com/par7133/DropIn" style="color:#000000"><span style="color:#119fe2">on</span> github</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="afeedback" href="mailto:posta@elettronica.lol" style="color:#000000"><span style="color:#119fe2">for</span> feedback</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="asupport" href="tel:+39-378-0812391" style="font-size:13px;background-color:#15c60b;border:2px solid #15c60b;color:black;height:27px;text-decoration:none;">&nbsp;&nbsp;get support&nbsp;&nbsp;</a><div id="pwd2" style="float:right;position:relative;top:+13px;display:none"><input type="password" id="Password2" name="Password2" placeholder="password" style="font-size:13px; background:#393939; color:#ffffff; width: 125px; border-radius:3px;" value="" autocomplete="off"></div>
</div>
<?php else: ?>
<div class="header2">
   <?php echo(APP_CUSTOM_HEADER); ?>
</div>
<?php endif; ?>

<div style="clear:both;margin:auto">&nbsp;</div>

<?php
  $callSideBarTOP = 1; 
  if(APP_USE === "PRIVATE") {
    $callSideBarTOP = 65;   
  }    
?>

<div id="call-sidebar" style="top:<?php echo($callSideBarTOP);?>px;">
    &nbsp;
</div>

<div id="sidebar">
    
    <button id="sidebar-close" type="button" class="close" aria-label="Close" onclick="closeSideBar();">
      <span aria-hidden="true">&times;</span>
    </button>
    
    <br><br>
    <img id="genius" src="/DIS_res/HLgenius.png" alt="HL Genius" title="HL Genius">
    &nbsp;<br><br>
    <div style="text-align:left;white-space:nowrap;">
    &nbsp;<input id="Password" name="Password" class="sidebarcontrol" type="password" placeholder="password" value="<?php echo($password);?>" autocomplete="off">&nbsp;<input type="submit" class="sidebarcontrol" value="<?php echo(getResource("Go", $curLocale));?>" style="width:24%; height: 25px;background-color:lightgray;color:#000000;"><br>
    &nbsp;<input id="Salt"  class="sidebarcontrol" type="text" placeholder="salt" autocomplete="off"><br>
    <div style="text-align:center;">
    <a id="butHashMe" href="#" onclick="showEncodedPassword();"><?php echo(getResource("Hash Me", $curLocale));?>!</a>     
    
    <br><br><br>

    </div>
    </div>
</div>

<div id="content-bar">

  <div style="width:100%; padding: 8px; text-align:center; font-size:26px; border:0px solid red;">
   
    <br>
  
    <?php if (APP_DEFAULT_CONTEXT === "PRIVATE"): ?>
     
     <div id="content-header">
    
      <?php if ($contextType === PUBLIC_CONTEXT_TYPE): ?>
     
        <div id="guest-msg"><h1><?php echo(APP_GUEST_MSG??"&nbsp;"); ?></h1></div>
      
      <?php else: ?>
      
        <div class="dragover" dropzone="copy" style="min-width:630px;">

        <div id="drop-img">
        <div id="fireupload" onclick="$('#files').click()">
        <img src="/DIS_res/dnd2.gif">
        </div>
        </div>

        <div id="template-img">
        <div id="templated">
        <a href="/template.mp3"><img src="/DIS_res/template.png"></a>
        </div>
        </div>
                                
        <input id="files" name="files[]" type="file" accept=".txt" style="display:none;" multiple>
        
        <input type="hidden" id="t" name="t"> 
        
        <div id="welcome-msg"><h1><span id="page-title"><?php echo(APP_WELCOME_MSG??"&nbsp;"); ?></span></h1></div>
                      
        <div style="clear:both;margin:auto;"><br></div>
     
        <input type="hidden" name="msg-sign" value="<?php echo($gdate . PHP_PIPE . PHP_PIPE . PHP_PIPE  . $gtime); ?>"> 
        
        <hr>
        
        <br>
        
        </div>
        
        <?php showHistory(); ?>

      <?php endif; ?>
    
    <?php else: ?>
    
      <div id="content-header">  
        
      <?php if ($contextType === PUBLIC_CONTEXT_TYPE): ?>

        <div class="dragover" dropzone="copy" style="min-width:630px;">

        <div id="drop-img">
        <div id="fireupload" onclick="$('#files').click()">
        <img src="/DIS_res/dnd2.gif">
        </div>
        </div>

        <div id="template-img">
        <div id="templated">
        <a href="/template.mp3"><img src="/DIS_res/template.png"></a>
        </div>
        </div>
                                
        <input id="files" name="files[]" type="file" accept=".txt" style="display:none;" multiple>
        
        <input type="hidden" id="t" name="t"> 
        
        <div id="welcome-msg"><br><h1><span id="page-title"><?php echo(APP_WELCOME_MSG??"&nbsp;"); ?></span></h1></div>
                      
        <div style="clear:both;margin:auto;"><br></div>
     
        <input type="hidden" name="msg-sign" value="<?php echo($gdate . PHP_PIPE . PHP_PIPE . PHP_PIPE  . $gtime); ?>"> 
     
        <hr>

        <div style="clear:both;float:right;margin-right:5%;margin-bottom:30px;">
        
        <input id="txtSearch" name="q" type="text" class="search-control" value="<?PHP echo($q); ?>">
   
        </div>              
                      
        </div>

        <br><br><br><br>

        <?php showHistory(); ?>

      <?php else: ?>

        <div class="dragover" dropzone="copy" style="min-width:630px;">

        <div id="drop-img">
        <div id="fireupload" onclick="$('#files').click()">
        <img src="/DIS_res/dnd2.gif">
        </div>
        </div>

        <div id="template-img">
        <div id="templated">
        <a href="/template.mp3"><img src="/DIS_res/template.png"></a>
        </div>
        </div>
                                
        <input id="files" name="files[]" type="file" accept=".txt" style="display:none;" multiple>
        
        <input type="hidden" id="t" name="t"> 
        
        <div id="welcome-msg"><h1><span id="page-title"><?php echo(APP_WELCOME_MSG??"&nbsp;"); ?></span></h1></div>
                      
        <div style="clear:both;margin:auto;"><br></div>
     
        <input type="hidden" name="msg-sign" value="<?php echo($gdate . PHP_PIPE . PHP_PIPE . PHP_PIPE  . $gtime); ?>"> 
        
        <hr>
        
        <br>
        
        </div>
        
        <?php showHistory(); ?>

      <?php endif; ?>
    
    <?php endif; ?>
    
    <div style="clear:both;margin:auto;"><br><br><br><br><br></div>

    <?php if(APP_USE === "BUSINESS"): ?>    
    <div id="footer2">
      <a id="ahome" href="http://dropinsound.5mode-foss.eu" target="_blank" style="color:black;"><img id="logo-hl" src="/DIS_res/DIlogo.png">Powered by DropInSound</a>
    </div>
    <?php endif; ?>&nbsp;
       
  </div>     

</div>

<input type="hidden" id="CommandLine" name="CommandLine">
<input type="hidden" name="hideSplash" value="<?php echo($hideSplash); ?>">
<input type="hidden" name="hideHCSplash" value="1">
<input type="hidden" name="captcha_count" value="<?php echo($captchacount); ?>">
<input type="hidden" name="last_message" value="<?php echo($lastMessage); ?>">

</form>

<div class="footer">
<div id="footerCont">&nbsp;</div>
<div id="footer"><span style="background:#FFFFFF;opacity:1.0;margin-right:10px;">&nbsp;&nbsp;A <a href="http://5mode.com">5 Mode</a> project <span class="no-sm">and <a href="http://wysiwyg.systems">WYSIWYG</a> system</span>. CC&nbsp;&nbsp;</span></div>	
</div>

<?php if (file_exists(APP_PATH . DIRECTORY_SEPARATOR . "skinner.html")): ?>
<?php include("skinner.html"); ?> 
<?php endif; ?>

<?php if (file_exists(APP_PATH . DIRECTORY_SEPARATOR . "metrics.html")): ?>
<?php include("metrics.html"); ?> 
<?php endif; ?>

</body>
</html>
