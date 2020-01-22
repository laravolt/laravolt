<?php
$translator = \Jenssegers\Date\Date::getTranslator();

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
    $months[] = $translator->transChoice($key, 0);
}

$days = [];
foreach ($dayKeys as $key) {
    $days[] = mb_strtoupper(mb_substr($translator->trans($key), 0, 1));
}

$months = json_encode($months, JSON_UNESCAPED_UNICODE);
$days = json_encode($days, JSON_UNESCAPED_UNICODE);
?>

<script>
    $(function () {
        $('.ui.calendar.date').each(function (idx, elm) {
            elm = $(elm);
            var format = elm.data('datepicker-format');

            if (!format) {
                format = 'YYYY-MM-DD';
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
