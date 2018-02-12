var newRowID = 1000000;
var oldTitreR = 0;
var idT;


(function($) {
    $(document).ready(function() {
        
        
        var nbDays = $('#nbDay').val();
        idT = $('#idT').val();

        $('div[id^="client"],div[id^="heures"],div[id^="debuts"],div[id^="titreR"],div[id^="status"],div[id^="scaner"]').click(function(event) {
            var str = $(this).attr("id");
            var col = str.substr(0,6);
            var id = str.substr(6);
            if(col === "titreR"){
                oldTitreR = $('#inputtitreR'+id+' option:selected').val();
            }
            viewEditCol(col,id);
        });

        $('select[id^=input]').change(function() {
            var str = $(this).attr("id");
            var col = str.substr(5,6);
            var id = str.substr(11);
            closeEditCol(col,id);
        });

        $('#seekClient').keyup(function(event) {
            findClient($('#seekClient').val());
        });
    });  
})(jQuery);



/////////////////////////////////////////////// GETTERS ///////////////////////
function getVal(col,id){
	return $("#input"+col+id+" option:selected").val();
}

function getTxt(col,id){
	return $("#input"+col+id+" option:selected").text();
}


function getId(el){
    return $('#'+el).id;
}


////////////////////////


/////////////////////////////////////////////// SETTERS ///////////////////////

function setVal(col,id, val){
	$("#"+col+id).find('p').html(val);
        if(col === "titreM"){
            if(val != 0){
                $("#"+col+id).find('p').css("color","red");
            }
            else{
                $("#"+col+id).find('p').css("color","green");
            }
        }
}
////////////////////////

function isValidInsert(id){
    if(getVal("client",id) == 0)
            return false;
    else if(getVal("heures",id) == 0)
            return false;
    else
            return true;	
}


function viewEditCol(col,id){
    var el = "#"+col+id;
    var $txt = $(el).find('p');
    var input = "#input"+col+id;

    if(col == "titreR"){
        var nbH = getVal("heures",id);
        if(nbH != 0){
            $(input).show();
            $txt.hide();
            $(el).click(function(e) {
              e.stopPropagation();
            });

            $(document).click(function(){  
                    closeEditCol(col,id);
            });
        }
    }
    else{
        $(input).show();
        $txt.hide();
        $(el).click(function(e) {
          e.stopPropagation();
        });

        $(document).click(function(){  
                closeEditCol(col,id);
        });
    }
		
	
}

function closeEditCol(col,id){
	var el = "#"+col+id;
	var $txt = $(el).find('p');
	var input = "#input"+col+id;
	$txt.show();
	$(input).hide();
}



function newValue(col,id,update){
    var el = "#"+col+id;
    var $txt = $(el).find('p');
    var input = "#input"+col+id;
    $txt.html($(input + " option:selected").text());
    var idC = $(input + " option:selected").val();

    closeEditCol(col,id);
    if(col === "client"){

        if(idC === 'AJ' || idC === 'M' || idC === 'V' || idC === 'CE' || idC === 'AU' || idC === 'JF' || idC === 'AT'){
              if(update === "false"){
                  prepareInsertAbs(id);
                  setRowAbsence(id,idC);
              }
            else if(update === "true"){
                    updateRowAsAbsence(id,idC);
                    setRowAbsence(id,idC);
            }
        }
        else{
            if(update === "true"){
                var txt = $('#titreM'+id).find('p').text();
                if( txt === 'AJ' || txt === 'M' || txt === 'V' || txt === 'CE' || txt === 'AU' || txt === 'JF' || txt === 'AT' ){
                    removeAbsence(id);
                }
                updateClient(id,idC);
            }
        }
    }
    else if(col === "debuts"){
        var nbH = $("#inputheures"+id+" option:selected").val();
        $txt.html(calculHoraire(id,idC,nbH));
        if(update === "true"){
                updateDebut(id,idC);
        }
    }
    else if(col === "heures"){
        var debuts = $("#inputdebuts"+id+" option:selected").val();
        if($("#hPrevue"+id).length){
            $txt.html($(input + " option:selected").text() + " / " + $("#hPrevue"+id).val());
        }

        $("#debuts"+id).find('p').html(calculHoraire(id,debuts,idC));
        adjustTitres(id);
        populateTitresR(id,idC);
        calcTitresM(id);
        if(update === "true"){
                updateHeure(id,idC);
                $(".total_table").load(location.href+" .total_table>*","");

        }
    }
    else if (col === "titreR"){
        setSelectedTitre(input,idC);
        adjustTitres(id);
        calcTitresM(id);

        if(update === "true"){
                if(idC === "E"){
                        $('#titreM'+id).find('p').html("E");
                        if( $('#typeM').val() == 1 ){
                                addStatus(id);
                                
                            }
                }
                else{
                        deleteStatus(id);
                        
                    }

                updateTitresR(id,idC);
                $(".total_table").load(location.href+" .total_table>*","");
                

        }
    }
    else if(col === "status"){
        setStatus(id,idC);
        updateStatus(id,idC);
        $(".total_table").load(location.href+" .total_table>*","");

    }

    if(update === "false" && isValidInsert(id)){
        prepareInsert(id);	
    }
	
}

function deleteStatus(id){
    if($('#inputstatus'+id).length){
            $('#status'+id).html("");
    }
}
function addStatus(id){
    valString = "newValue('status',"+id+",'true')";
    var content = '<p style="color:red;">En attente </p>'+
                                    '<select id="inputstatus'+id+'" onchange="'+valString+'" style="display:none;" class="width">'+
                                            '<option value="1"> En règle </option>'+
                                            '<option selected value="2"> En attente </option>'+
                                    '</select>'
    $('#status'+id).html(content);
}

function setStatus(id,val){
    if(val == 1){
            $('#status'+id).find('p').css('color','green').html("En règle");
            $('#inputstatus'+id+' option:selected').prop('selected',false);
            $('#inputstatus'+id+' option[value="'+val+'"]').prop('selected',true);
    }
    else{
            $('#status'+id).find('p').css('color','red').html("En attente");
            $('#inputstatus'+id+' option:selected').prop('selected',false);
            $('#inputstatus'+id+' option[value="'+val+'"]').prop('selected',true);
    }
}

function setSelectedTitre(input,val){
    $(input+' option:selected').prop('selected',false);
    $(input+' option[value="'+val+'"]').prop('selected',true);
}


function setRowAbsence(id,idC){
    $('#client'+id).find('p').html(getTxt('client',id));
    $('#heures'+id).find('p').html(idC);
    $('#debuts'+id).find('p').html(idC);
    $('#titreR'+id).find('p').html(idC);
    $('#titreM'+id).find('p').html(idC);
    $('#item'+id).attr("style","border-color:grey;background-color:lightgrey;");
}

function removeAbsence(id){
    $('#client'+id).find('p').html(getTxt('client',id));
    $('#heures'+id).find('p').html(getTxt('heures',id));
    $('#debuts'+id).find('p').html(getTxt('debuts',id));
    $('#titreR'+id).find('p').html(getTxt('titreR',id));
    $('#titreM'+id).find('p').html(getTxt('titreM',id));
    $('#item'+id).removeAttr("style");
}

function prepareInsertAbs(id){
    var client = getVal("client",id);
    var day = $("#inputday"+id).val();
    var month = $("#inputmonth"+id).val();
    var year = $("#inputyear"+id).val();
    var idT = $("#idT").val();
    insertAbsence(day,month,year,client,idT,id);
}

function prepareInsert(id){
    var client = getVal("client",id);
    var heures = getVal("heures",id);
    var debuts = 8;
    var titreR = 0;
    var day = $("#inputday"+id).val();
    
           
    var month = $("#inputmonth"+id).val();
    var year = $("#inputyear"+id).val();
    var idT = $("#idT").val();
    if($("#hPrevue"+id).length)
        var hPrevue = $("#hPrevue"+id).val();
    else
        var hPrevue = 0;
    insertPrestation(day,month,year,client,heures,debuts,titreR,idT,hPrevue,id);
    //$(".total_table").load(location.href+" .total_table>*","");
}



function setAsPrested(newID,oldID){
    remplaceID(oldID,newID);
    
        $(".total_table").load(location.href+" .total_table>*","");
   
    
    
}


function adjustTitres(id){
    var nbH = getVal("heures",id);
    var nbT = getVal("titreR",id);
    if(nbT != "E"){
        if(nbH < nbT){
            $("#inputtitreR"+id+" option[value='"+nbT+"']").prop('selected', false);
            $("#inputtitreR"+id+" option[value='"+nbH+"']").prop('selected', true);
        }
    }
}

function populateTitresR(id, nb){
    var input = "#inputtitreR"+id;
    var selected = $(input+" option:selected").val();
    $(input).empty();
    var newContent= "";
    $(input).append("<option value='E'>E</option>");
    for(var i = 0; i <= nb; ++i){
        if(i == selected)
            $(input).append('<option selected value="'+i+'">'+i+'</option>');
        else
            $(input).append('<option value="'+i+'">'+i+'</option>');
    }

    setVal("titreR",id,selected);
}	

function calcTitresM(id){
    var nbH = getVal("heures",id);
    var nbT = getVal("titreR",id);
    var tiM = nbH-nbT;
    setVal("titreM",id,tiM);
}

function calculHoraire(id,debuts,nbH){
    var demiH_fin = false;
    var newD = debuts;
    var h =  parseInt(newD);
    var min = newD - h;
    if(min.toFixed(1) != 0){
            demiH_fin = true;
    }
    var heureFin = parseInt(Math.floor(h)) + parseInt(nbH);
    var demiH = false;
    if(newD <= 12 && heureFin > 12){
        if(min.toFixed(1) == 0){
            demiH = true;
        }
        else{
            heureFin = parseInt(heureFin) + 1;
        }
    }
    $nomC = $('#debuts'+id).find($('p'));

    if(min.toFixed(1) == 0 && demiH){
        return h +":00 / "+heureFin+":30";
    }
    else if(min.toFixed(1) != 0 && demiH || min.toFixed(1) != 0 && demiH_fin){
        return h +":30 / "+heureFin+":30";
    }
    else if(min.toFixed(1) != 0 && !demiH){
        return h +":30 / "+heureFin+":00";
    }
    else if(min.toFixed(1) == 0){
        return h +":00 / "+heureFin+":00";
    }
    else{
        return h+":30 / "+heureFin+":30";
    }
			
}

function initInputs($row,id){
    $row.find('#inputclient'+newRowID+' option:selected').prop('selected',false);
    $row.find('#inputdebuts'+newRowID+' option:selected').prop('selected',false);
    $row.find('#inputheures'+newRowID+' option:selected').prop('selected',false);
    $row.find('#inputtitreR'+newRowID+' option:selected').prop('selected',false);
    $("#inputheures"+newRowID+ " option[value='0']").prop('selected', true);
    $("#inputtitreR"+newRowID+ " option[value='0']").prop('selected', true);
    $("#inputdebuts"+newRowID+ " option[value='8']").prop('selected', true);
    initTxt($row,id);
}

function initTxt($row,id){
    $row.find('#client'+newRowID).find('p').html('Sélectionner');
    $row.find('#heures'+newRowID).find('p').html('0');
    $row.find('#debuts'+newRowID).find('p').html('8:00/');
    $row.find('#titreR'+newRowID).find('p').html('0');
    $row.find('#titreM'+newRowID).find('p').html('0');
    $row.insertAfter( $("#item"+id) );
    ++newRowID;
}



function initRow($row,id){
    $row.find('#client'+id).prop('id','client'+newRowID).attr("onclick","viewEditCol('client',"+newRowID+")").find('#inputclient'+id).prop('id','inputclient'+newRowID).attr("onchange","newValue('client',"+newRowID+",'false')");
    $row.find('#heures'+id).prop('id','heures'+newRowID).attr("onclick","viewEditCol('heures',"+newRowID+")").find('#inputheures'+id).prop('id','inputheures'+newRowID).attr("onchange","newValue('heures',"+newRowID+",'false')");
    $row.find('#debuts'+id).prop('id','debuts'+newRowID).attr("onclick","viewEditCol('debuts',"+newRowID+")").find('#inputdebuts'+id).prop('id','inputdebuts'+newRowID).attr("onchange","newValue('debuts',"+newRowID+",'false')");
    $row.find('#titreR'+id).prop('id','titreR'+newRowID).attr("onclick","viewEditCol('titreR',"+newRowID+")").find('#inputtitreR'+id).prop('id','inputtitreR'+newRowID).attr("onchange","newValue('titreR',"+newRowID+",'false')");
    $row.find('#titreM'+id).prop('id','titreM'+newRowID);
    $row.find('#inputday'+id).prop('id','inputday'+newRowID);
    $row.find('#inputmonth'+id).prop('id','inputmonth'+newRowID);
    $row.find('#inputyear'+id).prop('id','inputyear'+newRowID);
    $row.find('#hPrevue'+id).prop('id','hPrevue'+newRowID);
    $row.find('#hPrevue'+newRowID).val(0);

    initInputs($row,id);
	
}

function remplaceID(id,newID){
    $('#add'+id).prop('id','add'+newID).attr("onclick","addRow("+newID+")");
    $('#client'+id).prop('id','client'+newID).attr("onclick","viewEditCol('client',"+newID+")").find('#inputclient'+id).prop('id','inputclient'+newID).attr("onchange","newValue('client',"+newID+",'true')");
    $('#heures'+id).prop('id','heures'+newID).attr("onclick","viewEditCol('heures',"+newID+")").find('#inputheures'+id).prop('id','inputheures'+newID).attr("onchange","newValue('heures',"+newID+",'true')");
    $('#debuts'+id).prop('id','debuts'+newID).attr("onclick","viewEditCol('debuts',"+newID+")").find('#inputdebuts'+id).prop('id','inputdebuts'+newID).attr("onchange","newValue('debuts',"+newID+",'true')");
    $('#titreR'+id).prop('id','titreR'+newID).attr("onclick","viewEditCol('titreR',"+newID+")").find('#inputtitreR'+id).prop('id','inputtitreR'+newID).attr("onchange","newValue('titreR',"+newID+",'true')");
    $('#titreM'+id).prop('id','titreM'+newID);
    $('#inputday'+id).prop('id','inputday'+newID);
    $('#inputmonth'+id).prop('id','inputmonth'+newID);
    $('#inputyear'+id).prop('id','inputyear'+newID);
    $('#hPrevue'+id).prop('id','hPrevue'+newID);
    $('#item'+id).prop('id','item'+newID);
    if($('#remove'+id).length){
        $('#remove'+id).attr('id','remove'+newID);
        $('#remove'+newID).find('i').attr('onclick','deleteRow('+newID+')');
    }
    
//    $('#debuts'+newID +' p').html('8:00/');
//    $('#titreR'+newID+ ' p').html('0');
        
}


function addRow(id){
    var itemId = "item"+id;
    $("#"+itemId).insertAfter( $("#itemId") );
    var $div = $('#item'+id);
    var rowID = newRowID;
    var $row = $div.clone().prop('id', 'item'+rowID);
    if($row.find('#remove'+id).length){
        $row.find('#add'+id).remove();
        $row.find('#remove'+id).attr('id','remove'+rowID);
        $row.find('#remove'+rowID+' > i').attr('onclick','deleteRow('+rowID+')');
    }
    else{
        $row.find('#add'+id).attr('id','remove'+rowID);
        $row.find('#remove'+rowID).html('<i style="color:red;" onclick="removeRow('+rowID+')" class="fa fa-times fa-fw" aria-hidden="true"></i>');
    }



    initRow($row,id);

}

function removeRow(id){
    $('#item'+id).remove();
}

function delAss(idC,idT,$el){
	$el.parent().remove();
	deleteAssT(idC,idT);
}
	
/////////////////////////////////// AJAX /////////////////	
function deleteRow(idP){
    $.ajax({
        url: "Prestation/deletePrest",
        type: "POST",
        data: {idP:idP},
        success: function (data) {
            removeRow(idP);
             $(".total_table").load(location.href+" .total_table>*","");
            
        },
        error: function(jqXHR, textStatus, errorThrown) {
           console.log(textStatus, errorThrown);
        }
    });
}





	
function updateDebut(idP,hDebut){	
    $.ajax({
        url: "Prestation/updateD",
        type: "POST",
        
        data: {idP:idP,hDebut:hDebut},
        error: function(jqXHR, textStatus, errorThrown) {
           console.log(textStatus, errorThrown);
        }
    });
}
function updateTot(){
    $(".total_table").load(location.href+" .total_table>*","");
}	
	
function updateTitresR(idP,nbTR){
    if(nbTR === "E"){
        $.ajax({
            url: "Prestation/updateTE",
            type: "POST",
            async: false,
            data: {idP:idP},
            success: function (data) {
                
                

            },
            error: function(jqXHR, textStatus, errorThrown) {
               console.log(textStatus, errorThrown);
            }
        });
    }
    else{
        $.ajax({
            url: "Prestation/updateT",
            type: "POST",
            async: false,
            data: {idP:idP,nbTR:nbTR,oldTitreR:oldTitreR},
            succes: function(){
                updateTot();
            },
            error: function(jqXHR, textStatus, errorThrown) {
               console.log(textStatus, errorThrown);
            }
        });
    }
}
		
	
function updateHeure(idP,heure){
    $.ajax({
        url: "Prestation/updateH",
        type: "POST",
        async: false,
        data: {idP:idP,newH:heure},
        error: function(jqXHR, textStatus, errorThrown) {
           console.log(textStatus, errorThrown);
        }
    });
}

function updateClient(idP,idC){
    $.ajax({
        url: "Prestation/updateC",
        type: "POST",
        data: {idP:idP,idC:idC},
        succes: function(){
            
        },
        error: function(jqXHR, textStatus, errorThrown) {
           console.log(textStatus, errorThrown);
        }
    });
}

function updateStatus(idP,idC){
    $.ajax({
        url: "Prestation/updateS",
        type: "POST",
        async: false,
        data: {idP:idP,idC:idC},
        succes: function(d){
           
        },
        error: function(jqXHR, textStatus, errorThrown) {
           console.log(textStatus, errorThrown);
        }
    });
}

function updateRowAsAbsence(idP,idC){
    $.ajax({
        url: "Prestation/prestToAbsence",
        type: "POST",
        async: false,
        data: {idP:idP,idC:idC},
        success: function (d) {
            updateTot();
        },
        error: function(jqXHR, textStatus, errorThrown) {
           console.log(textStatus, errorThrown);
        }
    });

	
}



function deleteAssT(idC,idT){
    $.ajax({
        url: "Travailleur/deletAssT",
        type: "POST",
        data: {idC:idC,idT:idT},
        succes: function() {

        },
        error: function(jqXHR, textStatus, errorThrown) {
        }
    });
}

function assClient(idC,idT){
    $.ajax({
        url: "Admin/assClient",
        type: "POST",
        data: {idC:idC,idT:idT},
        succes: function() {

        },
        error: function(jqXHR, textStatus, errorThrown) {
        }
    });
}
//////////////////////////////////////////////////////////////////////////////////////

function insertPrestation(day,month,year,client,heures,debuts,titreR,idT,hPrevue,oldID){
    var idPL;
    if( $('#plan'+oldID).length ){
        idPL = $('#plan'+oldID).val();
    }
    else{
        idPL = "";
    }
    $.ajax({
        url: "Prestation/newP",
        type: "POST",
        async:false,
        data: {day:day,month:month,year:year,client:client,heures:heures,debuts:debuts,titreR:titreR,idT:idT,hPrevue:hPrevue,idPL:idPL},
        success: function (data) {
            setAsPrested(data,oldID);
        },
        error: function(jqXHR, textStatus, errorThrown) {
           console.log(textStatus, errorThrown);
        }

    });
        
      
}

function insertAbsence(day,month,year,client,idT,oldID){
    var idPL = "";
    var hPrevue = 0;
    if( $('#plan'+oldID).length ){
        idPL = $('#plan'+oldID).val();
        hPrevue = $('#hPrevue'+oldID).val();
    }

    $.ajax({
        url: "Prestation/newPAbsence",
        type: "POST",
        async:true,
        data: {day:day,month:month,year:year,client:client,idT:idT,idPL:idPL,hPrevue:hPrevue},
        success: function (data) {
            setAsPrested(data,oldID);
             
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }

    });
}

function findClient(keyword){
    $.ajax({
        url: "Prestation/find_client",
        type: "POST",
        data: {keyword:keyword},
        success: function (data) {
            if(keyword == ""){
                $('#resultClient').html("");
            }
            else
                $('#resultClient').html(data);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });
    
}

function addClient(id,nom,prenom){
    $('#listeCA').append('<li><i onclick="delAss('+id+','+idT+',$(this))" style="color:red;" class="fa fa-minus-circle" aria-hidden="true"></i> '+nom+' ' +prenom+'</li>');
    $('#'+id).remove();
    assClient(id,idT);
}


