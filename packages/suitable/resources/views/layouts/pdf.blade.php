<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title></title>
    <style>

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0px;
            border-left: 1px solid #e1eaf4;
            border-bottom: 1px solid #e1eaf4;
        }

        table thead th {
            background: #e1eaf4;
            padding: 0.92857143em 0.78571429em;
            font-size: 9pt;
        }

        /* Table Row */
        table tr td {
            padding: 0.75em;
            font-size: 9pt;
            border-right: 1px solid #e1eaf4;
        }

        table tr:nth-child(2n-1) td{
            background: #F9FAFB;
        }

        table td, table th {
            font-family: sans-serif;
        }

    </style>
</head>
<body>
{!! $table !!}
</body>
</html>
