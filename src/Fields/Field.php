<?php

declare(strict_types=1);

namespace Laravolt\Fields;

interface Field
{
    public const BELONGS_TO = 'laravolt.fields.belongsTo';

    public const ACTION = 'action';

    public const BOOLEAN = 'boolean';

    public const BUTTON = 'button';

    public const CHECKBOX = 'checkbox';

    public const CHECKBOX_GROUP = 'checkboxGroup';

    public const COLOR = 'color';

    public const DATE = 'date';

    public const DATE_PICKER = 'datepicker';

    public const DATETIME_PICKER = 'datetimepicker';

    public const DROPDOWN = 'dropdown';

    public const DROPDOWN_COLOR = 'dropdownColor';

    public const DROPDOWN_DB = 'dropdownDB';

    public const EMAIL = 'email';

    public const HIDDEN = 'hidden';

    public const HTML = 'html';

    public const NUMBER = 'number';

    public const MULTIROW = 'multirow';

    public const PASSWORD = 'password';

    public const RADIO_GROUP = 'radioGroup';

    public const REDACTOR = 'redactor';

    public const RESTFUL_BUTTON = 'restfulButton';

    public const RUPIAH = 'rupiah';

    public const SEGMENT = 'segment';

    public const SUBMIT = 'submit';

    public const TEXT = 'text';

    public const TEXTAREA = 'textarea';

    public const TIME = 'time';

    public const UPLOADER = 'uploader';
}
