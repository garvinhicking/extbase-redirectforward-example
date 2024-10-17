<?php

use GarvinHicking\RedirectForwardExample\Controller\DummyController;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || die('Access denied.');

call_user_func(
    function()
    {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'gh_redirectforward_example',
            'RedirectForwardExample',
            [
                DummyController::class => 'list,target,check,errormessage'
            ],
            // non-cacheable actions
            [
                DummyController::class => 'list,target,check,errormessage'
            ],
            ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
        );
    }
);
