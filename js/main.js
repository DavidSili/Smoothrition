$(document).ready(function() {

	$(document).on('click','.menu-item', function () {
		$('.navbar-nav li').removeClass('active');
		$(this).addClass('active');
		$.get("index.php",{do: $(this).data('page')}, function (data) {
			$('#wrapper').html(data.html);
			$('select').select2();
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