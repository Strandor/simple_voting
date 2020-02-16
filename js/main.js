var arr = document.getElementsByClassName("score-option");

// Register event for each select
for (var k=0; k < arr.length; k++) {
   arr[k].onchange = function(el) {
     // Get each select box
      for (var v=0; v < arr.length; v++) {
       var select = arr[v];
       if(select.getAttribute("name") != this.getAttribute("name") && k.value !== 0) {
       }
     }
   }
}
