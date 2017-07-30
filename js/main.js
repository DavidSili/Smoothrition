$(document).ready(function() {

	$(document).on('click','.menu-item', function () {
        if ($('#myNavbar').hasClass('in')) $('.navbar-toggle').click();
	    $('.navbar-nav li').removeClass('active');
		$(this).addClass('active');
		$.get("index.php",{do: $(this).data('page')}, function (data) {
			$('#wrapper').html(data.html);
			$('select').select2();
		}, 'json');
	});

// Food input

    $(document).on('change','#food-groups', function () {
        if ($(this).val() != "") {
            $('#loader').addClass('loading');
            $.get("index.php", {do: 'food-input', stage: 'food-items', group_id: $(this).val()}, function (data) {
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
            $.get("index.php", {do: 'food-input', stage: 'that-food', food_id: $(this).val(), group_id: group_id}, function (data) {
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