<?php
namespace Librette\ConfirmationDialog;

use Nette;
use Nette\Forms\Controls;
use Nette\Forms\Form;

/**
 * @author David Matejka
 */
class ConfirmationForm extends Form
{

	protected function validateParent(Nette\ComponentModel\IContainer $parent)
	{
		parent::validateParent($parent);
		$this->monitor('Nette\Application\UI\Presenter');
	}


	protected function attached($obj)
	{
		parent::attached($obj);
		if ($obj instanceof Nette\Application\UI\Presenter) {
			$this->httpRequest = $obj->getContext()->getByType('Nette\Http\IRequest');
			$this->getElementPrototype()->action = substr($this->httpRequest->getUrl(), strlen($this->httpRequest->getUrl()->hostUrl));
			$name = $this->lookupPath('Nette\Application\UI\Presenter');
			if (!isset($this->getElementPrototype()->id)) {
				$this->getElementPrototype()->id = 'frm-' . $name;
			}
			$tracker = new Controls\HiddenField($name);
			$tracker->setOmitted();
			$this[self::TRACKER_ID] = $tracker;
			if (iterator_count($this->getControls()) && $this->isSubmitted()) {
				foreach ($this->getControls() as $control) {
					if (!$control->isDisabled()) {
						$control->loadHttpData();
					}
				}
			}
		}
	}


	public function isAnchored()
	{
		return (bool) $this->lookup('Nette\Application\UI\Presenter', FALSE);
	}

}