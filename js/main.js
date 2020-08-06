//---------------------------------
//-----------add buttons-----------
//----------inside modals----------
//---------------------------------

function new_data(btn, caller) {
    caller = caller || null; // ES6 doesn't work in IE
    switch (btn) {
        // '+region' button 
        case 'reg':
            var new_reg = $('#new_reg').val();
            if (!!new_reg) { // if not empty
                $.ajax({
                    type: 'POST',
                    url: 'edit_global.php',
                    dataType: 'json',
                    data: { 
                        new_reg: new_reg,
                        r_type: r_type,
                        r_id: r_id 
                    },
                    success: function(response) {
                        $('#reg_table').html(response[0]);
                        $('#meterInfo_table').html(response[1]);
                    },
                    error: function() {
                        alert('fail');
                    }
                });
            }
            break;

        // '+structure type' button
        case 'str':
            var new_str = $('#new_str').val();
            if (!!new_str) { // if not empty
                $.ajax({
                    type: 'POST',
                    url: 'edit_global.php',
                    dataType: 'json',
                    data: { 
                        new_str: new_str,
                        r_type: r_type,
                        r_id: r_id 
                    },
                    success: function(response) {
                        $('#str_table').html(response[0]);
                        $('#meterInfo_table').html(response[1]);
                    },
                    error: function() {
                        alert('fail');
                    }
                });
            }
            break;
        
        // '+tus' button
        case 'tus':
            var new_tus = $('#new_tus').val();
            if (!!new_tus) { // if not empty
                $.ajax({
                    type: 'POST',
                    url: 'edit_global.php',
                    dataType: 'json',
                    data: { 
                        new_tus: new_tus,
                        r_type: r_type,
                        r_id: r_id 
                    },
                    success: function(response) {
                        $('#tus_table').html(response[0]);
                        $('#meterInfo_table').html(response[1]);
                    },
                    error: function() {
                        alert('fail');
                    }
                });
            }
            break;
            
        // '+owner' button (inactive)
        case 'own':
            var new_own = $('#new_own').val();
            if (!!new_own) { // if not empty
                $.ajax({
                    type: 'POST',
                    url: 'edit_global.php',
                    dataType: 'json',
                    data: { 
                        new_own: new_own,
                        r_type: r_type,
                        r_id: r_id 
                    },
                    success: function(response) {
                        $('#own_table').html(response[0]);
                        $('#meterInfo_table').html(response[1]);
                    },
                    error: function() {
                        alert('fail');
                    }
                });
            }
            break;
            
        // '+meter type' button
        case 'meter_type':
            var new_meter_type = {
                meter_type: $('#meterTypes_meter_type').val(),
                res_name: $('#meterTypes_res_name').val()
            };
            
            
            if (!!new_meter_type['meter_type']) { // if not empty
                $.ajax({
                    type: 'POST',
                    url: 'edit_global.php',
                    dataType: 'json',
                    data: { 
                        new_meter_type: new_meter_type,
                        r_type: r_type,
                        r_id: r_id
                    },
                    success: function(response) {
                        $('#meterType_table').html(response[0]);
                        $('#meterInfo_table').html(response[1]);
                    },
                    error: function() {
                        alert('fail');
                    }
                });
            }
            break;
            
        // '+meter num' button
        case 'meter_num':
            var new_meter_num = {
                meter_num: $('#meterNums_meter_num').val(),
                res_name: $('#meterNums_res_name').val()
            };
            
            
            if (!!new_meter_num['meter_num']) { // if not empty
                $.ajax({
                    type: 'POST',
                    url: 'edit_global.php',
                    dataType: 'json',
                    data: { 
                        new_meter_num: new_meter_num,
                        r_type: r_type,
                        r_id: r_id
                    },
                    success: function(response) {
                        $('#meterNum_table').html(response[0]);
                        $('#meterInfo_table').html(response[1]);
                        $('#rel_table').html(response[2]);
                    },
                    error: function() {
                        alert('fail');
                    }
                });
            }
            break;
            
        // '+meter's full info' button
        case 'meter_info':
            var new_meter_info = {};
            new_meter_info['meter_num'] = $('#meters_meter_num').val();
            new_meter_info['id'] = $('#meters_id').val();
            new_meter_info['meter_type'] = $('#meters_meter_type').val();
            new_meter_info['reg'] = $('#reg').val();
            new_meter_info['tus'] = $('#tus').val();
            new_meter_info['str_type'] = $('#str_type').val();
            new_meter_info['str_num'] = $('#str_num').val();
            new_meter_info['address'] = $('#address').val();
            new_meter_info['res_name'] = $('#meters_res_name').val();
            //new_meter_info['owner'] = $('#owner').val();
            
            if (!!new_meter_info['address']) { // if not empty
                $.ajax({
                    type: 'POST',
                    url: 'edit_global.php',
                    dataType: 'json',
                    data: { 
                        new_meter_info: new_meter_info,
                        r_type: r_type,
                        r_id: r_id
                    },
                    success: function(response) {
                        $('#meterInfo_table').html(response[0]);
                        $('#meterDate_table').html(response[1]);
                    },
                    error: function() {
                        alert('fail');
                    }
                });
            }
            break;
            
        // '+meter's date/valid info' button
        case 'meter_date':
            var new_meter_date = {};
            new_meter_date['meter_num'] = caller.parent().parent().children().eq(0).text();
            new_meter_date['setup_date'] = caller.parent().parent().children().eq(1).children().val();
            new_meter_date['comm_date'] = caller.parent().parent().children().eq(2).children().val();
            new_meter_date['inspect_date'] = caller.parent().parent().children().eq(3).children().val();
            new_meter_date['next_insp_date'] = caller.parent().parent().children().eq(4).children().val();
            new_meter_date['end_verif'] = caller.parent().parent().children().eq(5).children().val();
            new_meter_date['res_name'] = caller.parent().parent().children().eq(6).text() == 'Электричество' ? 1 : 2;
            
            $.ajax({
                type: 'POST',
                url: 'edit_global.php',
                data: { 
                    new_meter_date: new_meter_date,
                    r_id: r_id
                },
                success: function(response) {
                    //console.log(response);
                    $('#meterDate_table').html(response);
                },
                error: function() {
                    alert('fail');
                }
            });
            break;
            
        // '+agreement' button
        case 'agr':
            var new_agr = {
                agr: $('#new_agr').val(),
                concl_date: $('#concl_date').val(),
                res_name: $('#agrs_res_name').val()
            };
            
            if (!!new_agr['agr']) { // if not empty
                $.ajax({
                    type: 'POST',
                    url: 'edit_global.php',
                    dataType: 'json',
                    data: { 
                        new_agr: new_agr,
                        r_type: r_type,
                        r_id: r_id 
                    },
                    success: function(response) {
                        $('#agr_table').html(response[0]);
                        $('#rel_table').html(response[1]);
                    },
                    error: function() {
                        alert('fail');
                    }
                });
            }
            break;
            
        // '+relation meter_num <-> agreement' button
        case 'rel':
            var new_rel = {
                agr: $('#agr').val(),
                meter_num: $('#agrRel_meter_num').val(),
                code: $('#code').val()
            };
            
            $.ajax({
                type: 'POST',
                url: 'edit_global.php',
                data: { 
                    new_rel: new_rel,
                    r_type: r_type,
                    r_id: r_id  
                },
                success: function(response) {
                    $('#rel_table').html(response);
                },
                error: function() {
                    alert('fail');
                }
            });
            break;
    }
}

//---------------------------------
//----------edit button------------
//----------inside modal-----------
//-----------meterDates------------

// 'X meter date/valid info' button
function edit_data(caller) {
    var setup_date = caller.parent().parent().children().eq(1).text();
    var comm_date = caller.parent().parent().children().eq(2).text();
    var inspect_date = caller.parent().parent().children().eq(3).text();
    var next_insp_date = caller.parent().parent().children().eq(4).text();
    var end_verif = caller.parent().parent().children().eq(5).text();
//    var eq5 = caller.parent().parent().children().eq(5).text().split(/-/).reverse().join('.');

    caller.parent().parent().children().eq(1).html("<input type='date' class='form-control' style='text-align:right; width: 145px;' value='" + setup_date + "' >");
    caller.parent().parent().children().eq(2).html("<input type='date' class='form-control' style='text-align:right; width: 145px;' value='" + comm_date + "' >");
    caller.parent().parent().children().eq(3).html("<input type='date' class='form-control' style='text-align:right; width: 145px;' value='" + inspect_date + "' >");
    caller.parent().parent().children().eq(4).html("<input type='date' class='form-control' style='text-align:right; width: 145px;' value='" + next_insp_date + "' >");
    caller.parent().parent().children().eq(5).html("<input type='date' class='form-control' style='text-align:right; width: 145px;' value='" + end_verif + "' >");
    caller.parent().html("  <button type='button' onclick='new_data(\"meter_date\", $(this))' class='btn btn-light' style='background-color: white;'>\n\
                                <img src='img/save.png' width='30px' />\n\
                            </button>");
}
    

//---------------------------------
//-----------del buttons-----------
//----------inside modals----------
//---------------------------------

// 'X meter num' button
function del_data(btn, caller) {
    switch (btn) {
        //
        case 'meter_num':
            var del_meter_num = {
                meter_num: caller.parent().parent().children().eq(1).text(),
                res_name: caller.parent().parent().children().eq(8).text() == 'Электричество' ? 1 : 2
            };

            $.ajax({
                type: 'POST',
                url: 'edit_global.php',
                dataType: 'json',
                data: { 
                    del_meter_num: del_meter_num,
                    r_type: r_type,
                    r_id: r_id
                },
                success: function(response) {
                    $('#meterNum_table').html(response[0]);
                    $('#meterInfo_table').html(response[1]);
                    $('#meterDate_table').html(response[2]);
                    $('#rel_table').html(response[3]);
                },
                error: function() {
                    alert('fail');
                }
            });
            break;
            
        // 'X meter's full info' button
        case 'meter_info':
            var del_meter_info = { 
                meter_num: caller.parent().parent().children().eq(0).text(),
                res_name: caller.parent().parent().children().eq(8).text() == 'Электричество' ? 1 : 2
            };
            
            $.ajax({
                type: 'POST',
                url: 'edit_global.php',
                dataType: 'json',
                data: { 
                    del_meter_info: del_meter_info,
                    r_type: r_type,
                    r_id: r_id 
                },
                success: function(response) {
                    $('#meterInfo_table').html(response[0]);
                    $('#meterDate_table').html(response[1]);
                    $('#meterNum_table').html(response[2]);
                    $('#rel_table').html(response[3]);
                },
                error: function() {
                    alert('fail');
                }
            });
            break;
            
        // 'X agreement' button
        case 'agr':
            var del_agr = caller.parent().parent().children().eq(0).text();
            
            if (!!del_agr) { // if not empty
                $.ajax({
                    type: 'POST',
                    url: 'edit_global.php',
                    dataType: 'json',
                    data: { 
                        del_agr: del_agr,
                        r_type: r_type,
                        r_id: r_id 
                    },
                    success: function(response) {
                        $('#agr_table').html(response[0]);
                        $('#rel_table').html(response[1]);
                    },
                    error: function() {
                        alert('fail');
                    }
                });
            }
            break;
            
        // 'X relation meter_num <-> agreement' button
        case 'rel':
            var del_rel = caller.parent().parent().children().eq(0).text();
            
            if (!!del_rel) { // if not empty
                $.ajax({
                    type: 'POST',
                    url: 'edit_global.php',
                    data: { 
                        del_rel: del_rel,
                        r_type: r_type,
                        r_id: r_id 
                    },
                    success: function(response) {
                        $('#rel_table').html(response);
                    },
                    error: function() {
                        alert('fail');
                    }
                });
            }
            break;
    }
}

// function for all search fields in modals
function finder(s_line, table, col) { // s_line = search field element
    var regex = new RegExp(s_line.val());
    for (var i = 0; i < table.find('tbody').children().length; i++) {
        !!table.find('tbody').children().eq(i).children().eq(col).text().match(regex) ? 
            table.find('tbody').children().eq(i).prop('hidden', false) : 
            table.find('tbody').children().eq(i).prop('hidden', true);
    }
}

// users' readings calculations
function calc(caller) {
    let init_value = caller[0].parentNode.previousSibling.previousSibling;
    let last_value = caller[0];
    let result = caller[0].parentNode.nextSibling.nextSibling;
    
    if (isNaN(last_value.value) || last_value.value < Number(init_value.innerHTML)) {
        result.innerHTML = 'неверный ввод!';
    } else {
        result.innerHTML = last_value.value - Number(init_value.innerHTML);
    }
}

//---------------------------------
//-----------page loaded-----------
//---------------------------------

$(document).ready(function(){
    //users' interface
    //result calculations
//    var init_value = document.getElementsByClassName("init_value");
//    var last_value = document.getElementsByClassName("last_value");
//    var result = document.getElementsByClassName("result");
    
    // aux func -> close modal by esc/button
    function close_modal(modal_id) {
        //close modal by ESC...
        $(document).keyup(function(event) {
            if (event.keyCode == 27) {
                $(modal_id).modal('hide');
            }
        });
        //... and by close button
        $('.close').click(function() {
            $(modal_id).modal('hide');
        });
    }
    
//    // doesn't work in IE
//    // searching for an active input field
//    for (let i = 0; i < last_value.length; ++i) {
//        // pressing ENTER
//        last_value[i].addEventListener("keypress", function(event) {
//            console.log(last_value[i]);
//            if (event.keyCode == 13) {
//                event.preventDefault();
//                if (isNaN(last_value[i].value) || last_value[i].value < Number(init_value[i].innerHTML)) {
//                    result.innerHTML = 'неверный ввод!';
//                } else {
//                    result.innerHTML = last_value[i].value - Number(init_value[i].innerHTML);
//                    //alert(document.getElementsByClassName("result")[i].innerHTML);
//                }
//            }
//        });
//        
//        // input field changed
//        last_value[i].addEventListener("change", function(event) {
//            event.preventDefault();
//            if (isNaN(last_value[i].value) || last_value[i].value < Number(init_value[i].innerHTML)) {
//                result[i].innerHTML = 'неверный ввод!';
//            } else {
//                result[i].innerHTML = last_value[i].value - Number(init_value[i].innerHTML);
//                //alert(document.getElementsByClassName("result")[i].innerHTML);
//            }
//        });
//    }

    var reading_id, meter_num, value, notes, obj;  
    // pop up 'edit readings' modal in admin interface
    $('.btn-edit').click(function() {
        reading_id = $(this).parent().nextAll().eq(0).text();
        meter_num = $(this).parent().prevAll().eq(3).text();
        value = $(this).parent().prevAll().eq(2).text();
        notes = $(this).parent().prevAll().eq(1).text();
        //var month = $('#')
        
        if ($(this).parent().parent().eq(0).children().attr('rowspan') == null) {
            var i = 0;
            while ($(this).parent().parent().prevAll().eq(i).children().attr('rowspan') == null) {
                //anti inf loop protection(just in case)
                if (i == 1e3) {
                    break;
                }
                i++;
            }
            var obj_typ = $(this).parent().parent().prevAll().eq(i).children().first().text();
        } else {
            var obj_typ = $(this).parent().parent().children().first().text(); // first() is eq(0)
        }
        
        obj = obj_typ + ' ' + $(this).parent().prevAll().eq(4).text();
        
        $('#objName').text(obj);
        $('#meterNum').text(meter_num);
        $('#value').val(value);
        $('#notes').val(notes);
        
        close_modal('#edit');
    });
    
    // 'save' button in 'edit readings' modal in admin iface
    $('#editSave').click(function() {
        var new_value = $('#value').val();
        var new_notes = $('#notes').val();
        //save edit modal -> db
        $.ajax({
            type: 'POST',
            url: 'edit_readings.php',
            data: {
                reading_id: reading_id,
                meter_num: meter_num,
                obj: obj,
                value: new_value,
                notes: new_notes
            },
            success: function(response) {
                //alert(response);
            },
            error: function() {
                alert('fail');
            }
        });
        location.reload();
    });
    
    //---------------------------------
    //----------closing modals---------
    //------------functions------------
    //--(admin's 'edit global' iface)--
    
    $('#btn_mod_addReg').click(function() {
        close_modal($('#modalRegions'));
    });

    $('#btn_mod_addStr').click(function() {
        close_modal($('#modalStructures'));
    });
    
    $('#btn_mod_addTus').click(function() {
        close_modal($('#modalTuses'));
    });

    $('#btn_mod_addOwn').click(function() {
        close_modal($('#modalOwners'));
    });

    $('#btn_mod_addMeterNum').click(function() {
        close_modal($('#modalMeterNums'));
    });

    $('#btn_mod_addMeterType').click(function() {
        close_modal($('#modalMeterTypes'));
    });

    $('#btn_mod_addMeter').click(function() {
        close_modal($('#modalMeters'));
    });

    $('#btn_mod_addMeterDate').click(function() {
        close_modal($('#modalMeterDates'));
    });

    $('#btn_mod_addAgr').click(function() {
        close_modal($('#modalAgrs'));
    });

    $('#btn_mod_addAgrRel').click(function() {
        close_modal($('#modalAgrRels'));
    });
    
    //---------------------------------
    //----------search fields----------
    //---------------------------------
    
    $('#find_meters_meterNum').keyup(function() {
        finder($('#find_meters_meterNum'), $('#meterInfo_table'), 0);
    });
    
    $('#find_meterDates_meterNum').keyup(function() {
        finder($('#find_meterDates_meterNum'), $('#meterDate_table'), 0);
    });
    
    $('#find_agrs_name').keyup(function() {
        finder($('#find_agrs_name'), $('#agr_table'), 1);
    });
    
    $('#find_agrRels_name').keyup(function() {
        finder($('#find_agrRels_name'), $('#rel_table'), 1);
    });
    
    $('#find_agrRels_meterNum').keyup(function() {
        finder($('#find_agrRels_meterNum'), $('#rel_table'), 3);
    });
    
    //--------------------
    // clear search by clicking 'x' button in search field or start searching by pushing ENTER
    // works well for all search fields over there
    $('input[type=search]').on('search', function () {
        var regex = new RegExp($(this).val());
        for (var i = 0; i < $('table').find('tbody').children().length; i++) {
            !!$('table').find('tbody').children().eq(i).children().eq(0).text().match(regex) ? 
                $('table').find('tbody').children().eq(i).prop('hidden', false) : 
                $('table').find('tbody').children().eq(i).prop('hidden', true);
        }
    });
});

        