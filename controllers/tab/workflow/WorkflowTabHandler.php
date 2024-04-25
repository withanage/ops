<?php

/**
 * @file controllers/tab/workflow/WorkflowTabHandler.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class WorkflowTabHandler
 *
 * @ingroup controllers_tab_workflow
 *
 * @brief Handle AJAX operations for workflow tabs.
 */

namespace APP\controllers\tab\workflow;

use APP\core\Application;
use APP\notification\Notification;
use APP\template\TemplateManager;
use Exception;
use PKP\controllers\tab\workflow\PKPWorkflowTabHandler;
use PKP\decision\DecisionType;
use PKP\plugins\Hook;
use PKP\security\Role;
use PKP\stageAssignment\StageAssignment;

class WorkflowTabHandler extends PKPWorkflowTabHandler
{
    /**
     * @copydoc PKPWorkflowTabHandler::fetchTab
     *
     * @hook Publication::testAuthorValidatePublish [[&$errors, $assignment->getUserId(), $context->getId(), $submission->getId()]]
     */
    public function fetchTab($args, $request)
    {
        $this->setupTemplate($request);
        $templateMgr = TemplateManager::getManager($request);
        $stageId = $this->getAuthorizedContextObject(Application::ASSOC_TYPE_WORKFLOW_STAGE);
        $submission = $this->getAuthorizedContextObject(Application::ASSOC_TYPE_SUBMISSION);
        switch ($stageId) {
            case WORKFLOW_STAGE_ID_PRODUCTION:
                $errors = [];
                $context = $request->getContext();

                $submitterAssignments = StageAssignment::withSubmissionIds([$submission->getId()])
                    ->withRoleIds([Role::ROLE_ID_AUTHOR])
                    ->get();

                foreach ($submitterAssignments as $submitterAssignment) {
                    Hook::call('Publication::testAuthorValidatePublish', [&$errors, $submitterAssignment->userId, $context->getId(), $submission->getId()]);
                }

                if (!empty($errors)) {
                    $authorPublishRequirements = '';
                    foreach ($errors as $error) {
                        $authorPublishRequirements .= $error . "<br />\n";
                    }
                    $templateMgr->assign('authorPublishRequirements', $authorPublishRequirements);
                }
                break;
        }
        return parent::fetchTab($args, $request);
    }

    /**
     * Get all production notification options to be used in the production stage tab.
     *
     * @param int $submissionId
     *
     * @return array
     */
    protected function getProductionNotificationOptions($submissionId)
    {
        return [
            Notification::NOTIFICATION_LEVEL_NORMAL => [
                Notification::NOTIFICATION_TYPE_VISIT_CATALOG => [Application::ASSOC_TYPE_SUBMISSION, $submissionId],
                Notification::NOTIFICATION_TYPE_AWAITING_REPRESENTATIONS => [Application::ASSOC_TYPE_SUBMISSION, $submissionId],
            ],
            Notification::NOTIFICATION_LEVEL_TRIVIAL => []
        ];
    }

    protected function getNewReviewRoundDecisionType(int $stageId): DecisionType
    {
        throw new Exception('Tried to get a review round decision type in OPS which does not support review rounds.');
    }
}
