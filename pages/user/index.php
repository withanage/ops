<?php

/**
 * @defgroup pages_user User Pages
 */

/**
 * @file pages/user/index.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @ingroup pages_user
 *
 * @brief Handle requests for user functions.
 *
 */

switch ($op) {
    //
    // Misc.
    //
    case 'index':
    case 'authorizationDenied':
    case 'toggleHelp':
    case 'getInterests':
        return new APP\pages\user\UserHandler();
    default:
        return require_once('lib/pkp/pages/user/index.php');
}
