<?php
namespace Librette\ConfirmationDialog;

use Nette\Application\UI\Presenter;
use Nette\ComponentModel\IContainer;
use Nette\Forms\Controls;
use Nette\Forms\Form;
use Nette\Http\IRequest;

/**
 * @author David Matejka
 */
class ConfirmationForm extends Form
{

	/**
	 * @param IRequest|null
	 */
	public function __construct(IRequest $httpRequest = NULL)
	{
		parent::__construct();
		$this->httpRequest = $httpRequest;
	}


	protected function validateParent(IContainer $parent)
	{
		parent::validateParent($parent);
		$this->monitor(Presenter::class);
	}


	protected function attached($obj)
	{
		parent::attached($obj);
		if ($obj instanceof Presenter) {
			if ($this->httpRequest === NULL) {
				$this->httpRequest = $obj->getContext()->getByType(IRequest::class);
			}
			$this->getElementPrototype()->action = substr($this->httpRequest->getUrl(), strlen($this->httpRequest->getUrl()->hostUrl));
			$name = $this->lookupPath(Presenter::class);
			if (!isset($this->getElementPrototype()->id)) {
				$this->getElementPrototype()->id = 'frm-' . $name;
			}
			$this[self::TRACKER_ID] = (new Controls\HiddenField($name))->setOmitted();
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
		return (bool) $this->lookup(Presenter::class, FALSE);
	}

}
