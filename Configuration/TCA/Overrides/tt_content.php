<?php

defined('TYPO3') or die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

ExtensionUtility::registerPlugin(
    'gh_redirectforward_example',
    'RedirectForwardExample',
    'GH Redirect-Forward Example'
);

