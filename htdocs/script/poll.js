var JSONdata;
var questionNumber = 0;

var getJSON = function(url, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.responseType = 'json';
    xhr.onload = function() {
      var status = xhr.status;
      if (status === 200) {
        callback(null, xhr.response);
      } else {
        callback(status, xhr.response);
      }
    };
    xhr.send();
}
var printMessage = function(title, message) {
  document.getElementById("main").innerHTML += "<div class=\"title\"><h1>" + title + "</h1></div><hr><br><div class=\"values\"><div><p>" + message +"</p></div></div>";
}

var actionButton = function() {
  changeTitle(JSONdata["questions"][questionNumber]["question"]);
  changeValue();
  questionNumber++;
}

var setupQuestion = function() {
  printMessage("Hefja könnun", "Við erum að safna gögnum um *setja málefni*. Öll svör eru órekjanleg.");
  document.getElementById("main").innerHTML += "<div class=\"action\"><button id=\"actionButton\">Næsta</button></div>";
  document.getElementById("actionButton").addEventListener("click", actionButton);
  var div = document.createElement("div");
  div.innerHTML = "<h2 id=\"number\">0</h2><h3>/" + JSONdata["questions"].length + "</h3>";
  div.classList.add("questionNumber")
  document.body.prepend(div);
}

var getAPI = function() {
  getJSON('/api/GetPoll.php',
  function(err, data) {
    JSONdata = data;
    if (err !== null) {
        printMessage("Villa kom upp", "Því miður kom villa upp á meðan við reyndum að tengjast við þjón<br>Villa: " + err);
        return false;
    } else if(data["message"] != null) {
      printMessage(data["message"]["title"], data["message"]["desc"]);
      return false
    } else {
      setupQuestion();
    }
  })
}
var changeTitle = function(new_text) {
  var el = document.getElementsByClassName("title")[0];
  el.innerHTML = "<h1 class=\"title_transistion\">" + el.children[0].textContent + "</h1><h1 class=\"hidden\">" + new_text + "</h1><h1 class=\"title_transistion\">" + new_text + "</h1>";

  setTimeout(function() {
    el.children[0].remove();
    el.children[0].classList.toggle("hidden");
    el.children[1].remove();
  }, 250);
}

var createRadioButton = function(div) {
  var radio = document.createElement("div");
  for(var i=0; i <= 10; i++) {
    radio.innerHTML += '<label for="radio_' + i + '">' + i + '<input type="radio" id="radio_' + i + '" name="radio" value="' + i + '"><span class="checkmark"></span></label>';
  }
  radio.classList.add("radios");
  div.append(radio)
}

var changeValue = function() {
  var el = document.getElementsByClassName("values")[0];
  el.children[0].classList.add("transistion_before");
  var div = document.createElement("div");
  createRadioButton(div);
  div.classList.add("transistion_after")
  el.append(div);
  setTimeout(function() {
    el.children[0].remove();
    el.children[0].classList.remove("transistion_after");
  }, 500);
}

var removeLoader = function() {
  document.getElementById("loader").remove();
}

window.onload = function(e) {
  getAPI();

  removeLoader();
}
