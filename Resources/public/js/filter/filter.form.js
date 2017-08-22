$(document).on('change', 'form.autosubmit input, form.autosubmit select', function () {
    $(this).parents('form').submit();
});

$(document).on('click', '[data-trigger="filter"]', function () {
    var element = $(this);
    var field = element.data('filter-field');
    var value = element.data('filter-value');
    $('#' + field).val(value).trigger('change');
});