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

Login with the password for the admin view.

For any need of software additions, plugins and improvements please write to <a href="mailto:info@5mode.com">info@5mode.com</a>  

To help please donate by clicking <a href="https://gaox.io/l/dona1">https://gaox.io/l/dona1</a> and filling the form.  
    
## Screenshots  
	   
 ![DropInSound in action](/Public/DIS_res/screenshot1.png)

Feedback: <a href="mailto:code@gaox.io" style="color:#e6d236;">code@gaox.io</a>
