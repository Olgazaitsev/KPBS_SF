<?php

namespace Intervolga\Sed\ComponentTemplateLevel;


use Bitrix\Main\Application;
use Bitrix\Main\Event;

class ComponentTemplate
{
    /**
     * Выполняет "расширяющий" код других модулей в момент выполнения result_modifier.php
     *
     * @param \CBitrixComponent $component
     */
    public static function extendMutator($component)
    {
        $onMutatorEvent = new Event(
            'intervolga.sed',
            'OnComponentTemplateMutator',
            array(
                'component' => clone $component,
                'componentName' => $component->getName(),
                'templateName' => $component->getTemplate()->GetName(),
                'arParams' => $component->arParams,
                'arResult' => $component->arResult,
            )
        );
        $onMutatorEvent->send();

        $results = $onMutatorEvent->getResults();

        $finalArParams = $component->arParams;
        $finalArResult = $component->arResult;
        foreach ($results as $result) {
            $resultParameters = $result->getParameters();
            $finalArParams = array_merge($finalArParams, $resultParameters['arParams']);
            $finalArResult = array_merge($finalArResult, $resultParameters['arResult']);
        }

        $component->arParams = $finalArParams;
        $component->arResult = $finalArResult;
    }

    /**
     * Выполняет "расширяющий" код других модулей в момент выполнения страницы шаблона.
     *
     * @param \CBitrixComponent $component
     */
    public static function extendTemplate($component)
    {
        $extenderTemplate = $component->getTemplate();

        $onAfterTemplateEvent = new Event(
            'intervolga.sed',
            'OnBeforeComponentTemplatePage',
            array(
                'component' => clone $component,
                'componentName' => $component->getName(),
                'templateName' => $extenderTemplate->GetName(),
                'templatePageName' => $extenderTemplate->GetPageName(),
                'arParams' => $component->arParams,
                'arResult' => $component->arResult,
            )
        );
        $onAfterTemplateEvent->send();

        self::includeActual($component);

        $onAfterTemplateEvent = new Event(
            'intervolga.sed',
            'OnAfterComponentTemplatePage',
            array(
                'component' => clone $component,
                'componentName' => $component->getName(),
                'templateName' => $extenderTemplate->GetName(),
                'templatePageName' => $extenderTemplate->GetPageName(),
                'arParams' => $component->arParams,
                'arResult' => $component->arResult,
            )
        );
        $onAfterTemplateEvent->send();
    }

    /**
     * Выполняет "расширяющий" код других модулей в момент выполнения component_epilog.php
     *
     * @param \CBitrixComponent $component
     */
    public static function extendEpilogue($component)
    {
        $onEpilogueEvent = new Event(
            'intervolga.sed',
            'OnComponentTemplateEpilogue',
            array(
                'component' => clone $component,
                'componentName' => $component->getName(),
                'templateName' => $component->getTemplate()->GetName(),
                'arParams' => $component->arParams,
                'arResult' => $component->arResult,
            )
        );
        $onEpilogueEvent->send();
    }

    /**
     * Подключает оригинальный шаблон компонента с учетом логики поиска шаблона БУС.
     *
     * @param \CBitrixComponent $component Компонент, для которого выполняется расширяющий шаблон.
     */
    public static function includeActual($component)
    {
        $docRoot = Application::getDocumentRoot();

        $extenderTemplate = $component->getTemplate();
        $templateName = $extenderTemplate->GetName();
        $templatePage = $extenderTemplate->GetPageName();

        $foundTemplates = ComponentTemplateUtil::findTemplates($component);

        // Remove extender template.
        array_shift($foundTemplates);

        if (empty($foundTemplates)) {
            throw new \RuntimeException('Original component template not found.');
        }

        $originalPagePath = null;
        $origianlTemplateInfo = null;
        foreach ($foundTemplates as $origianlCandidate) {
            $originalPageRelativePathCandidate = $origianlCandidate['path'] . '/' . $templateName . '/' . $templatePage . '.php';
            $originalPagePathCandidate = $docRoot . $originalPageRelativePathCandidate;

            if (is_file($originalPagePathCandidate)) {
                $originalPagePath = $originalPageRelativePathCandidate;
                $origianlTemplateInfo = $origianlCandidate;
                break;
            }
        }

        if (empty($originalPagePath)) {
            throw new \RuntimeException('Original component template not found.');
        }

        $origianlTemplateDir = dirname($originalPagePath);

        $originalTemplate = clone $extenderTemplate;

        $originalTemplate->__file = $originalPagePath;
        $originalTemplate->__folder = $origianlTemplateDir;

        $originalTemplate->__hasCSS = file_exists($docRoot . $origianlTemplateDir . '/style.css');
        $originalTemplate->__hasJS = file_exists($docRoot . $origianlTemplateDir . '/script.js');

        if (isset($origianlTemplateInfo['site_template'])) {
            $originalTemplate->__siteTemplate = $origianlTemplateInfo['site_template'];
        }

        $originalTemplate->__templateInTheme = $origianlTemplateInfo['in_theme'] === true;

        $originalTemplate->IncludeTemplate($component->arResult);
    }
}