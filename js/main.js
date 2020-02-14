/* var arr = document.getElementsByClassName("score-option");

// Register event for each select
for (var k=0; k < arr.length; k++) {
   arr[k].onchange = function(el) {
     // Get each select box
     for (var v=0; v < arr.length; v++) {
       if(v !== k) {
         var select = arr[v];
         //Get each option
         for(var i=0; i < select.childNodes.length; i++) {
           var option = select.childNodes[i];
           if(option.value === this.value) {
             select.removeChild(option);
           }
          }
       }
     }
   }
}
*/
