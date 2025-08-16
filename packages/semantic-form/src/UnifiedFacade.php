<?php

namespace Laravolt\SemanticForm;

use Illuminate\Support\Facades\Facade;

/**
 * Unified Form Facade that works with both SemanticForm and PrelineForm
 * 
 * This facade automatically delegates to the configured form builder
 * and provides a consistent API for form building regardless of the
 * underlying UI framework.
 * 
 * @method static \Laravolt\SemanticForm\Elements\FormOpen open($action = null, $model = null)
 * @method static \Laravolt\SemanticForm\Elements\FormOpen get($url = null)
 * @method static \Laravolt\SemanticForm\Elements\FormOpen post($url = null)
 * @method static \Laravolt\SemanticForm\Elements\FormOpen put($url = null)
 * @method static \Laravolt\SemanticForm\Elements\FormOpen patch($url = null)
 * @method static \Laravolt\SemanticForm\Elements\FormOpen delete($url = null)
 * @method static string close()
 * @method static \Laravolt\SemanticForm\Elements\Text text($name, $defaultValue = null)
 * @method static \Laravolt\SemanticForm\Elements\Email email($name, $defaultValue = null)
 * @method static \Laravolt\SemanticForm\Elements\Password password($name)
 * @method static \Laravolt\SemanticForm\Elements\Number number($name, $defaultValue = null)
 * @method static \Laravolt\SemanticForm\Elements\TextArea textarea($name, $defaultValue = null)
 * @method static \Laravolt\SemanticForm\Elements\Select select($name, $options = [], $defaultValue = null)
 * @method static \Laravolt\SemanticForm\Elements\SelectMultiple selectMultiple($name, $options = [], $defaultValue = null)
 * @method static \Laravolt\SemanticForm\Elements\Checkbox checkbox($name, $value = 1, $checked = null)
 * @method static \Laravolt\SemanticForm\Elements\RadioButton radio($name, $value = null, $checked = null)
 * @method static \Laravolt\SemanticForm\Elements\RadioGroup radioGroup($name, $options = [], $checkedOption = null)
 * @method static \Laravolt\SemanticForm\Elements\CheckboxGroup checkboxGroup($name, $options = [], $checkedOptions = [])
 * @method static \Laravolt\SemanticForm\Elements\File file($name)
 * @method static \Laravolt\SemanticForm\Elements\Hidden hidden($name, $value = null)
 * @method static \Laravolt\SemanticForm\Elements\Button button($value = 'Button')
 * @method static \Laravolt\SemanticForm\Elements\Button submit($value = 'Submit')
 * @method static \Laravolt\SemanticForm\Elements\Date date($name, $defaultValue = null)
 * @method static \Laravolt\SemanticForm\Elements\Time time($name, $defaultValue = null)
 * @method static \Laravolt\SemanticForm\Elements\Color color($name, $defaultValue = null)
 * @method static \Laravolt\SemanticForm\Elements\InputWrapper input($name, $defaultValue = null)
 * @method static mixed bind($model)
 * @method static mixed switchTo($driver)
 * @method static string getCurrentDriver()
 * @method static array getAvailableDrivers()
 * @method static array getBuilderInfo($driver = null)
 * @method static mixed driver($driver = null)
 */
class UnifiedFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'form-manager';
    }

    /**
     * Switch to SemanticForm builder.
     *
     * @return mixed
     */
    public static function semantic()
    {
        return static::switchTo('semantic');
    }

    /**
     * Switch to PrelineForm builder.
     *
     * @return mixed
     */
    public static function preline()
    {
        return static::switchTo('preline');
    }

    /**
     * Get information about the current form builder.
     *
     * @return array
     */
    public static function info()
    {
        return static::getBuilderInfo();
    }

    /**
     * Check if the current builder is SemanticForm.
     *
     * @return bool
     */
    public static function isSemantic()
    {
        return static::getCurrentDriver() === 'semantic';
    }

    /**
     * Check if the current builder is PrelineForm.
     *
     * @return bool
     */
    public static function isPreline()
    {
        return static::getCurrentDriver() === 'preline';
    }

    /**
     * Auto-detect and switch to the best form builder.
     *
     * @return mixed
     */
    public static function autoDetect()
    {
        $manager = static::getFacadeRoot();
        $bestDriver = $manager->autoDetect();
        
        return $manager->driver($bestDriver);
    }
}