<html>
<head>
  <title>piCoffee</title>
  <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
  <!-- Compiled and minified CSS -->
  <link rel="stylesheet" href="materialize/css/materialize.min.css">
  <link href="https://fonts.googleapis.com/css?family=Asap" rel="stylesheet">
  <link rel="shortcut icon" type="image/png" href="img/favicon.png"/>
  <!-- Compiled and minified JavaScript -->
  <script src="materialize/js/materialize.min.js"></script>
  <script src="cronstrue/dist/cronstrue.min.js" type="text/javascript"></script>
  <script src="cronstrue/dist/cronstrue-i18n.min.js" type="text/javascript"></script>
   <!--Import Google Icon Font-->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <!--Let browser know website is optimized for mobile-->
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <script>
  $( document ).ready(function(){
    $(".button-collapse").sideNav();
    $('.datepicker').pickadate({
      selectMonths: true, // Creates a dropdown to control month
      selectYears: 15, // Creates a dropdown of 15 years to control year,
      today: 'Oggi',
      clear: 'Pulisci',
      close: 'Ok',
      format: 'dd/mm'
    });
    $('.timepicker').pickatime({
      default: 'now', // Set default time: 'now', '1:30AM', '16:30'
      fromnow: 0,       // set default time to * milliseconds from now (using with default = 'now')
      twelvehour: false, // Use AM/PM or 24-hour format
      donetext: 'OK', // text for done-button
      cleartext: 'Pulisci', // text for clear-button
      canceltext: 'Annulla', // Text for cancel-button,
      container: undefined, // ex. 'body' will append picker to body
      autoclose: false, // automatic close timepicker
      aftershow: function(){} //Function for after opening timepicker
    });
  });
  </script>
  <script>
  window.onload = function() {
    check_caldaia();
    getListaProgrammazione();
    check_acc();
    };
  </script>
  <style>
    body{
      font-family: 'Asap', sans-serif;
      font-size: 20px;
    }
    .switch label .lever {
      background-color: #c66581;
    }
    .switch label .lever:after{
      background-color: #a00330;
    }
    .logo{
      width: 50px;
      height: auto;
      margin: 5px;
    }
    .nome_logo{
      font-size: 30px;
    }
    nav {
      color: #fff;
      background-color: #8cc04b;
      width: 100%;
      height: 56px;
      line-height: 56px;
    }
    #on{
      margin: 4%;
    }
    .spinner-red, .spinner-red-only {
      border-color: #c7053d !important;
    }
    .spinner-blue, .spinner-blue-only {
      border-color: #8cc04b !important;
    }
    #caricamento, #specificogiorno, #caldaia_calda{
      display: none;
    }
    #opzioni > *{
      margin: 2%;
    }
    #opzioni{
      margin: 1%;
    }


  </style>

  <script type="text/javascript">
  var richiesta;
  var richieste_caldaia;

  setInterval(function(){
    check_caldaia()
  }, 1500);

  function make_coffee(){
    var durata_coffee=22;

    document.getElementById("on").classList.add("disabled");

    Materialize.toast("Caff&egrave; in preparazione", 4000);
    document.getElementById("caricamento").style.display="block";

    if(document.getElementById("quick_corto_lungo").checked == true){ //Se è stato scelto di fare il caffè lungo, cambia la durata dell'infusione
      durata_coffee=35;
    }
    richiesta= new XMLHttpRequest();

    url="comandi/on.php?durata="+encodeURIComponent(durata_coffee);

    richiesta.open("GET",url,true) //ASINCRONO
    richiesta.onreadystatechange = aggiorna;
    richiesta.send(null);

  }

  function schedule_coffee(){
    var durata_coffee=22;

    if(document.getElementById("schedule_corto_lungo").checked == true){ //Se è stato scelto di fare il caffè lungo, cambia la durata dell'infusione
      durata_coffee=35;
    }
    richiesta= new XMLHttpRequest();

    if(document.getElementById("specificogiorno_ripetizione").checked == true){ //giorni della settimana
    var giorniselezionati = document.getElementsByName("giorni");
    var giornisettimana="";
    var i=0;
    console.log(giorniselezionati);

    //SELEZIONO I GIORNI
    for(i=0;i<giorniselezionati.length;i++){
      if(giorniselezionati[i].checked==true)
        giornisettimana+=giorniselezionati[i].value+','
    }
    giornisettimana = giornisettimana.slice(0, -1);

    url="comandi/programmazione.php?durata="+encodeURIComponent(durata_coffee)+"&giorni="+encodeURIComponent(giornisettimana);
    //FINE

    }else{//Se si è scelto il solo giorno
          var data = document.getElementById("data").value;
          url="comandi/programmazione.php?durata="+encodeURIComponent(durata_coffee)+"&data="+encodeURIComponent(data);
    }

    var ora = document.getElementById("ora").value;
    url+="&ora="+encodeURIComponent(document.getElementById("ora").value);


    richiesta.open("GET",url,false) //SINCRONO
    richiesta.onreadystatechange = aggiornaP;
    richiesta.send(null);

    getListaProgrammazione();



  }

  function mostranascondi(){
    var ripetizione = document.getElementById("ripetizione");
    var specifico_giorno = document.getElementById("specificogiorno");
    if(document.getElementById("specificogiorno_ripetizione").checked == true){ //Se è stato scelot di fare il caffè lungo, fai il caffè Lungo
      ripetizione.style.display = "block";
      specifico_giorno.style.display = "none";
    }else {
      ripetizione.style.display = "none";
      specifico_giorno.style.display = "block";
    }

  }

  function mostranascondi_caldaia(){
    if((richieste_caldaia.readyState==4)&&(richieste_caldaia.status == 200)){
      console.log(richieste_caldaia.responseText);
      if(richieste_caldaia.responseText==1){
        var caldaia_fredda = document.getElementById("caldaia_fredda");
        var caldaia_calda = document.getElementById("caldaia_calda");
        caldaia_calda.style.display = "block";
        caldaia_fredda.style.display = "none";
      }else{
        var caldaia_fredda = document.getElementById("caldaia_fredda");
        var caldaia_calda = document.getElementById("caldaia_calda");
        caldaia_calda.style.display = "none";
        caldaia_fredda.style.display = "block";
      }
    }

  }


  function aggiorna(){
    if((richiesta.readyState==4)&&(richiesta.status == 200)){
        Materialize.toast("Caff&egrave; pronto!", 4000);
        document.getElementById("on").classList.remove("disabled");
        document.getElementById("caricamento").style.display="none";

      }
  }
  function aggiornaP(){
    if((richiesta.readyState==4)&&(richiesta.status == 200)){
        Materialize.toast("Caff&egrave; programmato!", 4000);
        console.log(richiesta.responseText);
      }
  }

  function aggiornaListaProgrammazioni(){
    if((richiesta.readyState==4)&&(richiesta.status == 200)){
      var cronstrue = window.cronstrue;
      var s = "";
      var str_tmp="";

      ris = document.getElementById("programmazione");

      programmazione=richiesta.responseText.split(/\r?\n/);

      for(i=0;i<programmazione.length-1;i++){
        str_tmp=programmazione[i].split(" sudo");
        s+="<p><input type='checkbox' id='progn"+i+"' name='prog' value='"+programmazione[i]+"' /><label for='progn"+i+"'>"+cronstrue.toString(str_tmp[0], { locale: "it" })+"</label></p>";
      }
      ris.innerHTML=s;
      //richiesta.responseText;
    }
  }

  function aggiornaAccSpen(){
    if((richiesta.readyState==4)&&(richiesta.status == 200)){
      if(richiesta.responseText==1){
        document.getElementById("spen").classList.add("disabled");
        document.getElementById("acc").classList.remove("disabled");
      }else{
        document.getElementById("acc").classList.add("disabled");
        document.getElementById("spen").classList.remove("disabled");
      }
    }
  }

  function scambiaAccSpen(stato,altro_stato){
    if((richiesta.readyState==4)&&(richiesta.status == 200)){
      console.log(stato);
      console.log(altro_stato);
      document.getElementById(String(stato)).classList.add("disabled");
      document.getElementById(String(altro_stato)).classList.remove("disabled");
    }
  }

  function getListaProgrammazione(){
    richiesta= new XMLHttpRequest();

    richiesta.open("GET","comandi/program_elenco.php",false) //SINCRONO
    richiesta.onreadystatechange = aggiornaListaProgrammazioni;
    richiesta.send(null);

  }

  function eliminaProgrammazioni(){
    richiesta= new XMLHttpRequest();

    var programmazioniSelezionate = document.getElementsByName("prog");
    var programmazioniDaEliminare = [];
    var i=0;
    //console.log(programmazioniSelezionate);

    //SELEZIONO I GIORNI
    for(i=0;i<programmazioniSelezionate.length;i++){
      if(programmazioniSelezionate[i].checked==true){
        programmazioniDaEliminare[i]="";
        programmazioniDaEliminare[i]=programmazioniSelezionate[i].value;
        console.log(programmazioniSelezionate[i].value);
      }
    }
    //console.log(programmazioniDaEliminare);

    richiesta.open("GET","comandi/eliminazione.php?programmazioniDaEliminare="+encodeURIComponent(JSON.stringify(programmazioniDaEliminare)),false)
    console.log("comandi/eliminazione.php?programmazioniDaEliminare="+encodeURIComponent(JSON.stringify(programmazioniDaEliminare))); //SINCRONO
    richiesta.onreadystatechange = getListaProgrammazione;
    richiesta.send(null);
  }

  function check_caldaia(){

    richieste_caldaia = new XMLHttpRequest();

    richieste_caldaia.open("GET","comandi/leggi_caldaia.php",true)
    richieste_caldaia.onreadystatechange = mostranascondi_caldaia;
    richieste_caldaia.send(null);
  }

function check_acc(){
  richiesta = new XMLHttpRequest();

  richiesta.open("GET","comandi/controlla_acc.php",false)
  richiesta.onreadystatechange = aggiornaAccSpen;
  richiesta.send(null);
}

function setAccSpen(stato,altro_stato){
  richiesta = new XMLHttpRequest();

  richiesta.open("GET","comandi/accendi_spegni.php",false)
  //richiesta.onreadystatechange =
  richiesta.send(null);
  scambiaAccSpen(stato,altro_stato);
}


  </script>
</head>
<body>

  <nav class="nav-extended navbar-fixed">
    <div class="nav-wrapper">
      <img src="img/Logo1.png" class="logo col s12"></img><a href="#" class="nome_logo" style="vertical-align: bottom; margin-left: 20px; color:#404040;">piCoffee</a>
      <a href="#" data-activates="mobile-demo" class="button-collapse"><i class="material-icons">menu</i></a>
      <ul id="nav-mobile" class="right hide-on-med-and-down">
        <li><a href="#">Esame di stato 2018</a></li>
      </ul>
      <ul class="side-nav" id="mobile-demo">
        <li><a href="#">Esame di stato 2018</a></li>
      </ul>
    </div>
    <div class="nav-content">
      <ul class="tabs tabs-transparent">
        <li class="tab"><a href="#quickcoffee">Caff&egrave; veloce</a></li>
        <li class="tab"><a href="#schedulecoffee">Programma il caff&egrave;</a></li>
        <li class="tab"><a href="#opzioni">Opzioni</a></li>
      </ul>
    </div>
  </nav>

<div id="quickcoffee" class="col s12 center-align">

  <div id="bottone" class="col s6">
    <p>Prepara il caffè con un semplice tocco.<br></p>
    <a id="on" class="btn-floating btn-large waves-effect waves-light red" onclick="make_coffee();"><i class="material-icons">local_cafe</i></a>
  </div>
    <p>Scegli il caffè che più ti piace</p>
  <div class="switch">
      <label>
        Caff&egrave; Espresso
        <input id="quick_corto_lungo" type="checkbox">
        <span class="lever"></span>
        Caff&egrave; Lungo
      </label>
    </div>
  <div id="caldaia_calda" style="margin: 7%;">
    <a class="btn btn-floating btn-large pulse orange darken-4"><i class="material-icons">whatshot</i></a>
    <p>La caldaia è pronta. E tu sei pronto per un buon caffè?</p>
  </div>
  <div id="caldaia_fredda" style="margin: 7%;">
    <a class="btn btn-floating btn-large light-blue lighten-2"><i class="material-icons">whatshot</i></a>
    <p>La caldaia non è ancora calda. Preparati subito un caffè!</p>
  </div>
  <div id="risultato"></div>
  <div id="caricamento">
    <div class="preloader-wrapper big active">

          <div class="spinner-layer spinner-blue">
            <div class="circle-clipper left">
              <div class="circle"></div>
            </div><div class="gap-patch">
              <div class="circle"></div>
            </div><div class="circle-clipper right">
              <div class="circle"></div>
            </div>
          </div>
          <div class="spinner-layer spinner-red">
            <div class="circle-clipper left">
              <div class="circle"></div>
            </div><div class="gap-patch">
              <div class="circle"></div>
            </div><div class="circle-clipper right">
              <div class="circle"></div>
            </div>
          </div>
        </div>
  </div>

</div>


<div id="schedulecoffee" class="col s12 center-align">
  <p> Fai in modo ti trovare il caffè pronto quando vuoi. <br/><br/></p>
  <p>Scegli il caffè che più ti piace</p>
  <div class="switch">
      <label>
        Caff&egrave; Espresso
        <input id="schedule_corto_lungo" type="checkbox">
        <span class="lever"></span>
        Caff&egrave; Lungo
      </label>
  </div>
  <p>Scegli quando pianificarlo.</p>
  <div class="switch">
      <label>
        Singola programmazione
        <input id="specificogiorno_ripetizione" type="checkbox" onchange="mostranascondi();" checked>
        <span class="lever"></span>
        Multiplaprogrammazione
      </label>
  </div>
  <div id="specificogiorno">
  <p>Seleziona il giorno/i.</p>
  <input id="data" type="text" class="datepicker col s2 offset-s4">
  </div>

  <div id="ripetizione">
  <p>Seleziona i giorni della settimana.</p>
    <p>
      <input type="checkbox" id="lun" name="giorni" value="1" />
      <label for="lun">Luned&igrave;</label>
    </p>
    <p>
      <input type="checkbox" id="mar" name="giorni" value="2"/>
      <label for="mar">Marted&igrave;</label>
    </p>
    <p>
      <input type="checkbox" id="mer" name="giorni" value="3"/>
      <label for="mer">Mercoled&igrave;</label>
    </p>
    <p>
      <input type="checkbox" id="gio" name="giorni" value="4"/>
      <label for="gio">Gioved&igrave;</label>
    </p>
    <p>
      <input type="checkbox" id="ven" name="giorni" value="5"/>
      <label for="ven">Venerd&igrave;</label>
    </p>
    <p>
      <input type="checkbox" id="sab" name="giorni" value="6"/>
      <label for="sab">Sabato</label>
    </p>
    <p>
      <input type="checkbox" id="dom" name="giorni" value="0"/>
      <label for="dom">Domenica</label>
    </p>
  </div>
  <p>Dimmi anche a che ora vuoi che ti venga preparato il caffè.</p>
  <input id="ora" type="text" class="timepicker" tabindex="55">
  <a class="btn-floating btn-large waves-effect waves-light red" onclick=schedule_coffee();><i class="material-icons">av_timer</i></a>

  <p>Ecco le pianificazioni già in corso</p>
  <div id="programmazione"></div>
  <a class="btn-floating btn-large waves-effect waves-light red"  onclick=eliminaProgrammazioni();><i class="material-icons">delete</i></a>
</div>

<div id="opzioni" class="col s12 center-align">
  Accensione e spegnimento
  <br>
  <a id="acc" onclick="setAccSpen('acc', 'spen');" style="font-size: 18px;" class="waves-effect waves-light btn-large green darken-1"><i class="material-icons left">flash_on</i>Accendi la caffettiera</a>
  <a id="spen" onclick="setAccSpen('spen', 'acc');" style="font-size: 18px;" class="waves-effect waves-light btn-large orange accent-4"><i class="material-icons left">flash_off</i>Spegni la caffettiera</a>
  <div class="card blue-grey darken-1">
        <div class="card-content black-text amber">
          <span class="card-title"><i class="material-icons">warning</i> Attenzione</span>
          <p>Ricorda di spegnere la caffettiera prima di uscire!</p>
        </div>
  </div>
</div>


</body>
</html>
