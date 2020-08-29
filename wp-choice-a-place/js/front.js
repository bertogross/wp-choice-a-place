document.addEventListener("DOMContentLoaded", function() {
  
  /**
  * Function Click Event
  */
  if(document.getElementById("wcyp-toggle")){
    for (let i = 0; i < document.getElementsByClassName("wcyp-action").length; i++){
      document.getElementsByClassName("wcyp-action")[i].addEventListener("click", function(){
        var wcypToggleElement = document.getElementById("wcyp-toggle");
        setCookie('wcypActionToggle', 'show', 1);

        if(wcypToggleElement.classList.contains("hide")){
          wcypToggleElement.classList.remove("hide");
        }
        wcypToggleElement.classList.add("show");

      }, false);      
    }

  }


  /**
  * Function onChange Event
  */
  if(document.getElementById('wcyp-select')){

    document.getElementById('wcyp-select').addEventListener("change", function(){

      setCookie('wcypActionToggle', 'hide', 1);

      document.location.href=this.value;//redirect to URL

      verifyToggle();

    }, false);

  }


  verifyToggle();
  
  
}, false);

/**
* If cookie == hide don't show #wcyp-toggle
*/ 
function verifyToggle() {
  if(document.getElementById('wcyp-select')){
    var wcypToggleElement = document.getElementById("wcyp-toggle");
    
    if( !getCookie("wcypActionToggle") || getCookie("wcypActionToggle") !== 'hide'){
      
      wcypToggleElement.classList.add("show");
      setCookie('wcypActionToggle', 'show', 1);
     
    }else{
      
      if(wcypToggleElement.classList.contains("show")){
        wcypToggleElement.classList.remove("show");
        setCookie('wcypActionToggle', 'hide', 1);
        
      }
      
    }
  }
}


/**
* Function Set Cookie
*/ 
function setCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays*24*60*60*1000));
	var expires = "expires="+ d.toUTCString();
	document.cookie = cname + "=" + cvalue + "; SameSite=Lax; " + expires + ";path=/";
}

/**
* Function Get Cookie
*/ 
function getCookie(cname) {
	var name = cname + "=";
	var decodedCookie = decodeURIComponent(document.cookie);
	var ca = decodedCookie.split(';');
	for (let i = 0; i <ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' '){
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0){
			return c.substring(name.length, c.length);
		}
	}
	return "";
}
