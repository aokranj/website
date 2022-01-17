<?php
/*
 * ConfigMaps for WordPress WP-CLI - Configuration management for your wp_options table
 *
 * Copyright (C) 2022 Bostjan Skufca Jese
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, see <https://www.gnu.org/licenses/gpl-2.0.html>.
 */

namespace WP\CLI\ConfigMaps;

use WP_CLI;

if (!defined('WP_CLI')) {
    throw new Exception("Cannot run outside WP-CLI context");
}

class ConfigMapService
{
    const CONFIG_MAP_SUPPORTED_VERSION_MIN = 1;
    const CONFIG_MAP_SUPPORTED_VERSION_MAX = 1;

    public static $configMapIndex = [];

    /**
     * Configure which config maps to use
     */
    public static function setCustomMaps ($configMaps)
    {
        foreach ($configMaps as $mapId => $mapFile) {
            self::$configMapIndex[$mapId] = [
                'file' => $mapFile,
            ];
        }
    }

    public static function getMapCount ()
    {
        return count(self::$configMapIndex);
    }

    public static function doesMapIdExist ($mapId)
    {
        return isset(self::$configMapIndex[$mapId]);
    }

    public static function getMapFile ($mapId)
    {
        return self::$configMapIndex[$mapId]['file'];
    }

    public static function getPrintableFilePath ($filePath)
    {
        return preg_replace("#^" . ABSPATH . "#", '[ABSPATH]/', $filePath);
    }

    public static function getAllMapsMetadata ()
    {
        return self::$configMapIndex;
    }

    public static function getMapsMetadata ()
    {
        return self::$configMapIndex;
    }

    public static function getMaps ()
    {
        $configMaps = [];
        foreach (self::$configMapIndex as $mapId => $mapMetadata) {
            // When dumping, unknown keys should only land in the first defined config map, not in all of them
            if ($mapId == array_key_first(self::$configMapIndex)) {
                $undefKeyActionDump = "add";
            } else {
                $undefKeyActionDump = "ignore";
            }
            $configMaps[$mapId] = self::getMap($mapId, $undefKeyActionDump);
        }
        return $configMaps;
    }

    /**
     * Read the map from the file as specified in the config map index
     */
    public static function getMap ($mapId, $undefKeyActionDump)
    {
        $mapFile = self::getMapFile($mapId);
        if (!is_file($mapFile) || !is_readable($mapFile)) {
            throw new Exception("Config map file '$mapFile' for config map ID '$mapId' does not exist or it is not readable");
        }
        $configMapContainer = require $mapFile;
        if ($configMapContainer === 1) {
            throw new Exception("Reading the file for config map ID '$mapId' did not yield any content. Perhaps the config map is missing the `return` statement?");
        }

        if (!isset($configMapContainer['metadata']['version'])) {
            throw new Exception("Config map '$mapId' is missing version information (array['metadata']['version'] field)");
        }

        if (
            ($configMapContainer['metadata']['version'] < self::CONFIG_MAP_SUPPORTED_VERSION_MIN)
            ||
            ($configMapContainer['metadata']['version'] > self::CONFIG_MAP_SUPPORTED_VERSION_MAX)
        ) {
            throw new Exception(
                "Config map version not supported. " .
                "Min=" . self::CONFIG_MAP_SUPPORTED_VERSION_MIN . ", " .
                "max=" . self::CONFIG_MAP_SUPPORTED_VERSION_MAX . ", " .
                "yours($mapId)=" . $configMapContainer['metadata']['version'] . "."
            );
        }

        if (!isset($configMapContainer['data'])) {
            throw new Exception("Container for config map '$mapId' is missing data");
        }

        if (!is_array($configMapContainer['data'])) {
            throw new Exception("Container for config map '$mapId' has an invalid data type (array['data'] is not an array)");
        }

        $minimizedConfigMap = $configMapContainer['data'];
        $configMap = self::inflateMap($minimizedConfigMap, $undefKeyActionDump);
        return $configMap;
    }

    /**
     * Convert a config map to a corresponding (minimized) PHP code
     *
     * Takes a config map (or an ID of a config map and fetches a corresponding
     * config map) and converts it to its minimal representation in a form of a
     * PHP code. The returned PHP content includes the starting PHP tag,
     * optionally a header and a `return [...];` statement.
     *
     * @param   mixed   $mapOrId        A config map or an ID of a config map
     * @param   string  $header         A header to include at the top of the returned PHP content
     * @return  string                  A PHP file content that, when include()/require()-d, it recreates a config map
     */
    public static function getMapAsPhp ($mapOrId, $header="")
    {
        if (is_array($mapOrId)) {
            $configMap = $mapOrId;
        } else {
            $mapId = $mapOrId;
            $configMap = self::getMap($mapId);
        }
        $configMapMinimized = self::minimizeMap($configMap);

        $configMapContainer = [
            'metadata' => [
                'version' => 1,
            ],
            'data' => $configMapMinimized,
        ];

        $configMapFileContent = "<?php\n\n";
        if ($header != "") {
            $configMapFileContent .= $header;
            if (substr($header, -1) == "\n") {
                $configMapFileContent .= "\n";
            } else {
                $configMapFileContent .= "\n\n";
            }
        }

        // Generate the actual config map PHP code (+ condense it a bit)
        $configMapPhp = var_export($configMapContainer, true);
        $configMapPhp = preg_replace('#^array \(#', '[', $configMapPhp); // Starting 'array ('
        $configMapPhp = preg_replace('# => \n\s+array \(#', ' => [', $configMapPhp); // Every subsequent " => \n   array ("
        $configMapPhp = preg_replace('#(\s+)\),$#m', "$1],", $configMapPhp); // Every end of array
        $configMapPhp = preg_replace('#\)$#', "]", $configMapPhp); // The end of the very first array, in the last line

        $configMapFileContent .= "return " . $configMapPhp;
        $configMapFileContent .= ";\n";

        return $configMapFileContent;
    }

    /**
     * Create a config map based on the current wp_options content
     *
     * This is a "I'll do my best to create a config map for you" process. It
     * simply uses the default action values for a given option type + does a
     * few additional tweaks for some well known options (i.e. `recently_activated`
     * option that simply tracks recently activated plugins).
     *
     * @param   bool    $manualFixups   Apply manual fixups to make the map more relevant (default=true)
     * @return  array                   Config map
     */
    public static function generateMapFromWpOptions ($manualFixups=true)
    {
        $rawOptions = Db::getAllOptions();

        $configMap = [];
        foreach ($rawOptions as $optionName => $rawOptionValue) {

            WP_CLI::debug(__CLASS__.'::'.__FUNCTION__ . ": Processing wp_options entry: ". $optionName);

            if (preg_match('/^a:[0-9]+:{/', $rawOptionValue)) {

                // Serialized array
                $decodedOptionValue = unserialize($rawOptionValue);
                $optionSpec = self::generateDefaultOptionSpecFromValue($decodedOptionValue);
                $optionSpec['encoding'] = 'serialize';

            } elseif (preg_match('/^{/', $rawOptionValue)) {

                // JSON
                $decodedOptionValue = json_decode($rawOptionValue, true);
                $optionSpec = self::generateDefaultOptionSpecFromValue($decodedOptionValue);
                $optionSpec['encoding'] = 'json';

            } elseif (is_string($rawOptionValue)) {

                // String
                $optionSpec = self::generateDefaultOptionSpecFromValue($rawOptionValue);
                $decodedOptionValue = $rawOptionValue;

            } else {
                throw new Exception("Unsupported data type (1): ". gettype($optionValue) .", value=". $optionValue);
            }

            //ksort($optionSpec);
            $configMap[$optionName] = $optionSpec;
        }

        if ($manualFixups) {
            $configMap['can_compress_scripts']['action-apply']           = 'ignore';
            $configMap['can_compress_scripts']['action-dump']            = 'ignore';
            $configMap['can_compress_scripts']['value']                  = NULL;
            $configMap['finished_updating_comment_type']['action-apply'] = 'ignore';
            $configMap['finished_updating_comment_type']['action-dump']  = 'ignore';
            $configMap['finished_updating_comment_type']['value']        = NULL;
            $configMap['recently_activated']['action-apply']             = 'ignore';
            $configMap['recently_activated']['action-dump']              = 'ignore';
            $configMap['recently_activated']['value']                    = NULL;
            $configMap['recently_edited']['action-apply']                = 'ignore';
            $configMap['recently_edited']['action-dump']                 = 'ignore';
            $configMap['recently_edited']['value']                       = NULL;
            $configMap['uninstall_plugins']['action-apply']              = 'ignore';
            $configMap['uninstall_plugins']['action-dump']               = 'ignore';
            $configMap['uninstall_plugins']['value']                     = NULL;
        }

        return $configMap;
    }

    /**
     * Generate a default option spec from an option value
     */
    protected static function generateDefaultOptionSpecFromValue ($optionValue)
    {
        if (is_array($optionValue)) {
            $optionSpec = [
                'type'                   => 'array',
                'action-apply'           => 'walk',
                'action-dump'            => 'walk',
                'undef-key-action-apply' => 'ignore',
                'undef-key-action-dump'  => 'add',
                'value'                  => [],
            ];
            foreach ($optionValue as $key => $val) {
                $optionSpec['value'][$key] = self::generateDefaultOptionSpecFromValue($val);
            }
            ksort($optionSpec['value']);

        } elseif (is_string($optionValue)) {
            $optionSpec = [
                'type'         => 'string',
                'action-apply' => 'copy-as-is',
                'action-dump'  => 'copy-as-is',
                'value'        => $optionValue,
            ];

        } elseif (is_int($optionValue)) {
            $optionSpec = [
                'type'         => 'int',
                'action-apply' => 'copy-as-is',
                'action-dump'  => 'copy-as-is',
                'value'        => $optionValue,
            ];

        } elseif (is_bool($optionValue)) {
            $optionSpec = [
                'type'         => 'bool',
                'action-apply' => 'copy-as-is',
                'action-dump'  => 'copy-as-is',
                'value'        => $optionValue,
            ];

        } elseif (is_null($optionValue)) {
            $optionSpec = [
                'type'         => 'null',
                'action-apply' => 'copy-as-is',
                'action-dump'  => 'copy-as-is',
                'value'        => NULL,
            ];

        } else {
            throw new Exception("Unsupported data type (2): ". gettype($optionValue) .", value=". $optionValue);
        }

        return $optionSpec;
    }

    /**
     * Minimize a full config map
     */
    protected static function minimizeMap ($fullConfigMap)
    {
        $minimizedConfigMap = [];

        foreach ($fullConfigMap as $optionName => $optionSpec) {
            switch ($optionSpec['type']) {
                case 'null':
                case 'string':
                case 'int':
                case 'bool':
                    if ($optionSpec['action-apply'] == $optionSpec['action-dump']) {
                        $optionSpec['action'] = $optionSpec['action-apply'];
                        unset($optionSpec['action-apply']);
                        unset($optionSpec['action-dump']);

                        if ($optionSpec['action'] == 'copy-as-is') {
                            $optionSpec = $optionSpec['value'];
                        }
                    }

                    break;

                case 'array':
                    if ($optionSpec['action-apply'] == $optionSpec['action-dump']) {
                        $optionSpec['action'] = $optionSpec['action-apply'];
                        unset($optionSpec['action-apply']);
                        unset($optionSpec['action-dump']);

                        // For copy-as-is do nothing

                        if ($optionSpec['action'] == 'walk') {
                            $optionSpec['value'] = self::minimizeMap($optionSpec['value']);
                            unset($optionSpec['action']);
                        }

                    } elseif ($optionSpec['action-apply'] == 'walk') {
                        $optionSpec['value'] = self::minimizeMap($optionSpec['value']);

                    } elseif ($optionSpec['action-apply'] == 'ignore') {
                        unset($optionSpec['value']);
                    }

                    if ($optionSpec['undef-key-action-apply'] == 'ignore') {
                        unset($optionSpec['undef-key-action-apply']);
                    }
                    if ($optionSpec['undef-key-action-dump'] == 'add') {
                        unset($optionSpec['undef-key-action-dump']);
                    }

                    break;
            }

            if (is_array($optionSpec)) {
                ksort($optionSpec);
            }
            $minimizedConfigMap[$optionName] = $optionSpec;
        }

        ksort($minimizedConfigMap);
        return $minimizedConfigMap;
    }

    /**
     * Inflate a minimized config map into full config map
     */
    protected static function inflateMap ($minimizedConfigMap, $undefKeyActionDump="add")
    {
        $fullConfigMap = [];

        foreach ($minimizedConfigMap as $optionName => $minimumOptionSpec) {

            if (is_array($minimumOptionSpec)) {
                $optionSpec = $minimumOptionSpec;
                switch ($optionSpec['type']) {
                    case 'null':
                    case 'string':
                    case 'int':
                    case 'bool':
                        if (!isset($optionSpec['action-apply'])) {
                            $optionSpec['action-apply'] = (isset($optionSpec['action']) ? $optionSpec['action'] : 'copy-as-is');
                        }
                        if (!isset($optionSpec['action-dump'])) {
                            $optionSpec['action-dump'] = (isset($optionSpec['action'])  ? $optionSpec['action'] : 'copy-as-is');
                        }
                        unset($optionSpec['action']);
                        break;

                    case 'array':
                        if (!isset($optionSpec['action-apply'])) {
                            $optionSpec['action-apply'] = (isset($optionSpec['action']) ? $optionSpec['action'] : 'walk');
                        }
                        if (!isset($optionSpec['action-dump'])) {
                            $optionSpec['action-dump'] = (isset($optionSpec['action'])  ? $optionSpec['action'] : 'walk');
                        }
                        unset($optionSpec['action']);

                        if (!isset($optionSpec['undef-key-action-apply'])) {
                            $optionSpec['undef-key-action-apply'] = 'ignore';
                        }
                        if (!isset($optionSpec['undef-key-action-dump'])) {
                            $optionSpec['undef-key-action-dump'] = $undefKeyActionDump;
                        }

                        if ($optionSpec['value'] != NULL) {
                            $optionSpec['value'] = self::inflateMap($optionSpec['value'], $undefKeyActionDump);
                        }
                        break;

                    default:
                        throw new Exception("Unsupported data type specification encountered: ". $optionSpec['type'] ." (found at option '". $optionName ."')");
                }

            } elseif (is_string($minimumOptionSpec)) {
                $optionSpec = [
                    'type'         => 'string',
                    'action-apply' => 'copy-as-is',
                    'action-dump'  => 'copy-as-is',
                    'value'        => $minimumOptionSpec,
                ];

            } elseif (is_int($minimumOptionSpec)) {
                $optionSpec = [
                    'type'         => 'int',
                    'action-apply' => 'copy-as-is',
                    'action-dump'  => 'copy-as-is',
                    'value'        => $minimumOptionSpec,
                ];

            } elseif (is_bool($minimumOptionSpec)) {
                $optionSpec = [
                    'type'         => 'bool',
                    'action-apply' => 'copy-as-is',
                    'action-dump'  => 'copy-as-is',
                    'value'        => $minimumOptionSpec,
                ];

            } elseif (is_null($minimumOptionSpec)) {
                $optionSpec = [
                    'type'         => 'null',
                    'action-apply' => 'copy-as-is',
                    'action-dump'  => 'copy-as-is',
                    'value'        => NULL,
                ];

            } else {
                throw new Exception("Unsupported data type (3) for '$optionName': ". gettype($minimumOptionSpec) .", value/minOptionSpec=". $minimumOptionSpec);
            }

            $fullConfigMap[$optionName] = $optionSpec;
        }

        return $fullConfigMap;
    }

    /**
     * Merge all defined config maps into a final single map that can then be applied to the database
     */
    public static function mergeDefinedMapSet ()
    {
        $mergedConfigMap = [];

        foreach (self::$configMapIndex as $mapId => $mapMetadata) {
            // When dumping, unknown keys should only land in the first defined config map, not in all of them
            if ($mapId == array_key_first(self::$configMapIndex)) {
                $undefKeyActionDump = "add";
            } else {
                $undefKeyActionDump = "ignore";
            }
            $configMap = self::getMap($mapId, $undefKeyActionDump);
            $mergedConfigMap = self::mergeTwoMaps($mergedConfigMap, $configMap, $mapId);
        }
        ksort($mergedConfigMap);

        return $mergedConfigMap;
    }

    /**
     * Merge two maps into one (options from the second map override options in the first map)
     */
    protected static function mergeTwoMaps ($firstMap, $secondMap, $secondMapId)
    {
        $mergedMap = $firstMap;

        // Handle null values correctly
        if (!isset($secondMap)) {
            return $mergedMap;
        }

        foreach ($secondMap as $optionName => $optionSpec) {
            switch ($optionSpec['type']) {
                case 'null':
                case 'string':
                case 'int':
                case 'bool':
                    $mergedMap[$optionName] = $optionSpec;
                    $mergedMap[$optionName]['source-map-id'] = $secondMapId;
                    break;

                case 'array':
                    if (!isset($mergedMap[$optionName])) {
                        $mergedMap[$optionName] = [
                            'type'  => 'array',
                            'value' => [],
                        ];
                    }

                    // This is only useful at the very top-level
                    if (isset($optionSpec['encoding'])) {
                        $mergedMap[$optionName]['encoding'] = $optionSpec['encoding'];
                    }

                    $mergedMap[$optionName]['action-apply']           = $optionSpec['action-apply'];
                    $mergedMap[$optionName]['action-dump']            = $optionSpec['action-dump'];
                    $mergedMap[$optionName]['undef-key-action-apply'] = $optionSpec['undef-key-action-apply'];
                    $mergedMap[$optionName]['undef-key-action-dump']  = $optionSpec['undef-key-action-dump'];
                    $mergedMap[$optionName]['value'] = self::mergeTwoMaps($mergedMap[$optionName]['value'], $optionSpec['value'], $secondMapId);
                    $mergedMap[$optionName]['source-map-id'] = $secondMapId;
                    break;

                default:
                    throw new Exception("Unsupported data type specification encountered: ". $optionSpec['type'] ." (found at option '". $optionName ."')");
            }
        }
        ksort($mergedMap);

        return $mergedMap;
    }

    /**
     * Apply the given config map to the database
     *
     * It takes the config map and updates the `wp_options` table according to
     * the map's specifications.
     *
     * @param   array   $configMap      Map to apply
     * @param   bool    $dryRun         If true, do not perform the database updates (default: false)
     * @return  array                   Array of changes that were performed
     */
    public static function applyMap ($configMap, $dryRun=false)
    {
        // Let's call these arrays "value maps", since we'll only be consulting
        // their values to determine what needs to be updated in the database
        $currentValueMap = self::generateMapFromWpOptions(false);
        $newValueMap = $currentValueMap;

        // Apply the map + generate change descriptions
        $changes = [];
        foreach ($configMap as $optionName => $optionSpec) {
            $newChanges = self::applyOptionSpec($newValueMap, $optionName, $optionSpec);
            $changes = array_merge($changes, $newChanges);
        }

        // Update database where applicable
        if ($dryRun == false) {
            $currentWpOptionsArray = self::generateRawWpOptionsFromMap($currentValueMap);
            $newWpOptionsArray = self::generateRawWpOptionsFromMap($newValueMap);
            foreach ($currentWpOptionsArray as $optionName => $optionValue) {
                if (!isset($newWpOptionsArray[$optionName])) {
                    Db::deleteOption($optionName);
                }
            }
            foreach ($newWpOptionsArray as $optionName => $optionValue) {
                if (!isset($currentWpOptionsArray[$optionName])) {
                    Db::insertOption($optionName, $optionValue);
                } else {
                    if ($currentWpOptionsArray[$optionName] != $newWpOptionsArray[$optionName]) {
                        Db::updateOption($optionName, $optionValue);
                    }
                }
            }
        }

        // Return an array of changes to be displayed in a console
        return $changes;
    }

    /**
     * Apply the given optionSpec to a given config/value map (in-place)
     *
     * It takes the option spec from a config map and applies it (in-place!) to
     * a config/value map item.
     *
     * @param   array   &$targetValueMap A config/value map to work on, passed by reference
     * @param   string  $optionName      Option to work on in the $targetValueMap
     * @param   array   $optionSpec      Option spec to apply
     * @return  array                    An array of change descriptions that were performed
     */
    public static function applyOptionSpec (&$targetValueMap, $optionName, $optionSpec, $parentNames="")
    {
        $changes = [];
        switch ($optionSpec['action-apply']) {

            case 'ignore':
                break;


            case 'delete':
                if (isset($targetValueMap[$optionName])) {
                    $oldValue = ($optionSpec['type'] == 'array' ? '(array)' : $targetValueMap[$optionName]['value']);
                    unset($targetValueMap[$optionName]);

                    $changes[] = [
                        'Option name'   => $parentNames . $optionName,
                        'Action'        => 'Delete',
                        'Before'        => $oldValue,
                        'After'         => '',
                        'Config map ID' => $optionSpec['source-map-id'],
                    ];
                }
                break;


            case 'copy-as-is':
                if ($optionSpec['type'] == 'array') {
                    throw new Exception("The 'action-apply' value 'copy-as-is' is not yet supported for data type 'array' (encoutered at '$optionName')");
                }

                if (!isset($targetValueMap[$optionName])) {
                    $targetValueMap[$optionName] = $optionSpec;

                    $changes[] = [
                        'Option name'   => $parentNames . $optionName,
                        'Action'        => 'Create',
                        'Before'        => '',
                        'After'         => $optionSpec['value'],
                        'Config map ID' => $optionSpec['source-map-id'],
                    ];
                } else {
                    if ($targetValueMap[$optionName]['value'] != $optionSpec['value']) {
                        $oldValue = $targetValueMap[$optionName]['value'];
                        $targetValueMap[$optionName]['value'] = $optionSpec['value'];

                        $changes[] = [
                            'Option name'   => $parentNames . $optionName,
                            'Action'        => 'Update',
                            'Before'        => $oldValue,
                            'After'         => $optionSpec['value'],
                            'Config map ID' => $optionSpec['source-map-id'],
                        ];
                    }
                }

                break;


            case 'walk':
                if ($optionSpec['type'] != 'array') {
                    throw new Exception("The 'action-apply' value 'walk' is only supported for data type 'array' (encoutered at '$optionName')");
                }

                if (!isset($targetValueMap[$optionName])) {
                    $targetValueMap[$optionName] = [
                        'type'                   => $optionSpec['type'],
                        'action-apply'           => $optionSpec['action-apply'],
                        'action-dump'            => $optionSpec['action-dump'],
                        'undef-key-action-apply' => $optionSpec['undef-key-action-apply'],
                        'undef-key-action-dump'  => $optionSpec['undef-key-action-dump'],
                        'value'                  => [],
                        'source-map-id'          => $optionSpec['source-map-id'],
                    ];

                    $changes[] = [
                        'Option name'   => $parentNames . $optionName,
                        'Action'        => 'Create',
                        'Before'        => '',
                        'After'         => '(array)',
                        'Config map ID' => $optionSpec['source-map-id'],
                    ];
                }
                foreach ($optionSpec['value'] as $arrayOptionName => $arrayOptionSpec) {
                    $newChanges = self::applyOptionSpec($targetValueMap[$optionName]['value'], $arrayOptionName, $arrayOptionSpec, $parentNames.$optionName." => ");
                    $changes = array_merge($changes, $newChanges);
                }

                if ($optionSpec['undef-key-action-apply'] == 'delete') {
                    foreach ($targetValueMap[$optionName]['value'] as $arrayOptionName => $arrayOptionSpec) {
                        if (!isset($optionSpec['value'][$arrayOptionName])) {
                            $oldValue = $arrayOptionSpec['value'];
                            unset($targetValueMap[$optionName]['value'][$arrayOptionName]);

                            $changes[] = [
                                'Option name'   => $parentNames . $optionName ." => " . $arrayOptionName,
                                'Action'        => 'Delete',
                                'Before'        => $oldValue,
                                'After'         => '',
                                'Config map ID' => $optionSpec['source-map-id'],
                            ];
                        }
                    }
                }

                break;


            default:
                throw new Exception("Unsupported action-apply value '". $optionSpec['action-apply'] ."' found (encountered at '$optionName')");
        }

        // $targetValueMap was passed by reference and worked on in-place, we're only returning a set of changes that were performed
        return $changes;
    }

    /**
     * Generate an array of wp_options from a config map
     *
     * Generated array is suitable for database inserts/updates.
     *
     * @param   array   configMap       A config/value map to work on, passed by reference
     * @return  array                   An array of wp_options, as if it came directly from the database (suitable for DB inserts/updates)
     */
    protected static function generateRawWpOptionsFromMap ($configMap)
    {
        $wpOptions = [];

        // Convert back to usual types
        foreach ($configMap as $optionName => $optionSpec) {
            switch ($optionSpec['type']) {
                case 'null':
                case 'string':
                case 'int':
                case 'bool':
                    $wpOptions[$optionName] = $optionSpec['value'];
                    break;

                case 'array':
                    $value = self::generateRawWpOptionsFromMap($optionSpec['value']);
                    if (!isset($optionSpec['encoding'])) {
                        $wpOptions[$optionName] = $value;
                    } else {
                        switch ($optionSpec['encoding']) {
                            case 'serialize':
                                $wpOptions[$optionName] = serialize($value);
                                break;
                            case 'json':
                                $wpOptions[$optionName] = json_encode($value);
                                break;
                            default:
                                throw new Exception("Unsupported encoding '". $optionSpec['encoding'] ."' requested (encountered at '$optionName')");
                        }
                    }
                    break;

                default:
                    throw new Exception("Unsupported value type '". $optionSpec['type'] ."' found (encountered at '$optionName')");
            }
        }

        return $wpOptions;
    }

    /**
     * Update values of a config map by pulling them from another config map (called "value map" in this case)
     *
     * Generated array is suitable for database inserts/updates.
     *
     * @param   array   $configMap      A config map to work on
     * @param   array   $newValueMap    A value map to use (it's essentially a config map, but all non-value metadata is ignored)
     * @param   array   $undefKeyAction Action to take with option keys that only appear in $newValueMap ('add' or 'ignore')
     * @return  array                   An updated config map
     */
    public static function updateMapValues ($configMap, $newValueMap, $undefKeyAction)
    {
        $updatedConfigMap = [];

        foreach ($configMap as $optionName => $optionSpec) {
            if ($optionSpec['action-dump'] == 'ignore') {
                $updatedConfigMap[$optionName] = $optionSpec;
                continue;
            }
            if (!isset($newValueMap[$optionName])) {
                $updatedConfigMap[$optionName] = $optionSpec;
                continue;
            }
            switch ($optionSpec['type']) {
                case 'null':
                case 'string':
                case 'int':
                case 'bool':
                    $updatedConfigMap[$optionName] = $optionSpec;
                    $updatedConfigMap[$optionName]['value'] = $newValueMap[$optionName]['value'];
                    break;

                case 'array':
                    $updatedConfigMap[$optionName] = $optionSpec;
                    $updatedConfigMap[$optionName]['value'] = self::updateMapValues($optionSpec['value'], $newValueMap[$optionName]['value'], $optionSpec['undef-key-action-dump']);
                    break;

                default:
                    throw new Exception("Unsupported value type '". $optionSpec['type'] ."' found (encountered at '$optionName')");
            }
        }

        if ($undefKeyAction == 'add' && !is_null($newValueMap)) {
            foreach ($newValueMap as $optionName => $optionSpec) {
                if (!isset($configMap[$optionName])) {
                    $updatedConfigMap[$optionName] = $optionSpec;
                }
            }
        }

        return $updatedConfigMap;
    }

    /**
     * Update a config map file with the new content
     */
    public static function updateMapFile ($mapId, $newConfigMap, $header="")
    {
        file_put_contents(
            self::getMapFile($mapId),
            self::getMapAsPhp($newConfigMap, $header)
        );
    }
}
