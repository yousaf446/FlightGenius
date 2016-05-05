$(document).ready(function() {
    'use strict';

    /**
     * Bootstrap Select
     */
    $('select').selectpicker();

    /**
     * Background image
     */
    $('*[data-background-image]').each(function() {
        $(this).css({
            'background-image': 'url(' + $(this).data('background-image') + ')'
        });
    });

    /**
     * Bootstrap Tooltip
     */
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
        var depart_date = $('#depart_date').datepicker({
            startDate: '3d'
        }).on('changeDate', function(e) {
            $('#arrive_date').datepicker({
                startDate: e.date
            });
        });
        var arrive_date = $('#arrive_date').datepicker({
            startDate: '3d'
        });
    })
});

function ArriveDiv(method) {
    if(method == 'show')
        $("#arriveDiv").show();
    else if(method == 'hide')
    $("#arriveDiv").hide();
}

function loader() {
    $("#loader").show();
    $("#search").hide();
}