<?php
/**
 * @file classes/components/form/context/AccessForm.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2000-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class AccessForm
 *
 * @ingroup classes_controllers_form
 *
 * @brief A preset form for configuring the terms under which a server will
 *  allow access to its published content.
 */

namespace APP\components\forms\context;

use APP\server\Server;
use PKP\components\forms\FieldOptions;
use PKP\components\forms\FormComponent;

class AccessForm extends FormComponent
{
    public const FORM_ACCESS = 'access';
    public $id = self::FORM_ACCESS;
    public $method = 'PUT';

    /**
     * Constructor
     *
     * @param string $action URL to submit the form to
     * @param array $locales Supported locales
     * @param Server $context Journal, Server or Press to change settings for
     */
    public function __construct($action, $locales, $context)
    {
        $this->action = $action;
        $this->locales = $locales;

        $this->addField(new FieldOptions('publishingMode', [
            'label' => __('manager.distribution.publishingMode'),
            'type' => 'radio',
            'options' => [
                ['value' => Server::PUBLISHING_MODE_OPEN, 'label' => __('manager.distribution.publishingMode.openAccess')],
                ['value' => Server::PUBLISHING_MODE_NONE, 'label' => __('manager.distribution.publishingMode.none')],
            ],
            'value' => $context->getData('publishingMode'),
        ]))
            ->addField(new FieldOptions('enableOai', [
                'label' => __('manager.setup.enableOai'),
                'description' => __('manager.setup.enableOai.description'),
                'type' => 'radio',
                'options' => [
                    ['value' => true, 'label' => __('common.enable')],
                    ['value' => false, 'label' => __('common.disable')],
                ],
                'value' => $context->getData('enableOai'),
            ]));
    }
}
