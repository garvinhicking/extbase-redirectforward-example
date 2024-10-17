<?php
namespace GarvinHicking\RedirectForwardExample\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Frontend\Controller\ErrorController;

class DummyController extends ActionController
{
    public function listAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }

    public function initializeTargetAction(): void
    {
        DebuggerUtility::var_dump($this->arguments, title: 'Arguments (for follow-up mapping)');
        DebuggerUtility::var_dump($this->request->getArguments(), title: 'Request Arguments (unmapped, raw)');

        if ($this->request->hasArgument('propagateException') &&
            $this->request->getArgument('propagateException') === 'yes') {
            switch($this->request->getArgument('handling') ?? 'default') {
                case 'redirect':
                    DebuggerUtility::var_dump('redirect', title: 'Handling');
                    $response = GeneralUtility::makeInstance(ErrorController::class)->pageNotFoundAction(
                        $this->request,
                        'redirect'
                    );
                    throw new PropagateResponseException($response);

                case 'forward':
                    DebuggerUtility::var_dump('forward', title: 'Handling');

                    $response = GeneralUtility::makeInstance(ErrorController::class)->pageNotFoundAction(
                        $this->request,
                        'forward'
                    );
                    throw new PropagateResponseException($response);

                default:
                    DebuggerUtility::var_dump('default', title: 'Handling');
                    break;
            }
        }

        if ($this->request->hasArgument('errorHandling') &&
            ($this->request->getArgument('errorHandling') ?? 'default') === 'catch'
            && !is_string($this->request->getArgument('validity'))) {

            $this->request = $this->request->withAttribute('myArgumentIsInvalid', 'failure-code-1');

            // Unset an argument that is not validatable.
            unset($this->arguments['validity']);
        }
    }

    public function targetAction(
        ?string $handling = null,
        ?string $propagateException = null,
        ?string $validity = null,
        ?string $errorHandling = null
    ): ResponseInterface
    {
        DebuggerUtility::var_dump($handling, title: 'targetAction: handling');
        DebuggerUtility::var_dump($propagateException, title: 'targetAction: propagateException');
        DebuggerUtility::var_dump($validity, title: 'targetAction: validity');
        DebuggerUtility::var_dump($errorHandling, title: 'targetAction: errorHandling');

        DebuggerUtility::var_dump($this->request, 'Request context');
        DebuggerUtility::var_dump($this->request->getArguments(), 'Request arguments');

        if ($handling === 'forward') {
            return (new ForwardResponse('check'))->withArguments(['method' => 'forward']);
        }

        if ($handling === 'redirect') {
            $uri = $this->uriBuilder->reset()->uriFor('check', ['method' => 'redirect']);
            return new RedirectResponse($uri);
        }

        if ($this->request->getAttribute('myArgumentIsInvalid') === 'failure-code-1') {
            return new ForwardResponse('errormessage');
        }

        return $this->htmlResponse();
    }

    public function checkAction(?string $method = null): ResponseInterface
    {
        $this->view->assign('method', $method);
        return $this->htmlResponse();
    }

    public function errormessageAction(?string $method = null): ResponseInterface
    {
        $this->view->assign('method', $method);
        return $this->htmlResponse();
    }

}
