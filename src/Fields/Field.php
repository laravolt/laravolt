<?php

namespace Laravolt\Fields;

interface Field
{
    const BELONGS_TO = 'laravolt.fields.belongsTo';
    const ACTION = 'action';
    const BOOLEAN = 'boolean';
    const BUTTON = 'button';
    const CHECKBOX = 'checkbox';
    const CHECKBOX_GROUP = 'checkboxGroup';
    const COLOR = 'color';
    const DATE = 'date';
    const DATE_PICKER = 'datepicker';
    const DATETIME_PICKER = 'datetimepicker';
    const DROPDOWN = 'dropdown';
    const DROPDOWN_COLOR = 'dropdownColor';
    const DROPDOWN_DB = 'dropdownDB';
    const EMAIL = 'email';
    const HIDDEN = 'hidden';
    const HTML = 'html';
    const NUMBER = 'number';
    const MULTIROW = 'multirow';
    const PASSWORD = 'password';
    const RADIO_GROUP = 'radioGroup';
    const REDACTOR = 'redactor';
    const RESTFUL_BUTTON = 'restfulButton';
    const RUPIAH = 'rupiah';
    const SEGMENT = 'segment';
    const SUBMIT = 'submit';
    const TEXT = 'text';
    const TEXTAREA = 'textarea';
    const TIME = 'time';
    const UPLOADER = 'uploader';
}
