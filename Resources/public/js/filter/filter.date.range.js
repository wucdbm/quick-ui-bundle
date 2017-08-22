$('.filter-date-range').on('click', '.date-range-clear', function (event) {
    var element = $(event.target).parents('.filter-date-range');
    element.find('.min').val('');
    element.find('.max').val('');
    element.find('.placeholder').val('');
    element.parents('form').submit();
    event.stopPropagation();
}).each(function (index, element) {
    var options = {
        opens: 'right',
        format: 'MM/DD/YYYY',
        separator: ' to ',
        showDropdowns: true,
        alwaysShowCalendars: true,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    };


    var minVal = $(element).find('.min').val();
    var maxVal = $(element).find('.max').val();

    if (minVal && maxVal) {
        options.startDate = moment(minVal, ['YYYY-MM-DD']);
        options.endDate = moment(maxVal, ['YYYY-MM-DD']);
        $(element).find('.placeholder').val(options.startDate.format('MMMM D, YYYY') + ' - ' + options.endDate.format('MMMM D, YYYY'));
    }

    $(element).daterangepicker(options, function (start, end) {
            var element = $(this.element);
            var placeholder = element.find('.placeholder');
            placeholder.val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            var min = element.find('.min');
            min.val(start.format('YYYY-MM-DD'));
            var max = element.find('.max');
            max.val(end.format('YYYY-MM-DD'));
            element.parents('form').submit();
        }
    )
});