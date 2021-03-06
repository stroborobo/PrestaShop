<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;

/**
 * FrameworkBundleAdminController that extends The Symfony framework bundle controller
 */
class FrameworkBundleAdminController extends Controller
{
    /**
     * This function returns form errors for JS implementation
     * Parse all errors mapped by id html field
     *
     * @param Form $form The form
     *
     * @return array[array[string]] Errors
     */
    public function getFormErrorsForJS(Form $form)
    {
        $errors = [];

        if (empty($form)) {
            return $errors;
        }

        $translator = $this->container->get('prestashop.twig.extension.translation');

        foreach ($form->getErrors(true) as $error) {
            if (!$error->getCause()) {
                $form_id = 'bubbling_errors';
            } else {
                $form_id = str_replace(
                    ['.', 'children[', ']', '_data'],
                    ['_', '', '', ''],
                    $error->getCause()->getPropertyPath()
                );
            }

            if ($error->getMessagePluralization()) {
                $errors[$form_id][] = $translator->transchoice(
                    $error->getMessageTemplate(),
                    $error->getMessagePluralization(),
                    $error->getMessageParameters(),
                    'form_error'
                );
            } else {
                $errors[$form_id][] = $translator->trans(
                    $error->getMessageTemplate(),
                    $error->getMessageParameters(),
                    'form_error'
                );
            }
        }
        return $errors;
    }

    /**
     * Creates a HookEvent, sets its parameters, and dispatches it.
     *
     * Wrapper to: @see HookDispatcher::dispatchForParameters()
     *
     * @param $hookName The hook name
     * @param $parameters The hook parameters
     */
    protected function dispatchHook($hookName, array $parameters)
    {
        $this->container->get('prestashop.hook.dispatcher')->dispatchForParameters($hookName, $parameters);
    }

    /**
     * Creates a RenderingHookEvent, sets its parameters, and dispatches it. Returns the event with the response(s).
     *
     * Wrapper to: @see HookDispatcher::renderForParameters()
     *
     * @param $hookName The hook name
     * @param $parameters The hook parameters
     * @return array The responses of hooks
     */
    protected function renderHook($hookName, array $parameters)
    {
        return $this->container->get('prestashop.hook.dispatcher')->renderForParameters($hookName, $parameters)->getContent();
    }
}
