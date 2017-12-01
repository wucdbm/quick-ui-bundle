$(document).on('refresh', '[data-refresh]', function (e, params) {
    var action = $(this).data('refresh');
    if (!action) {
        if (bootbox) {
            bootbox.alert('An action was completed, but the elements on the page were not refreshed.');
        }
        return;
    }
    params = params || {};
    if (params.element) {
        $(params.element).tooltip('hide');
        $(params.element).popover('hide');
    }
    $(this).block({
        message: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>',
        element: $(this)
    });
    var that = this;
    $.ajax({
        url: action,
        type: 'GET'
    }).done(function (html) {
        $(that).unblock();
        $(that).html(html);
        $(that).trigger('refreshed');
    });
});
$(document).on('click', '[data-trigger="refresh"]', function (e) {
    $(this).parents('[data-refresh]').trigger('refresh', {
        element: $(this)
    });
});
$(document).on('ajax-action', 'a', function (e) {
    e.preventDefault();
    if ($(this).data('disabled')) {
        return;
    }
    $(this).data('disabled', true);
    var that = this;
    var action = $(this).attr('href');
    var html = $(this).html();
    $(this).find('i').remove();
    $(this).prepend('<i class="fa fa-spinner fa-pulse"></i>');
    $.ajax({
        url: action,
        type: 'POST',
        dataType: 'json'
    }).done(function (data) {
        $(that).data('disabled', false);
        $(that).html(html);
        $.ajaxHandler.handle(data, that);
    });
    return false;
});
$(document).on('ajax-action', 'form', function (e) {
    e.preventDefault();
    if ($(this).data('disabled')) {
        return;
    }
    $(this).data('disabled', true);
    $(this).block();
    var that = this;
    var action = $(this).attr('action');
    var method = $(this).attr('method');
    var button = $(this).find('button[type="submit"]:focus');
    var html = button.html();
    //$(this).data('clicked-btn', this);
    $(this).data('clicked-btn-html', html);
    button.find('i').remove();
    button.prepend('<i class="fa fa-spinner fa-pulse"></i>');
    $.ajax({
        url: action,
        type: method,
        data: $(this).serialize(),
        dataType: 'json'
    }).done(function (data) {
        $(that).data('disabled', false);

        //var button = $(that).data('clicked-btn');
        var html = $(that).data('clicked-btn-html');
        $(button).html(html).data('original-html', true);
        $(that).unblock(that);
        $.ajaxHandler.handle(data, that);
    });
    return false;
});
$(document).on('click', 'a.ajax-action, [data-ajax-action="true"]', function (e) {
    e.preventDefault();
    $(this).trigger('ajax-action');
});
$(document).on('submit', 'form.ajax-action, [data-ajax-action="true"]', function (e) {
    e.preventDefault();
    $(this).trigger('ajax-action');
});
