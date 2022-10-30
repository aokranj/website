<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use WP\CLI\ConfMaps\ConfMapService;

final class ConfMapServiceTest extends TestCase
{

    public function testOverride (): void
    {
        $confMap1 = [
            'metadata' => [
                'version' => 1,
            ],
            'data' => [
                'var1' => 'val1',
            ],
        ];
        $confMap2 = [
            'metadata' => [
                'version' => 1,
            ],
            'data' => [
                'var1' => 'val2',
            ],
        ];
        $confMaps = [
            'cm1' => $confMap1,
            'cm2' => $confMap2,
        ];
        ConfMapService::setCustomMaps($confMaps);

        $confMapFinal = ConfMapService::mergeDefinedMapSet();

        $this->assertEquals($confMapFinal['var1']['value'], "val2");
    }



    public function testOverrideNested (): void
    {
        $confMap1 = [
            'metadata' => [
                'version' => 1,
            ],
            'data' => [
                'l1' => [
                    'type' => 'array',
                    'value' => [
                        'var1' => 'val1',
                    ],
                ],
            ],
        ];
        $confMap2 = [
            'metadata' => [
                'version' => 1,
            ],
            'data' => [
                'l1' => [
                    'type' => 'array',
                    'value' => [
                        'var1' => 'val2',
                    ],
                ],
            ],
        ];
        $confMaps = [
            'cm1' => $confMap1,
            'cm2' => $confMap2,
        ];
        ConfMapService::setCustomMaps($confMaps);

        $confMapFinal = ConfMapService::mergeDefinedMapSet();

        $this->assertEquals($confMapFinal['l1']['value']['var1']['value'], "val2");
    }



    public function testArrayMerge (): void
    {
        $confMap1 = [
            'metadata' => [
                'version' => 1,
            ],
            'data' => [
                'l1' => [
                    'type' => 'array',
                    'value' => [
                        'var1' => 'val1',
                    ],
                ],
            ],
        ];
        $confMap2 = [
            'metadata' => [
                'version' => 1,
            ],
            'data' => [
                'l1' => [
                    'type' => 'array',
                    'value' => [
                        'var2' => 'val2',
                    ],
                ],
            ],
        ];
        $confMaps = [
            'cm1' => $confMap1,
            'cm2' => $confMap2,
        ];
        ConfMapService::setCustomMaps($confMaps);

        $confMapFinal = ConfMapService::mergeDefinedMapSet();

        $this->assertEquals($confMapFinal['l1']['value']['var1']['value'], "val1");
        $this->assertEquals($confMapFinal['l1']['value']['var2']['value'], "val2");
    }
}
