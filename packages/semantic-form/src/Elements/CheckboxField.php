<?php namespace Laravolt\SemanticForm\Elements;

use AdamWathan\Form\Elements\Label;
use AdamWathan\Form\Elements\Element;

class CheckboxField extends FormGroup
{
	protected $label;
	protected $control;
	protected $inline = false;

	public function __construct(Label $label, Element $control)
	{
		$this->label = $label;
		$this->control = $control;
		$this->addClass('field');
	}

	public function render()
	{
		if ($this->inline === true) {
			return $this->label->render();
		}

		$html  = '<div';
		$html .= $this->renderAttributes();
		$html .= '>';
		$html .= '<div class="ui checkbox">';
		$html .=  $this->control;
		$html .=  $this->label;
		$html .= $this->renderHelpBlock();

		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	public function inline()
	{
		$this->inline = true;

		$class = $this->control()->getAttribute('type') . '-inline';
		$this->label->removeClass('control-label')->addClass($class);

		return $this;
	}

	public function control()
	{
		return $this->control;
	}

	public function setChecked($checked)
	{
		$checked ? $this->control()->check(): $this->control()->uncheck();

		return $this;
	}

	public function __call($method, $parameters)
	{
		call_user_func_array(array($this->control, $method), $parameters);
		return $this;
	}
}
