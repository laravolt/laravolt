<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title></title>
    <style>
        /*******************************
                     Table
        *******************************/
        table {
            width: 100%;
            border: 1px solid #999;
            border-collapse: separate;
            border-spacing: 0px;
        }

        table thead th {
            background: #F9FAFB;
            color: rgba(0, 0, 0, 0.87);
            padding: 0.92857143em 0.78571429em;
        }

        table thead tr > th:first-child {
            border-left: none;
        }

        table thead tr:first-child > th:first-child {
            border-radius: 0.28571429rem 0em 0em 0em;
        }

        table thead tr:first-child > th:last-child {
            border-radius: 0em 0.28571429rem 0em 0em;
        }

        table thead tr:first-child > th:only-child {
            border-radius: 0.28571429rem 0.28571429rem 0em 0em;
        }

        /* Footer */
        table tfoot {
            box-shadow: none;
        }

        table tfoot th {
            border-top: 1px solid rgba(34, 36, 38, 0.15);
            background: #F9FAFB;
            color: rgba(0, 0, 0, 0.87);
            padding: 0.78571429em 0.78571429em;
            vertical-align: middle;
            font-style: normal;
            font-weight: normal;
            text-transform: none;
        }

        table tfoot tr > th:first-child {
            border-left: none;
        }

        /* Table Row */
        table tr td {
            border-top: 1px solid #999;
        }

        /* Table Cells */
        table td {
            padding: 0.5em;
        }
    </style>
</head>
<body>
{!! $table !!}
</body>
</html>
