$(document).ready(function () {
    $('#envialia_pass').attr('type', 'password');
		$('#envialia_pass').prop('readonly', true);
		
		$( "#envialia_pass" ).focus(function() {
			$('#envialia_pass').prop('readonly', false);
		});		
    
    if ($('#envialia_env_grat_pen_on').is(':checked')) {
        $('#envialia_env_grat_pen_tipo_serv').prop('disabled', false);
        $('#envialia_env_grat_pen_imp_min').prop('disabled', false);
    }
    if ($('#envialia_env_grat_pen_off').is(':checked')) {
        $('#envialia_env_grat_pen_tipo_serv').prop('disabled', 'disabled');
        $('#envialia_env_grat_pen_imp_min').prop('disabled', 'disabled');
    }

    if ($('#envialia_env_grat_int_on').is(':checked')) {
        $('#envialia_env_grat_int_tipo_serv').prop('disabled', false);
        $('#envialia_env_grat_int_imp_min').prop('disabled', false);
    }

    if ($('#envialia_env_grat_int_off').is(':checked')) {
        $('#envialia_env_grat_int_tipo_serv').prop('disabled', 'disabled');
        $('#envialia_env_grat_int_imp_min').prop('disabled', 'disabled');
    }

    if ($('#envialia_perm_grab_pedido_on').is(':checked')) {
        $('#envialia_estado_pedido').prop('disabled', false);
    }

    if ($('#envialia_perm_grab_pedido_off').is(':checked')) {
        $('#envialia_estado_pedido').prop('disabled', 'disabled');
    }

    if ($("input:radio[name=envialia_conf_tar]:checked").val() == '0') {
        $('#envialia_tar_imp_fijo').prop('disabled', 'disabled');
    }
    if ($("input:radio[name=envialia_conf_tar]:checked").val() == '1') {
        $('#envialia_tar_imp_fijo').prop('disabled', 'disabled');
    }
    if ($("input:radio[name=envialia_conf_tar]:checked").val() == '2') {
        $('#envialia_tar_imp_fijo').prop('disabled', false);
    }

    if ($("input:radio[name=envialia_bultos]:checked").val() == '0') {
        $('#envialia_bultos_fijo_num').prop('disabled', false);
        $('#envialia_bultos_var_num').prop('disabled', 'disabled');
    }
    if ($("input:radio[name=envialia_bultos]:checked").val() == '1') {
        $('#envialia_bultos_fijo_num').prop('disabled', 'disabled');
        $('#envialia_bultos_var_num').prop('disabled', false);
    }
    if ($("input:radio[name=envialia_bultos]:checked").val() == '2') {
        $('#envialia_bultos_fijo_num').prop('disabled', 'disabled');
        $('#envialia_bultos_var_num').prop('disabled', 'disabled');
    }

    // Tipo estado para grabar pedido
    $('#envialia_perm_grab_pedido_on').on('change', function () {
        if ($('#envialia_perm_grab_pedido_on').is(':checked')) {
            $('#envialia_estado_pedido').prop('disabled', false);
        }
    });
    $('#envialia_perm_grab_pedido_off').on('change', function () {
        if ($('#envialia_perm_grab_pedido_off').is(':checked')) {
            $('#envialia_estado_pedido').prop('disabled', 'disabled');
        }
    });

    // Bultos
    $("input[name$='envialia_bultos']").on('change', function () {
        if ($("input:radio[name=envialia_bultos]:checked").val() == '0') {
            $('#envialia_bultos_fijo_num').prop('disabled', false);
            $('#envialia_bultos_var_num').prop('disabled', 'disabled');
        }
        if ($("input:radio[name=envialia_bultos]:checked").val() == '1') {
            $('#envialia_bultos_fijo_num').prop('disabled', 'disabled');
            $('#envialia_bultos_var_num').prop('disabled', false);
        }
        if ($("input:radio[name=envialia_bultos]:checked").val() == '2') {
            $('#envialia_bultos_fijo_num').prop('disabled', 'disabled');
            $('#envialia_bultos_var_num').prop('disabled', 'disabled');
        }
    });

    // Tarifa
    $("input[name$='envialia_conf_tar']").on('change', function () {
        if ($("input:radio[name=envialia_conf_tar]:checked").val() == '0') {
            $('#envialia_tar_imp_fijo').prop('disabled', 'disabled');
        }
        if ($("input:radio[name=envialia_conf_tar]:checked").val() == '1') {
            $('#envialia_tar_imp_fijo').prop('disabled', 'disabled');
        }
        if ($("input:radio[name=envialia_conf_tar]:checked").val() == '2') {
            $('#envialia_tar_imp_fijo').prop('disabled', false);
        }
    });

    // Envio gratis peninsular
    $('#envialia_env_grat_pen_on').on('change', function () {
        if ($('#envialia_env_grat_pen_on').is(':checked')) {
            $('#envialia_env_grat_pen_tipo_serv').prop('disabled', false);
            $('#envialia_env_grat_pen_imp_min').prop('disabled', false);
        }
    });
    $('#envialia_env_grat_pen_off').on('change', function () {
        if ($('#envialia_env_grat_pen_off').is(':checked')) {
            $('#envialia_env_grat_pen_tipo_serv').prop('disabled', 'disabled');
            $('#envialia_env_grat_pen_imp_min').prop('disabled', 'disabled');
        }
    });

    // Envio gratis internacional
    $('#envialia_env_grat_int_on').on('change', function () {
        if ($('#envialia_env_grat_int_on').is(':checked')) {
            $('#envialia_env_grat_int_tipo_serv').prop('disabled', false);
            $('#envialia_env_grat_int_imp_min').prop('disabled', false);
        }
    });
    $('#envialia_env_grat_int_off').on('change', function () {
        if ($('#envialia_env_grat_int_off').is(':checked')) {
            $('#envialia_env_grat_int_tipo_serv').prop('disabled', 'disabled');
            $('#envialia_env_grat_int_imp_min').prop('disabled', 'disabled');
        }
    });
});