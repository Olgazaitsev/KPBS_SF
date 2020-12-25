<?php

namespace Intervolga\Sed\ComponentTemplateLevel;


use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;

class ComponentTemplateUtil
{
    /**
     * Ищет все шаблоны с указанным именем для компонента.
     *
     * @param \CBitrixComponent $component Компонент.
     *
     * @return array Местоположения найденных шаблонов, отсортированные по приоритету
     *     (логика БУС - подключение первого шаблона из этого списка).
     */
    public static function findTemplates($component)
    {
        $docRoot = Application::getDocumentRoot();

        $candidates = self::buildCandidateList($component);

        $foundTemplates = array();
        foreach ($candidates as $candidate) {
            if (is_dir($docRoot . $candidate['path'])) {
                $foundTemplates[] = $candidate;
            }
        }

        return $foundTemplates;
    }

    /**
     * Возвращает список страниц компонента.
     *
     * Метод просматривает настройки ЧПУ в файле параметров компонента.
     * Если настроек нет - возвращает только 'template' (что в редких случаях может быть некорректно).
     *
     * @param string $componentName Полное имя компонента.
     *
     * @return string[] Названия страниц.
     * @throws ArgumentException Если имя компонента некорректно.
     */
    public static function getComponentPages($componentName)
    {
        if (!\CComponentEngine::checkComponentName($componentName)) {
            throw new ArgumentException('Invalid component name.', 'componentName');
        }

        $componentRelativePath = \CComponentEngine::makeComponentPath($componentName);
        $componentPath = getLocalPath('components' . $componentRelativePath);

        $docRoot = Application::getDocumentRoot();

        $componentParametersPath = $docRoot . $componentPath . '/.parameters.php';

        if (!is_file($componentParametersPath)) {
            return array('template');
        }

        include($componentParametersPath);

        if (!isset($arComponentParameters) || !is_array($arComponentParameters['PARAMETERS']['SEF_MODE'])) {
            return array('template');
        }

        return array_keys($arComponentParameters['PARAMETERS']['SEF_MODE']);
    }

    /**
     * Строит список метоположений шаблонов-кандидатов на подключение без проверки их существования (12 мест).
     *
     * @param \CBitrixComponent $component
     * 
     * @return array
     */
    private static function buildCandidateList($component)
    {
        $parentComponent = $component->getParent();
        $siteTemplate = $component->getSiteTemplateId();
        $relativePath = $component->getRelativePath();
        
        $isDefaultSiteTemplate = $siteTemplate == '.default';
        
        $candidates = array();
        
        if (!empty($parentComponent)) {
            $parentRelativePath = $parentComponent->getRelativePath();
            $parentTemplateName = $parentComponent->getTemplate()->GetName();

            if (!$isDefaultSiteTemplate) {
                $candidates[] = array(
                    'path' => '/local/templates/' . $siteTemplate . '/components' . $parentRelativePath . '/' . $parentTemplateName . $relativePath,
                    'in_theme' => true,
                );
            }

            $candidates[] = array(
                'path' => '/local/templates/.default/components' . $parentRelativePath . '/' . $parentTemplateName . $relativePath,
                'in_theme' => true,
                'site_template' => '.default',
            );

            $candidates[] = array(
                'path' => '/local/components' . $parentRelativePath . '/templates/' . $parentTemplateName . $relativePath,
                'in_theme' => true,
                'site_template' => '',
            );
        }

        if (!$isDefaultSiteTemplate) {
            $candidates[] = array(
                'path' => '/local/templates/' . $siteTemplate . '/components' . $relativePath,
            );
        }

        $candidates[] = array(
            'path' => '/local/templates/.default/components' . $relativePath,
            'site_template' => '.default',
        );
        $candidates[] = array(
            'path' => '/local/components' . $relativePath . '/templates',
            'site_template' => '',
        );

        if (!empty($parentComponent)) {
            $parentRelativePath = $parentComponent->getRelativePath();
            $parentTemplateName = $parentComponent->getTemplate()->GetName();

            if (!$isDefaultSiteTemplate) {
                $candidates[] = array(
                    'path' => BX_PERSONAL_ROOT . '/templates/' . $siteTemplate . '/components' . $parentRelativePath . '/' . $parentTemplateName . $relativePath,
                    'in_theme' => true,
                );
            }
            $candidates[] = array(
                'path' => BX_PERSONAL_ROOT . '/templates/.default/components' . $parentRelativePath . '/' . $parentTemplateName . $relativePath,
                'in_theme' => true,
                'site_template' => '.default',
            );
            $candidates[] = array(
                'path' => '/bitrix/components' . $parentRelativePath . '/templates/' . $parentTemplateName . $relativePath,
                'in_theme' => true,
                'site_template' => '',
            );
        }

        if (!$isDefaultSiteTemplate) {
            $candidates[] = array(
                'path' => BX_PERSONAL_ROOT . '/templates/' . $siteTemplate . '/components' . $relativePath,
            );
        }

        $candidates[] = array(
            'path' => BX_PERSONAL_ROOT . '/templates/.default/components' . $relativePath,
            'site_template' => '.default',
        );
        $candidates[] = array(
            'path' => '/bitrix/components' . $relativePath . '/templates',
            'site_template' => '',
        );
        
        return $candidates;
    }
}