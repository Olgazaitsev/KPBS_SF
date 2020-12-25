<?php

namespace Intervolga\Sed\ComponentTemplateLevel;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Config\Option;


/**
 * Управляет регистрацией зависимостей модулей от расширений шаблонов компонентов.
 *
 * Класс необходим для использования другими модулями шаблона проектирования "Extend Component Template".
 * Расширение шаблона компонента - это шаблон-прослойка, который запускает чужой код, а затем делегирует
 * свое выполнение оригинальному шаблону обратно. Эта прослойка создает точку расширения БУС.
 *
 * @package Intervolga\Sed\ComponentTemplateLevel
 */
class ExtensionDependency
{

    /**
     * Регистрирует зависимость модуля от расширения шаблона компонента.
     * Если расширение не создано, создает его в шаблоне сайта .default в local.
     *
     * @param string $moduleId Модуль, которому требуется шаблон-расширение.
     * @param string $componentName Полное название компонента.
     * @param string $templateName Название шаблона.
     * @param string[] $additionalPages Дополнительные страницы шаблона.
     * @throws ArgumentException
     */
    public static function register($moduleId, $componentName, $templateName, $additionalPages = array())
    {
        $dependencies = self::readDependencies();

        if (!is_array($additionalPages)) {
            $additionalPages = array();
        }

        ExtensionTemplate::sync($componentName, $templateName, $additionalPages);

        if (!is_array($dependencies[$componentName][$templateName])) {
            $dependencies[$componentName][$templateName] = array();
        }

        if (!in_array($moduleId, $dependencies[$componentName][$templateName])) {
            $dependencies[$componentName][$templateName][] = $moduleId;
        }

        self::saveDependencies($dependencies);
    }

    /**
     * Удаляет зависимость модуля от расширения шаблона компонента.
     * Если от расширения не останется зависимостей, оно будет удалено.
     *
     * @param string $moduleId Модуль, которому теперь не требуется шаблон-расширение.
     * @param string $componentName Полное название компонента.
     * @param string $templateName Название шаблона.
     */
    public static function remove($moduleId, $componentName, $templateName)
    {
        $dependencies = self::readDependencies();

        if (!is_array($dependencies[$componentName][$templateName])) {
            return;
        }

        if (in_array($moduleId, $dependencies[$componentName][$templateName])) {
            $index = array_search($moduleId, $dependencies[$componentName][$templateName]);
            unset($dependencies[$componentName][$templateName][$index]);
        }

        $isNoDependencies = false;
        if (empty($dependencies[$componentName][$templateName])) {
            unset($dependencies[$componentName][$templateName]);
            $isNoDependencies = true;

            if (empty($dependencies[$componentName])) {
                unset($dependencies[$componentName]);
            }
        }

        if ($isNoDependencies) {
            try {
                ExtensionTemplate::delete($componentName, $templateName);
            } catch (ArgumentException $e) {
                // Do nothing.
            }
        }

        self::saveDependencies($dependencies);
    }

    /**
     * Возвращает зарегистрированные зависимости модулей от шаблонов-расширений.
     * @param string $componentName Фильтр по названию компонента.
     * @param string $templateName Фильтр по названию шаблона.
     * @return array Зависимости.
     */
    public static function getDependencies($componentName = null, $templateName = null)
    {
        $dependencies = self::readDependencies();

        if (empty($componentName)) {
            return $dependencies;
        }

        if (empty($dependencies[$componentName])) {
            return array();
        }

        $filteredDependencies = array($dependencies[$componentName]);

        if (empty($templateName)) {
            return $filteredDependencies;
        }

        if (empty($filteredDependencies[$componentName][$templateName])) {
            return array($componentName => array());
        }

        $filteredDependencies[$componentName] = array($componentName => $filteredDependencies[$componentName][$templateName]);

        return $filteredDependencies;
    }

    /**
     * Считывает и возвращает сохраненные зависимости.
     * Выделено в отдельную функцию для быстрого изменения способа хранения.
     */
    private static function readDependencies()
    {
        $dependencies = include(self::getDependenciesStoragePath());

        if (empty($dependencies)) {
            $dependencies = array();
        }

        return $dependencies;
    }

    /**
     * Перезаписывает (сохраняет) зависимости.
     * Выделено в отдельную функцию для быстрого изменения способа хранения.
     */
    private static function saveDependencies($dependencies)
    {
        file_put_contents(
            self::getDependenciesStoragePath(),
            '<?php' . PHP_EOL . PHP_EOL . 'return ' . var_export($dependencies, true) . ';'
        );
    }

    private static function getDependenciesStoragePath()
    {
        $moduleDir = dirname(dirname(__DIR__));
        $storagePath = $moduleDir . '/_dependenciesStorage.php';
        return $storagePath;
    }
}
