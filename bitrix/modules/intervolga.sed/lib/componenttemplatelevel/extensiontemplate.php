<?php

namespace Intervolga\Sed\ComponentTemplateLevel;


use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;

class ExtensionTemplate
{
    /**
     * Убеждается, что шаблон-расширение существует и содержит все страницы. При необходимости их создает.
     *
     * Шаблон-расширение создается в /local/templates/.default/components/$componentNamespace/$componentShortName/<$templateName>.
     *
     * @param string $componentName Полное имя компонента.
     * @param string $templateName Название шаблона.
     * @param string[] $additionalPages Дополнительные страницы шаблона.
     * @throws ArgumentException Если название компонента некорректно.
     */
    public static function sync($componentName, $templateName, $additionalPages = array())
    {
        if (!\CComponentEngine::checkComponentName($componentName)) {
            throw new ArgumentException('Invalid component name.', 'componentName');
        }

        $docRoot = Application::getDocumentRoot();
        $componentRelativePath = \CComponentEngine::makeComponentPath($componentName);
        $templatePath = '/local/templates/.default/components' . $componentRelativePath . '/' . $templateName;
        $extensionMarkerPath = $templatePath . '/extension';

        // Проверить существование шаблона.
        $extensionExists = false;
        if (is_dir($docRoot . $templatePath)) {
            if (!is_file($docRoot . $extensionMarkerPath)) {
                throw new \RuntimeException(
                    'Another template ' . $templateName . ' exists for ' . $componentName .
                    ' in /local/templates/.default/... and it\'s not of type extension.'
                );
            }
            $extensionExists = true;
        }

        // Если не существует - создать.
        $moduleDir = dirname(dirname(__DIR__));
        $templateBaseDir = $moduleDir . '/files/extensiontemplatebase';
        $templateBasePage = $moduleDir . '/files/extensiontemplate_page.php';

        if (!$extensionExists) {
            CopyDirFiles($templateBaseDir, $docRoot . $templatePath);
        }

        // Проверить существование страниц и создать недостающие.
        $componentPages = ComponentTemplateUtil::getComponentPages($componentName);
        $componentPages = array_merge($componentPages, $additionalPages);
        foreach ($componentPages as $page) {
            if (!is_scalar($page)) {
                continue;
            }

            $pagePath = $docRoot . $templatePath . '/' . $page . '.php';
            if (!is_file($pagePath)) {
                copy($templateBasePage, $pagePath);
            }
        }
    }

    /**
     * Удаляет шаблон-расширение.
     *
     * @param string $componentName Полное имя компонента.
     * @param string $templateName Название шаблона.
     *
     * @throws ArgumentException Если название компонента некорректно.
     */
    public static function delete($componentName, $templateName)
    {
        if (!\CComponentEngine::checkComponentName($componentName)) {
            throw new ArgumentException('Invalid component name.', 'componentName');
        }

        $docRoot = Application::getDocumentRoot();
        $componentRelativePath = \CComponentEngine::makeComponentPath($componentName);
        $templatePath = '/local/templates/.default/components' . $componentRelativePath . '/' . $templateName;
        $extensionMarkerPath = $templatePath . '/extension';

        // Проверить существование шаблона.
        $extensionExists = false;
        if (is_dir($docRoot . $templatePath)) {
            if (!is_file($docRoot . $extensionMarkerPath)) {
                throw new \RuntimeException(
                    'Another template ' . $templateName . ' exists for ' . $componentName .
                    ' in /local/templates/.default/... and it\'s not of type extension.'
                );
            }
            $extensionExists = true;
        }

        if ($extensionExists) {
            DeleteDirFilesEx($templatePath);
        }
    }
}