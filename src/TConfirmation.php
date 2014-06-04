<?php
namespace Librette\ConfirmationDialog;

use Nette;
use Nette\Forms\Form;
use Nette\Utils\Random;

/**
 * @author David Matejka
 */
trait TConfirmation
{

	private $processingSignal = FALSE;


	public function signalReceived($signal)
	{
		$this->processingSignal = TRUE;
		$result = parent::signalReceived($signal);
		$this->processingSignal = FALSE;

		return $result;
	}


	protected function confirm($message = NULL)
	{
		if (!$this->processingSignal) {
			throw new \LogicException("You can use confirmation dialog only in handle* methods");
		}
		if ($message !== NULL) {
			$this['confirmationDialog']->message = $message;
		}

		/** @var Form $form */
		$form = $this['confirmationDialog']['form'];
		if ($form['ok']->isSubmittedBy()) {
			if ($form['token']->value !== $this->getConfirmationToken($this->getPresenter()->signal, $this->getParameters())) {
				throw new Nette\Application\UI\BadSignalException("Token is not valid");
			}

			return TRUE;
		} elseif ($form['cancel']->isSubmittedBy()) {
			$form['cancel']->onClick($form['cancel']);

			return FALSE;
		}
		$this['confirmationDialog']->enabled = TRUE;
		$form['token']->value = $this->getConfirmationToken($this->getPresenter()->signal, $this->getParameters());

		return FALSE;
	}


	protected function isConfirmationCancelled()
	{
		return $this['confirmationDialog']['form']['cancel']->isSubmittedBy();
	}


	protected function getConfirmationToken($signal, $parameters)
	{
		$sessionSection = $this->getPresenter()->getSession('Librette.ConfirmationDialog');
		if (!isset($sessionSection->token)) {
			$sessionSection->token = Random::generate(10);
		}

		return substr(md5(serialize([get_class($this), $signal, $parameters]) . $sessionSection->token), 0, 10);
	}


	protected function createComponentConfirmationDialog()
	{
		return new ConfirmationDialog();
	}

}
