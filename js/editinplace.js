/*
 * Another In Place Editor - a jQuery edit in place plugin
 *
 * Copyright (c) 2009 Dave Hauenstein
 *
 * License:
 * This source file is subject to the BSD license bundled with this package.
 * Available online: {@link http://www.opensource.org/licenses/bsd-license.php}
 * If you did not receive a copy of the license, and are unable to obtain it,
 * email davehauenstein@gmail.com,
 * and I will send you a copy.
 *
 * Project home:
 * http://code.google.com/p/jquery-in-place-editor/
 *
 */
$(document).ready(function(){
		
    // This example only specifies a URL to handle the POST request to
    // the server, and tells the script to show the save / cancel buttons
    $(".phrase").editInPlace({        
		url: "save.php",        
		params: ip_address = "ip=" + user_ip,
		show_buttons: true,		
		success: function (new_html){
			$("#body").prepend('<div class="rule"><span class="heart">&hearts;</span>&nbsp;&nbsp;<span class="writing">' + new_html +'</span>&nbsp;<span class="stamp">1 sec ago</span></div>');            
            return(new_html);
        } 
	}); 
});