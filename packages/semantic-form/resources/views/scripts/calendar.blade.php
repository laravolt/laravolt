<?php
$catalogue = \Jenssegers\Date\Date::getTranslator()->getCatalogue();

$monthKeys = [
        'january',
        'february',
        'march',
        'april',
        'may',
        'june',
        'july',
        'august',
        'september',
        'october',
        'november',
        'december'
];
$dayKeys = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

$months = [];
foreach ($monthKeys as $key) {
    $months[] = $catalogue->get($key);
}

$days = [];
foreach ($dayKeys as $key) {
    $days[] = strtoupper(substr($catalogue->get($key), 0, 1));
}

$months = json_encode($months);
$days = json_encode($days);
?>

<script>
    $(function () {
        $('.ui.calendar.date').each(function (idx, elm) {
            elm = $(elm);
            var format = elm.data('datepicker-format');

            if(!format) {
                format = 'YYYY/MM/DD';
            }

            elm.calendar({
                type: 'date',
                formatter: {
                    date: function (date, settings) {
                        if (!date) {
                            return '';
                        }
                        var DD = ("0" + date.getDate()).slice(-2);
                        var MM = ("0" + (date.getMonth() + 1)).slice(-2);
                        var MMMM = settings.text.months[date.getMonth()];
                        var YY = date.getFullYear().toString().substr(2, 2);
                        var YYYY = date.getFullYear();

                        return format.replace('DD', DD).replace('MMMM', MMMM).replace('MM', MM).replace('YYYY', YYYY).replace('YY', YY);
                    }
                },
                text: {
                    days: {!! $days !!},
                    months: {!! $months !!}
                }
            });
        });
    });
</script>
