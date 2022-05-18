 var frutasvalor = [5,8,7,2];
function cargar() {
    // document.getElementById("imgCaptcha01").src = "imagenes/beterraga.png";
    
    var frutas = ["beterraga", "carambola", "cebolla", "durazno", "fresa", "granadilla", "manzana", "maracuya", "palta", "papaya", "platano", "rabanito", "sandia", "uva"];
    nrofrutas=14;
    p1 = getRandomInt(1, nrofrutas);
    var item = [0,0,0,0]
    item[0] = getRandomInt(1, nrofrutas);
    for (;;)        {
        item[1] =  getRandomInt(1, nrofrutas);
        if (item[0]!=item[1]) {
            break;
        }            
    }
    for (;;)        {
        item[2] =  getRandomInt(1, nrofrutas);
        if (item[2]!=item[1]  &  item[2]!=item[0]  ) {
            break;
        }            
    }
    for (;;)        {
        item[3] =  getRandomInt(1, nrofrutas);
        if (item[3]!=item[2]  &  item[3]!=item[1] &  item[3]!=item[0]   ) {
            break;
        }            
    }
//
      var xip = "fspreset.minagri.gob.pe";
    // var xip = "10.3.0.21";
     
     
    p1 = "http://"+xip+":5000/Content/capcha/img/" + frutas[item[0]] + ".png"
    p2 = "http://"+xip+":5000/Content/capcha/img/" + frutas[item[1]] + ".png"
    p3 = "http://"+xip+":5000/Content/capcha/img/" + frutas[item[2]]  + ".png"
    p4 = "http://"+xip+":5000/Content/capcha/img/" +  frutas[item[3]] + ".png"
    var frutasElegir = [p1,p2,p3,p4];
    vdefe = getRandomInt(0,4);
    im = frutasElegir[vdefe];
    var nimagem = im.split("/");
    var nimg = nimagem[6].split(".");
    

    $("#hfCaptcha_Seleccion").val(frutasvalor[vdefe]);    
    $("#imgCaptcha01").attr("src", p1);
    $("#imgCaptcha02").attr("src", p2);
    $("#imgCaptcha03").attr("src", p3);
    $("#imgCaptcha04").attr("src", p4);
    $("#ImgSel").text(nimg[0]);

    document.getElementById('imgCaptcha01').className = 'CSSCaptcha_Imagen';
    document.getElementById('imgCaptcha02').className = 'CSSCaptcha_Imagen';
    document.getElementById('imgCaptcha03').className = 'CSSCaptcha_Imagen';
    document.getElementById('imgCaptcha04').className = 'CSSCaptcha_Imagen';
    $("#res").val("*");
    $("#res-error").removeClass("hidden");
}



function cargar1(a,b,c,d,e,f) {
    // document.getElementById("imgCaptcha01").src = "imagenes/beterraga.png";
    
    var frutas = ["beterraga", "carambola", "cebolla", "durazno", "fresa", "granadilla", "manzana", "maracuya", "palta", "papaya", "platano", "rabanito", "sandia", "uva"];
    nrofrutas=14;
    p1 = getRandomInt(1, nrofrutas);
    var item = [0,0,0,0]
    item[0] = getRandomInt(1, nrofrutas);
    for (;;)        {
        item[1] =  getRandomInt(1, nrofrutas);
        if (item[0]!=item[1]) {
            break;
        }            
    }
    for (;;)        {
        item[2] =  getRandomInt(1, nrofrutas);
        if (item[2]!=item[1]  &  item[2]!=item[0]  ) {
            break;
        }            
    }
    for (;;)        {
        item[3] =  getRandomInt(1, nrofrutas);
        if (item[3]!=item[2]  &  item[3]!=item[1] &  item[3]!=item[0]   ) {
            break;
        }            
    }
//
      var xip = "fspreset.minagri.gob.pe";
    // var xip = "10.3.0.21";
     
     
    p1 = "http://"+xip+":5000/Content/capcha/img/" + frutas[item[0]] + ".png"
    p2 = "http://"+xip+":5000/Content/capcha/img/" + frutas[item[1]] + ".png"
    p3 = "http://"+xip+":5000/Content/capcha/img/" + frutas[item[2]]  + ".png"
    p4 = "http://"+xip+":5000/Content/capcha/img/" +  frutas[item[3]] + ".png"
    var frutasElegir = [p1,p2,p3,p4];
    vdefe = getRandomInt(0,4);
    im = frutasElegir[vdefe];
    var nimagem = im.split("/");
    var nimg = nimagem[6].split(".");
    

    $("#"+a).val(frutasvalor[vdefe]);    
    $("#"+b).attr("src", p1);
    $("#"+c).attr("src", p2);
    $("#"+d).attr("src", p3);
    $("#"+e).attr("src", p4);
    $("#"+f).text(nimg[0]);


    $("#"+b).addClass("CSSCaptcha_Imagen");
    $("#"+c).addClass("CSSCaptcha_Imagen");
    $("#"+d).addClass("CSSCaptcha_Imagen");
    $("#"+e).addClass("CSSCaptcha_Imagen");

    $("#res1").val("*");
    $("#res-error1").removeClass("hidden");
}


function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min)) + min;
}



function Captcha_ActivaOpcion1(numImagen,b,c,d,e) {
    
    document.getElementById('hfCaptcha_ticket1').value = frutasvalor[numImagen-1];
    
    $("#"+b).addClass("CSSCaptcha_Imagen");
    $("#"+c).addClass("CSSCaptcha_Imagen");
    $("#"+d).addClass("CSSCaptcha_Imagen");
    $("#"+e).addClass("CSSCaptcha_Imagen");
    
    $("#"+b).removeClass("CSSCaptcha_ImagenSeleccionada");
    $("#"+c).removeClass("CSSCaptcha_ImagenSeleccionada");
    $("#"+d).removeClass("CSSCaptcha_ImagenSeleccionada");
    $("#"+e).removeClass("CSSCaptcha_ImagenSeleccionada");
    
    var imgse = [b,c,d,e];

    document.getElementById(imgse[numImagen-1]).className = 'CSSCaptcha_ImagenSeleccionada';
   ValidarCaptcha1() ;
}


function Captcha_ActivaOpcion(numImagen) {
      
    document.getElementById('hfCaptcha_ticket').value = frutasvalor[numImagen-1];
    document.getElementById('imgCaptcha01').className = 'CSSCaptcha_Imagen';
    document.getElementById('imgCaptcha02').className = 'CSSCaptcha_Imagen';
    document.getElementById('imgCaptcha03').className = 'CSSCaptcha_Imagen';
    document.getElementById('imgCaptcha04').className = 'CSSCaptcha_Imagen';
    document.getElementById('imgCaptcha0' + numImagen).className = 'CSSCaptcha_ImagenSeleccionada';
   ValidarCaptcha() ;
}

function ValidarCaptcha() {
    var txtSeleccion = document.getElementById('hfCaptcha_ticket').value;
    var imgSelec = document.getElementById('hfCaptcha_Seleccion').value ;
    verif = txtSeleccion-imgSelec;
    if (verif == 0) {
        $("#res").val("1");
        $("#res-error").addClass("hidden");
    }
    else
    {
        $("#res").val("*");
        $("#res-error").removeClass("hidden");
    }
}


function ValidarCaptcha1() {

    var txtSeleccion = document.getElementById('hfCaptcha_ticket1').value;
    var imgSelec = document.getElementById('hfCaptcha_Seleccion1').value ;
    verif = txtSeleccion-imgSelec;
    if (verif == 0) {
        $("#res1").val("1");
        $("#res-error1").addClass("hidden");
    }
    else
    {
        $("#res1").val("*");
        $("#res-error1").removeClass("hidden");
    }
}


