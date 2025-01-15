# DropInSound
  
Hello and welcome to DropInSound!  
  
DropInSound is a light and simple software on premise to drag your stuff in the form of sound.  
   
DropInSound is released under GPLv3 license, it is supplied AS-IS and we do not take any responsibility for its misusage.  
  
First step, use the left side panel password and salt fields to create the hash to insert in the config file. Remember to manually set there also the salt value.  
  
As you are going to run DropInSound in the PHP process context, using a limited web server or phpfpm user, you must follow some simple directives for an optimal first setup:  
<ol>
<li>Check the permissions of your "data" folder in your web app private path; and set its path in the config file.</li>
<li>In the data path create a ".DI_history" and ".DI_captchahistory" files and give them the write permission.</li>
<li>Finish to setup the configuration file apporpriately, in the specific:</li>
<ul>
 <li>Configure the APP_USE and APP_CONTEXT appropriately.</li>
 <li>Configure the DISPLAY attributes as required.</li>
 <li>Configure the max history items as required (default: 1000).</li>	      
</ul>
</ol>
   
Feedback: <a href="mailto:posta@elettronica.lol" style="color:#e6d236;">posta@elettronica.lol</a>
  	   
## Screenshots  
	   
 ![DropInSound in action](/DIS_res/screenshot1.png)
