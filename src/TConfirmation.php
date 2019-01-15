<?php
namespace Librette\ConfirmationDialog;

use Nette;
use Nette\Application\UI\BadSignalException;
use Nette\Application\UI\Presenter;
use Nette\Forms\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\Random;

/**
 * @author David Matejka
 * @mixin \Nette\Application\UI\Control
 */
trait TConfirmation
{

	/** @var ITranslator */
	protected $confirmationTranslator;

	/** @var ConfirmationDialogFactory */
	protected $confirmationDialogFactory;


	protected function confirm($question = NULL, $count = NULL, $parameters = [])
	{
		if ($question !== NULL && $translator = $this->getConfirmationTranslator()) {
			$question = $translator->translate($question, $count, $parameters);
		}

		/** @var ConfirmationDialog $confirmationDialog */
		$confirmationDialog = $this['confirmationDialog'];
		if ($question !== NULL) {
			$confirmationDialog->setQuestion($question);
		}

		/** @var Form $form */
		$form = $confirmationDialog['form'];
		if ($form['ok']->isSubmittedBy()) {
			if ($form['token']->value !== $this->getConfirmationToken()) {
				throw new BadSignalException('Token is not valid');
			}
			$form->validate();
			if (!$form->isValid()) {
				$confirmationDialog->setEnabled(TRUE);

				return FALSE;
			}

			return TRUE;
		} elseif ($form['cancel']->isSubmittedBy()) {
			$form['cancel']->onClick($form['cancel']);

			return FALSE;
		}
		$confirmationDialog->setEnabled(TRUE);
		$form['token']->value = $this->getConfirmationToken();

		return FALSE;
	}


	/**
	 * @return bool
	 */
	protected function isConfirmationCancelled()
	{
		return $this['confirmationDialog']['form']['cancel']->isSubmittedBy();
	}


	/**
	 * @return bool TRUE when confirmation was cancelled or successfully confirmed, otherwise FALSE
	 */
	protected function isConfirmationTerminated()
	{
		return $this->isConfirmationCancelled() || $this->confirm();
	}


	/**
	 * @return string
	 */
	private function getConfirmationToken()
	{
		$sessionSection = $this->getPresenter()->getSession('Librette.ConfirmationDialog');
		if (!isset($sessionSection->token)) {
			$sessionSection->token = Random::generate(10);
		}

		$parameters = $this instanceof Presenter ? $this->request->getParameters() : $this->getParameters();
		$signalIdentifier = [get_class($this), $this->getPresenter()->getSignal(), $parameters];

		return substr(md5(serialize($signalIdentifier) . $sessionSection->token), 0, 10);
	}


	protected function createComponentConfirmationDialog()
	{
		if ($this->confirmationDialogFactory !== NULL) {
			return $this->confirmationDialogFactory->create();
		} else {
			return new ConfirmationDialog();
		}
	}


	public function injectConfirmationDialogDependencies(ITranslator $translator = NULL, ConfirmationDialogFactory $confirmationDialogFactory = NULL)
	{
		$this->confirmationTranslator = $translator;
		$this->confirmationDialogFactory = $confirmationDialogFactory;
	}


	/**
	 * @return ITranslator|null
	 */
	private function getConfirmationTranslator()
	{
		if ($this->confirmationTranslator === NULL) {
			if (!($presenter = $this->getPresenter(FALSE))) {
				return NULL;
			}
			$this->confirmationTranslator = $presenter->getContext()->getByType(ITranslator::class, FALSE) ?: FALSE;
		}

		return $this->confirmationTranslator ?: NULL;
	}

}
