$(document).ready(function() {

	$(document).on('click','.menu-item', function () {
        if ($('#myNavbar').hasClass('in')) $('.navbar-toggle').click();
	    $('.navbar-nav li').removeClass('active');
		$(this).addClass('active');
		$.get("index.php",{
		    do: $(this).data('page')
        }, function (data) {
			$('#wrapper').html(data.html);
			$('select:not(.template)').select2();
		}, 'json');
	});

    // ----------- Food input -----------

    $(document).on('change','#food-groups', function () {
        if ($(this).val() != "") {
            $('#loader').addClass('loading');
            $.get("index.php", {
                do: 'food-input',
                stage: 'food-items',
                group_id: $(this).val()
            }, function (data) {
                $('#wrapper').html(data.html);
                $('.food-items-row').show();
                $('select').select2();
                $('#loader').removeClass('loading');
            }, 'json');
        } else {
            $('#food-items').html();
            $('#food-details input').val('');
            $('.food-items-row').hide();
            $('#food-details').hide();
        }
    });

    $(document).on('change','#food-items', function () {
        if ($(this).val() != "") {
            var group_id = $('#food-groups').val();
            $('#loader').addClass('loading');
            $.get("index.php", {
                do: 'food-input',
                stage: 'that-food',
                food_id: $(this).val(),
                group_id: group_id
            }, function (data) {
                $('#wrapper').html(data.html);
                $('.food-items-row').show();
                $('#food-details').show();
                $('select').select2();
                $('#loader').removeClass('loading');
            }, 'json');
        } else {
            $('#food-details input').val('');
            $('#food-details').hide();
        }
    });

    $(document).on('click','#food-details #save', function () {
        if ($('#food-details #name_sr').val() != "") {
            $('#loader').addClass('loading');
            $.post("index.php?do=food-input&stage=save-food&food_id=" + $('#fid').val(), {
                name_sr: $('#name_sr').val(),
                name_en: $('#name_en').val(),
                price: $('#price').val(),
                refuse: $('#refuse').val(),
                unit: $('#unit').val(),
                data: $('#data').val()
            }, function (data) {
                toastr.options = {
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": false,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": "300",
                    "hideDuration": "500",
                    "timeOut": "3000",
                    "extendedTimeOut": "500",
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };
                if (data.state == 'ok') {
                    toastr["success"]("Uspešno zapamćena namirnica", "Uspeh");
                } else {
                    toastr["error"]("Došlo je do greške", "Greška");
                }
                $('#wrapper').html(data.html);
                $('#loader').removeClass('loading');
            }, 'json');
        } else {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": false,
                "progressBar": false,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "500",
                "timeOut": "2000",
                "extendedTimeOut": "500",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
            toastr["info"]("Neophodno je da se popuni polje za naziv na srpskom jeziku", "info");
        }
    });

    // ----------- RDI input -----------

    $(document).on('click','.rdi-input-module .save', function () {
        $('#loader').addClass('loading');
        var name_sr = $(this).siblings('.name_sr').val();
        var nid = $(this).data('id');
        $.post("index.php?do=rdi-input&stage=save&nid=" + nid, {
            name_sr: name_sr,
            rdi: $(this).siblings('.rdi').val()
        }, function (data) {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": false,
                "progressBar": false,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "500",
                "timeOut": "3000",
                "extendedTimeOut": "500",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
            if (data.state == 'ok') {
                toastr["success"]("Uspešno zapamćen nutrijent: " + name_sr, "Uspeh");
            } else {
                toastr["error"]("Došlo je do greške", "Greška");
            }
            $('#wrapper').html(data.html);
            $('#loader').removeClass('loading');
        }, 'json');
    });

    // ----------- Indi calc -----------

    $(document).on('change','#foods', function () {
        $selectedItem = $('#foods').find(':selected');
        if ($(this).val() != '') {
            $('#price').val($selectedItem.data('price'));
            $('#refuse').val($selectedItem.data('refuse'));
            $('#food-details').show();
        } else {
            $('#food-details').hide();
        }
    });

    $(document).on('click','.indi-calc-module #calc', function () {
        $('#loader').addClass('loading');
        $selectedItem = $('#foods').find(':selected');
        $.get("index.php", {
            do: 'indi-calc',
            stage: 'calc',
            food_id: $selectedItem.val(),
            weight: $('#weight').val(),
            price: $selectedItem.data('price'),
            refuse: $selectedItem.data('refuse')
        }, function (data) {
            $('#wrapper').html(data.html);
            $('#loader').removeClass('loading');
        }, 'json');
    });

    $(document).on('click','.indi-report-module #detailed, .smooth-it-report-module #detailed', function () {
        if ($(this).data('shows') == 'basic') {
            $('.n_full').toggle();
            $(this).find('.btn-text').html($(this).data('basic'));
            $(this).find('i').prop('class', 'fa fa-toggle-up');
            $(this).data('shows', 'detailed');
        } else {
            $('.n_full').toggle();
            $(this).find('.btn-text').html($(this).data('detailed'));
            $(this).find('i').prop('class', 'fa fa-toggle-down');
            $(this).data('shows', 'basic');
        }
    });

        // ----------- Smooth it -----------

    $(document).on('change','.food-select', function (e) {
        $selectedItem = $(this).find(':selected');
        $indi_food_container = $(this).closest('.indi-food-container');
        if ($(this).val() != '') {
            $indi_food_container.find('.price').val($selectedItem.data('price'));
            $indi_food_container.find('.refuse').val($selectedItem.data('refuse'));
            $indi_food_container.find('.food-params').removeClass('hidden');
        } else {
            $indi_food_container.find('.food-params').addClass('hidden');
        }
    });

    $(document).on('click','.smooth-it-module #add', function () {
        $('.foods-container').append($('#template').html());
        $('.foods-container select').select2();
    });

    $(document).on('click','.smooth-it-module #calc', function () {
        var $data = [];
        var $foods = $('.foods-container').children();
        $foods.each(function() {
            food = {
                'food_id': $(this).find('.food-select').val(),
                'weight': $(this).find('.weight').val(),
                'price':  $(this).find('.price').val(),
                'refuse':  $(this).find('.refuse').val()
            };
            $data.push(food);
        });
        var parsedData = JSON.stringify($data);

        $('#loader').addClass('loading');
        $.post("index.php?do=smooth-it&stage=calc", {
            data: parsedData
        }, function (data) {
            $('#wrapper').html(data.html);
            $('#loader').removeClass('loading');
        }, 'json');
    });




    $(document).on('click','#task-btn', function () {
        var task = $('#task').val();
        $.get("index.php",{do: "unos", task: task}, function (data) {
            if (data.callback == 'ok') {
                location.reload();
            } else {
                alert('There was an error while trying to save a new task');
            }
        }, 'json');
    });

    $(document).on('click','.checkbox', function () {
        var t_id = $(this).attr('id');
        var checked_state = $(this).is(':checked');
        var done;
        var $title = $(this).next();
        if (checked_state == true) {
            $title.addClass('done');
            done = 1;
        } else {
            $title.removeClass('done');
            done = 0;
        }
        $.get("index.php",{do: "done", t_id: t_id, done: done}, function (data) {
            if (data.callback != 'ok') {
                console.log('error');
            }
        }, 'json');
    });

    $(document).on('click','#logout', function () {
        $.get("index.php", {do: "logout"});
        location.reload();
    });
});