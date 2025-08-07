<?php

namespace GutenkitPro\Config;

defined('ABSPATH') || exit;

class PostMeta
{

    use \GutenkitPro\Traits\Singleton;

    // class initilizer method
    public function __construct()
    {
        add_action("gutenkit/post-meta/list", array($this, "set_pro_meta_list"));
    }

    private static function generate_device_properties($condition)
    {
        if($condition == 'forSliders'){
            return [
                "type" => "object",
                "properties" => [
                    "size" => [
                        "type" => "number",
                        "default" => 0
                    ],
                    "unit" => [
                        "type" => "string",
                        "default" => "%"
                    ]
                ]
            ];
        } else if($condition == 'forBoxValues'){
            return [
                "type" => "object",
                "properties" => [
                    "top" => [
                        "type" => "string",
                    ],
                    "bottom" => [
                        "type" => "string",
                    ],
                    "left" => [
                        "type" => "string",
                    ],
                    "right" => [
                        "type" => "string",
                    ]
                ]
            ];
        }
    }

    // register post meta
    public function set_pro_meta_list($lists) {
        $slider_device_properties = self::generate_device_properties('forSliders');
        $box_values_device_properties = self::generate_device_properties('forBoxValues');

        $lists = array_merge($lists, [
            "enableOnePageScrollInPage" => [
                "post_type" => "",
                "args" => [
                    "type"         => "boolean",
                    "show_in_rest" => true,
                    "single" => true,
                    "default" => false
                ]
            ],
            "onePageScrollSpeed" => [
                "post_type" => "",
                "args" => [
                    "type"         => "number",
                    "show_in_rest" => true,
                    "single" => true,
                    "default" => 1000
                ],
            ],
            "onePageScrollAnimation" => [
                "post_type" => "",
                "args" => [
                    "type"         => "string",
                    "show_in_rest" => true,
                    "single" => true,
                    "default" => "easeInOutQuad"
                ]
            ],
            "onePageScrollShowDotNavigation" => [
                "post_type" => "",
                "args" => [
                    "type"         => "boolean",
                    "show_in_rest" => true,
                    "single" => true,
                    "default" => true
                ]
            ],
            "onePageScrollNavigation" => [
                "post_type" => "",
                "args" => [
                    "type"         => "string",
                    "show_in_rest" => true,
                    "single" => true,
                    "default" => "scaleUp"
                ]
            ],
            "onePageScrollNavigationPosition" => [
                "post_type" => "",
                "args" => [
                    "type"         => "string",
                    "show_in_rest" => true,
                    "single" => true,
                    "default" => "right"
                ]
            ],
            "onePageScrollNavigationHorizontal" => [ //post meta key
                "post_type" => "", //post type name put empty for all
                "args" => [ //pass arguments or an empty array
                    "type"         => "object",
                    "show_in_rest" => [ //show in rest api
                        "schema" => [ //schema
                            "type" => "object",
                            "properties" => [
                                "Desktop"         => $slider_device_properties,
                                "Tablet"          => $slider_device_properties,
                                "Mobile"          => $slider_device_properties,
                                "TabletLandscape" => $slider_device_properties,
                                "MobileLandscape" => $slider_device_properties,
                                "Laptop"          => $slider_device_properties,
                                "WideScreen"      => $slider_device_properties
                            ]
                        ]
                    ],
                    "single" => true,
                ]
            ],
            "onePageScrollNavigationVertical" => [ //post meta key
                "post_type" => "", //post type name put empty for all
                "args" => [ //pass arguments or an empty array
                    "type"         => "object",
                    "show_in_rest" => [ //show in rest api
                        "schema" => [ //schema
                            "type" => "object",
                            "properties" => [
                                "Desktop"         => $slider_device_properties,
                                "Tablet"          => $slider_device_properties,
                                "Mobile"          => $slider_device_properties,
                                "TabletLandscape" => $slider_device_properties,
                                "MobileLandscape" => $slider_device_properties,
                                "Laptop"          => $slider_device_properties,
                                "WideScreen"      => $slider_device_properties
                            ]
                        ]
                    ],
                    "single" => true,
                ]
            ],
            "onePageScrollNavigationSpacing" => [ //post meta key
                "post_type" => "", //post type name put empty for all
                "args" => [ //pass arguments or an empty array
                    "type"         => "object",
                    "show_in_rest" => [ //show in rest api
                        "schema" => [ //schema
                            "type" => "object",
                            "properties" => [
                                "Desktop"         => $slider_device_properties,
                                "Tablet"          => $slider_device_properties,
                                "Mobile"          => $slider_device_properties,
                                "TabletLandscape" => $slider_device_properties,
                                "MobileLandscape" => $slider_device_properties,
                                "Laptop"          => $slider_device_properties,
                                "WideScreen"      => $slider_device_properties
                            ]
                        ]
                    ],
                    "single" => true,
                ]
            ],
            "onePageScrollNavigationColor" => [
                "post_type" => "",
                "args" => [
                    "type"         => "string",
                    "show_in_rest" => true,
                    "single" => true,
                    "default" => "#00ff0d"
                ]
            ],
            "onePageScrollNavigationColorHover" => [
                "post_type" => "",
                "args" => [
                    "type"         => "string",
                    "show_in_rest" => true,
                    "single" => true,
                    "default" => "#00ff0d"
                ]
            ],
            "onePageScrollNavigationColorActive" => [
                "post_type" => "",
                "args" => [
                    "type"         => "string",
                    "show_in_rest" => true,
                    "single" => true,
                    "default" => "#00ff0d"
                ]
            ],
            "onePageScrollNavigationIcon" => [
                "post_type" => "",
                "args" => [
                    "type"         => "object",
                    "show_in_rest" => [
                        "schema" => [
                            "type" => "object",
                            'additionalProperties' => array(
                                'type' => 'object',
                            ),
                            "properties" => [
                                "id" => [
                                    "type" => "number",
                                ],
                                "label" => [
                                    "type" => "string",
                                ],
                                "src" => [
                                    "type" => "string",
                                ],
                                "title" => [
                                    "type" => "string",
                                ],
                                "type" => [
                                    "type" => "string",
                                ]
                            ]
                        ]
                    ],
                    "single" => true
                ]
            ],
            "onePageScrollNavigationWidth" => [ //post meta key
                "post_type" => "", //post type name put empty for all
                "args" => [ //pass arguments or an empty array
                    "type"         => "object",
                    "show_in_rest" => [ //show in rest api
                        "schema" => [ //schema
                            "type" => "object",
                            "properties" => [
                                "Desktop"         => $slider_device_properties,
                                "Tablet"          => $slider_device_properties,
                                "Mobile"          => $slider_device_properties,
                                "TabletLandscape" => $slider_device_properties,
                                "MobileLandscape" => $slider_device_properties,
                                "Laptop"          => $slider_device_properties,
                                "WideScreen"      => $slider_device_properties
                            ]
                        ]
                    ],
                    "single" => true,
                ]
            ],
            "onePageScrollNavigationWidthHover" => [ //post meta key
                "post_type" => "", //post type name put empty for all
                "args" => [ //pass arguments or an empty array
                    "type"         => "object",
                    "show_in_rest" => [ //show in rest api
                        "schema" => [ //schema
                            "type" => "object",
                            "properties" => [
                                "Desktop"         => $slider_device_properties,
                                "Tablet"          => $slider_device_properties,
                                "Mobile"          => $slider_device_properties,
                                "TabletLandscape" => $slider_device_properties,
                                "MobileLandscape" => $slider_device_properties,
                                "Laptop"          => $slider_device_properties,
                                "WideScreen"      => $slider_device_properties
                            ]
                        ]
                    ],
                    "single" => true,
                ]
            ],
            "onePageScrollNavigationWidthActive" => [ //post meta key
                "post_type" => "", //post type name put empty for all
                "args" => [ //pass arguments or an empty array
                    "type"         => "object",
                    "show_in_rest" => [ //show in rest api
                        "schema" => [ //schema
                            "type" => "object",
                            "properties" => [
                                "Desktop"         => $slider_device_properties,
                                "Tablet"          => $slider_device_properties,
                                "Mobile"          => $slider_device_properties,
                                "TabletLandscape" => $slider_device_properties,
                                "MobileLandscape" => $slider_device_properties,
                                "Laptop"          => $slider_device_properties,
                                "WideScreen"      => $slider_device_properties
                            ]
                        ]
                    ],
                    "single" => true,
                ]
            ],
            "onePageScrollNavigationHeight" => [ //post meta key
                "post_type" => "", //post type name put empty for all
                "args" => [ //pass arguments or an empty array
                    "type"         => "object",
                    "show_in_rest" => [ //show in rest api
                        "schema" => [ //schema
                            "type" => "object",
                            "properties" => [
                                "Desktop"         => $slider_device_properties,
                                "Tablet"          => $slider_device_properties,
                                "Mobile"          => $slider_device_properties,
                                "TabletLandscape" => $slider_device_properties,
                                "MobileLandscape" => $slider_device_properties,
                                "Laptop"          => $slider_device_properties,
                                "WideScreen"      => $slider_device_properties
                            ]
                        ]
                    ],
                    "single" => true,
                ]
            ],
            "onePageScrollNavigationHeightHover" => [ //post meta key
                "post_type" => "", //post type name put empty for all
                "args" => [ //pass arguments or an empty array
                    "type"         => "object",
                    "show_in_rest" => [ //show in rest api
                        "schema" => [ //schema
                            "type" => "object",
                            "properties" => [
                                "Desktop"         => $slider_device_properties,
                                "Tablet"          => $slider_device_properties,
                                "Mobile"          => $slider_device_properties,
                                "TabletLandscape" => $slider_device_properties,
                                "MobileLandscape" => $slider_device_properties,
                                "Laptop"          => $slider_device_properties,
                                "WideScreen"      => $slider_device_properties
                            ]
                        ]
                    ],
                    "single" => true,
                ]
            ],
            "onePageScrollNavigationHeightActive" => [ //post meta key
                "post_type" => "", //post type name put empty for all
                "args" => [ //pass arguments or an empty array
                    "type"         => "object",
                    "show_in_rest" => [ //show in rest api
                        "schema" => [ //schema
                            "type" => "object",
                            "properties" => [
                                "Desktop"         => $slider_device_properties,
                                "Tablet"          => $slider_device_properties,
                                "Mobile"          => $slider_device_properties,
                                "TabletLandscape" => $slider_device_properties,
                                "MobileLandscape" => $slider_device_properties,
                                "Laptop"          => $slider_device_properties,
                                "WideScreen"      => $slider_device_properties
                            ]
                        ]
                    ],
                    "single" => true,
                ]
            ],
            "onePageScrollNavigationBorder" => [
                "post_type" => "",
                "args" => [
                    "type" => "object",
                    "show_in_rest" => [
                        "schema" => [
                            "type" => "object",
                            "properties" => [
                                "Desktop" => [
                                    "type" => "object",
                                    "properties" => [
                                        "top" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "left" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "right" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "bottom" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "style" => [
                                            "type" => "string",
                                            "default" => "solid"
                                        ],
                                        "color" => [
                                            "type" => "string",
                                            "default" => "#00ff0d"
                                        ],
                                        "width" => [
                                            "type" => "string",
                                            "default" => "2px"
                                        ],
                                    ]
                                ],
                                "Tablet" => [
                                    "type" => "object",
                                    "properties" => [
                                        "top" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "left" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "right" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "bottom" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "style" => [
                                            "type" => "string",
                                            "default" => "solid"
                                        ],
                                        "color" => [
                                            "type" => "string",
                                            "default" => "#00ff0d"
                                        ],
                                        "width" => [
                                            "type" => "string",
                                            "default" => "2px"
                                        ],
                                    ]
                                ],
                                "Mobile" => [
                                    "type" => "object",
                                    "properties" => [
                                        "top" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "left" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "right" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "bottom" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "style" => [
                                            "type" => "string",
                                            "default" => "solid"
                                        ],
                                        "color" => [
                                            "type" => "string",
                                            "default" => "#00ff0d"
                                        ],
                                        "width" => [
                                            "type" => "string",
                                            "default" => "2px"
                                        ],
                                    ]
                                ],
                                "TabletLandscape" => [
                                    "type" => "object",
                                    "properties" => [
                                        "top" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "left" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "right" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "bottom" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "style" => [
                                            "type" => "string",
                                            "default" => "solid"
                                        ],
                                        "color" => [
                                            "type" => "string",
                                            "default" => "#00ff0d"
                                        ],
                                        "width" => [
                                            "type" => "string",
                                            "default" => "2px"
                                        ],
                                    ]
                                ],
                                "MobileLandscape" => [
                                    "type" => "object",
                                    "properties" => [
                                        "top" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "left" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "right" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "bottom" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "style" => [
                                            "type" => "string",
                                            "default" => "solid"
                                        ],
                                        "color" => [
                                            "type" => "string",
                                            "default" => "#00ff0d"
                                        ],
                                        "width" => [
                                            "type" => "string",
                                            "default" => "2px"
                                        ],
                                    ]
                                ],
                                "Laptop" => [
                                    "type" => "object",
                                    "properties" => [
                                        "top" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "left" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "right" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "bottom" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "style" => [
                                            "type" => "string",
                                            "default" => "solid"
                                        ],
                                        "color" => [
                                            "type" => "string",
                                            "default" => "#00ff0d"
                                        ],
                                        "width" => [
                                            "type" => "string",
                                            "default" => "2px"
                                        ],
                                    ]
                                ],
                                "WideScreen" => [
                                    "type" => "object",
                                    "properties" => [
                                        "top" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "left" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "right" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "bottom" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "style" => [
                                            "type" => "string",
                                            "default" => "solid"
                                        ],
                                        "color" => [
                                            "type" => "string",
                                            "default" => "#00ff0d"
                                        ],
                                        "width" => [
                                            "type" => "string",
                                            "default" => "2px"
                                        ],
                                    ]
                                ],
                            ]
                        ],
                    ],
                    "single" => true,
                ]
                ],

            "onePageScrollNavigationBorderHover" => [
                "post_type" => "",
                "args" => [
                    "type" => "object",
                    "show_in_rest" => [
                        "schema" => [
                            "type" => "object",
                            "properties" => [
                                "Desktop" => [
                                    "type" => "object",
                                    "properties" => [
                                        "top" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "left" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "right" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "bottom" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "style" => [
                                            "type" => "string",
                                        ],
                                        "color" => [
                                            "type" => "string",
                                        ],
                                        "width" => [
                                            "type" => "string",
                                        ],
                                    ]
                                ],
                                "Tablet" => [
                                    "type" => "object",
                                    "properties" => [
                                        "top" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "left" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "right" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "bottom" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "style" => [
                                            "type" => "string",
                                        ],
                                        "color" => [
                                            "type" => "string",
                                        ],
                                        "width" => [
                                            "type" => "string",
                                        ],
                                    ]
                                ],
                                "Mobile" => [
                                    "type" => "object",
                                    "properties" => [
                                        "top" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "left" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "right" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "bottom" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "style" => [
                                            "type" => "string",
                                        ],
                                        "color" => [
                                            "type" => "string",
                                        ],
                                        "width" => [
                                            "type" => "string",
                                        ],
                                    ]
                                ],
                                "TabletLandscape" => [
                                    "type" => "object",
                                    "properties" => [
                                        "top" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "left" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "right" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "bottom" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "style" => [
                                            "type" => "string",
                                        ],
                                        "color" => [
                                            "type" => "string",
                                        ],
                                        "width" => [
                                            "type" => "string",
                                        ],
                                    ]
                                ],
                                "MobileLandscape" => [
                                    "type" => "object",
                                    "properties" => [
                                        "top" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "left" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "right" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "bottom" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "style" => [
                                            "type" => "string",
                                        ],
                                        "color" => [
                                            "type" => "string",
                                        ],
                                        "width" => [
                                            "type" => "string",
                                        ],
                                    ]
                                ],
                                "Laptop" => [
                                    "type" => "object",
                                    "properties" => [
                                        "top" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "left" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "right" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "bottom" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "style" => [
                                            "type" => "string",
                                        ],
                                        "color" => [
                                            "type" => "string",
                                        ],
                                        "width" => [
                                            "type" => "string",
                                        ],
                                    ]
                                ],
                                "WideScreen" => [
                                    "type" => "object",
                                    "properties" => [
                                        "top" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "left" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "right" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "bottom" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "style" => [
                                            "type" => "string",
                                        ],
                                        "color" => [
                                            "type" => "string",
                                        ],
                                        "width" => [
                                            "type" => "string",
                                        ],
                                    ]
                                ],
                            ]
                        ],
                    ],
                    "single" => true,
                ]
                ],
            "onePageScrollNavigationBorderActive" => [
                "post_type" => "",
                "args" => [
                    "type" => "object",
                    "show_in_rest" => [
                        "schema" => [
                            "type" => "object",
                            "properties" => [
                                "Desktop" => [
                                    "type" => "object",
                                    "properties" => [
                                        "top" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "left" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "right" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "bottom" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "style" => [
                                            "type" => "string",
                                        ],
                                        "color" => [
                                            "type" => "string",
                                        ],
                                        "width" => [
                                            "type" => "string",
                                        ],
                                    ]
                                ],
                                "Tablet" => [
                                    "type" => "object",
                                    "properties" => [
                                        "top" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "left" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "right" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "bottom" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "style" => [
                                            "type" => "string",
                                        ],
                                        "color" => [
                                            "type" => "string",
                                        ],
                                        "width" => [
                                            "type" => "string",
                                        ],
                                    ]
                                ],
                                "Mobile" => [
                                    "type" => "object",
                                    "properties" => [
                                        "top" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "left" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "right" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "bottom" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "style" => [
                                            "type" => "string",
                                        ],
                                        "color" => [
                                            "type" => "string",
                                        ],
                                        "width" => [
                                            "type" => "string",
                                        ],
                                    ]
                                ],
                                "TabletLandscape" => [
                                    "type" => "object",
                                    "properties" => [
                                        "top" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "left" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "right" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "bottom" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "style" => [
                                            "type" => "string",
                                        ],
                                        "color" => [
                                            "type" => "string",
                                        ],
                                        "width" => [
                                            "type" => "string",
                                        ],
                                    ]
                                ],
                                "MobileLandscape" => [
                                    "type" => "object",
                                    "properties" => [
                                        "top" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "left" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "right" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "bottom" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "style" => [
                                            "type" => "string",
                                        ],
                                        "color" => [
                                            "type" => "string",
                                        ],
                                        "width" => [
                                            "type" => "string",
                                        ],
                                    ]
                                ],
                                "Laptop" => [
                                    "type" => "object",
                                    "properties" => [
                                        "top" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "left" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "right" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "bottom" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "style" => [
                                            "type" => "string",
                                        ],
                                        "color" => [
                                            "type" => "string",
                                        ],
                                        "width" => [
                                            "type" => "string",
                                        ],
                                    ]
                                ],
                                "WideScreen" => [
                                    "type" => "object",
                                    "properties" => [
                                        "top" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "left" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "right" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "bottom" =>[
                                            "type" => "object",
                                            "properties" => [
                                                "style" => [
                                                    "type" => "string",
                                                ],
                                                "color" => [
                                                    "type" => "string",
                                                ],
                                                "width" => [
                                                    "type" => "string",
                                                ],
                                            ]
                                        ],
                                        "style" => [
                                            "type" => "string",
                                        ],
                                        "color" => [
                                            "type" => "string",
                                        ],
                                        "width" => [
                                            "type" => "string",
                                        ],
                                    ]
                                ],
                            ]
                        ],
                    ],
                    "single" => true,
                ]
            ],
            "onePageScrollNavigationBorderRadius" => [
                "post_type" => "", //post type name put empty for all
                "args" => [ //pass arguments or an empty array
                    "type"         => "object",
                    "show_in_rest" => [ //show in rest api
                        "schema" => [ //schema
                            "type" => "object",
                            "properties" => [
                                "Desktop"         => $box_values_device_properties,
                                "Tablet"          => $box_values_device_properties,
                                "Mobile"          => $box_values_device_properties,
                                "TabletLandscape" => $box_values_device_properties,
                                "MobileLandscape" => $box_values_device_properties,
                                "Laptop"          => $box_values_device_properties,
                                "WideScreen"      => $box_values_device_properties
                            ]
                        ]
                    ],
                    "single" => true,
                ]
            ],
            "onePageScrollNavigationBorderRadiusHover" => [
                "post_type" => "", //post type name put empty for all
                "args" => [ //pass arguments or an empty array
                    "type"         => "object",
                    "show_in_rest" => [ //show in rest api
                        "schema" => [ //schema
                            "type" => "object",
                            "properties" => [
                                "Desktop"         => $box_values_device_properties,
                                "Tablet"          => $box_values_device_properties,
                                "Mobile"          => $box_values_device_properties,
                                "TabletLandscape" => $box_values_device_properties,
                                "MobileLandscape" => $box_values_device_properties,
                                "Laptop"          => $box_values_device_properties,
                                "WideScreen"      => $box_values_device_properties
                            ]
                        ]
                    ],
                    "single" => true,
                ]
            ],
            "onePageScrollNavigationBorderRadiusActive" => [
                "post_type" => "", //post type name put empty for all
                "args" => [ //pass arguments or an empty array
                    "type"         => "object",
                    "show_in_rest" => [ //show in rest api
                        "schema" => [ //schema
                            "type" => "object",
                            "properties" => [
                                "Desktop"         => $box_values_device_properties,
                                "Tablet"          => $box_values_device_properties,
                                "Mobile"          => $box_values_device_properties,
                                "TabletLandscape" => $box_values_device_properties,
                                "MobileLandscape" => $box_values_device_properties,
                                "Laptop"          => $box_values_device_properties,
                                "WideScreen"      => $box_values_device_properties
                            ]
                        ]
                    ],
                    "single" => true,
                ]
            ],


            // one page scroll navigation dot tooltip post metas

            "onePageScrollNavigationTooltipTypography" => [
                "post_type" => "",
                "args" => [
                    "type" => "object",
                    "show_in_rest" => [
                        "schema" => [
                            "type" => "object",
                            'additionalProperties' => [
                                'type' => 'object',
                            ],
                            "properties" => [
                                "fontFamily" => [
                                    "type" => "object",
                                    "properties" => [
                                        "label" => [
                                            "type" => "string",
                                        ],
                                        "value" => [
                                            "type" => "string",
                                        ],
                                        "variants" => [
                                            "type" => "array",
                                        ]
                                    ]
                                ],
                                "fontSize" => [
                                    "type" => "object",
                                    "properties" => [
                                        "Desktop"         => $slider_device_properties,
                                        "Tablet"          => $slider_device_properties,
                                        "Mobile"          => $slider_device_properties,
                                        "TabletLandscape" => $slider_device_properties,
                                        "MobileLandscape" => $slider_device_properties,
                                        "Laptop"          => $slider_device_properties,
                                        "WideScreen"      => $slider_device_properties
                                    ]
                                ],
                                "fontStyle" => [
                                    "type" => "string",
                                ],
                                "hasValue" => [
                                    "type" => "boolean",
                                ],
                                "fontWeight" => [
                                    "type" => "object",
                                    "properties" => [
                                        "label" => [
                                            "type" => "string",
                                        ],
                                        "value" => [
                                            "type" => "string",
                                        ]
                                    ]
                                ],
                                "letterSpacing" => [
                                    "type" => "object",
                                    "properties" => [
                                        "Desktop"         => $slider_device_properties,
                                        "Tablet"          => $slider_device_properties,
                                        "Mobile"          => $slider_device_properties,
                                        "TabletLandscape" => $slider_device_properties,
                                        "MobileLandscape" => $slider_device_properties,
                                        "Laptop"          => $slider_device_properties,
                                        "WideScreen"      => $slider_device_properties
                                    ]
                                ],
                                "lineHeight" => [
                                    "type" => "object",
                                    "properties" => [
                                        "Desktop"         => $slider_device_properties,
                                        "Tablet"          => $slider_device_properties,
                                        "Mobile"          => $slider_device_properties,
                                        "TabletLandscape" => $slider_device_properties,
                                        "MobileLandscape" => $slider_device_properties,
                                        "Laptop"          => $slider_device_properties,
                                        "WideScreen"      => $slider_device_properties
                                    ]
                                ],
                                "textDecoration" => [
                                    "type" => "string",
                                ],
                                "textTransform" => [
                                    "type" => "string",
                                ],
                                "wordSpacing" => [
                                    "type" => "object",
                                    "properties" => [
                                        "Desktop"         => $slider_device_properties,
                                        "Tablet"          => $slider_device_properties,
                                        "Mobile"          => $slider_device_properties,
                                        "TabletLandscape" => $slider_device_properties,
                                        "MobileLandscape" => $slider_device_properties,
                                        "Laptop"          => $slider_device_properties,
                                        "WideScreen"      => $slider_device_properties
                                    ]
                                ],
                            ]
                        ]
                    ],
                    "single" => true,

                ]
            ],
            "onePageScrollNavigationTooltipColor" => [
                "post_type" => "",
                "args" => [
                    "type"         => "string",
                    "show_in_rest" => true,
                    "single" => true,
                    "default" => "#ffffff"
                ]
            ],
            "onePageScrollNavigationTooltipColorHover" => [
                "post_type" => "",
                "args" => [
                    "type"         => "string",
                    "show_in_rest" => true,
                    "single" => true,
                ]
            ],
            "onePageScrollNavigationTooltipBgColor" => [
                "post_type" => "",
                "args" => [
                    "type"         => "string",
                    "show_in_rest" => true,
                    "single" => true,
                    "default" => "#00ff0d"
                ]
            ],
            "onePageScrollNavigationTooltipBgColorHover" => [
                "post_type" => "",
                "args" => [
                    "type"         => "string",
                    "show_in_rest" => true,
                    "single" => true,
                ]
            ],

            "onePageScrollNavigationTooltipPadding" => [
                "post_type" => "", //post type name put empty for all
                "args" => [ //pass arguments or an empty array
                    "type"         => "object",
                    "show_in_rest" => [ //show in rest api
                        "schema" => [ //schema
                            "type" => "object",
                            "properties" => [
                                "Desktop"         => $box_values_device_properties,
                                "Tablet"          => $box_values_device_properties,
                                "Mobile"          => $box_values_device_properties,
                                "TabletLandscape" => $box_values_device_properties,
                                "MobileLandscape" => $box_values_device_properties,
                                "Laptop"          => $box_values_device_properties,
                                "WideScreen"      => $box_values_device_properties
                            ]
                        ]
                    ],
                    "single" => true,
                ]
            ],
            "onePageScrollNavigationTooltipPaddingHover" => [
                "post_type" => "", //post type name put empty for all
                "args" => [ //pass arguments or an empty array
                    "type"         => "object",
                    "show_in_rest" => [ //show in rest api
                        "schema" => [ //schema
                            "type" => "object",
                            "properties" => [
                                "Desktop"         => $box_values_device_properties,
                                "Tablet"          => $box_values_device_properties,
                                "Mobile"          => $box_values_device_properties,
                                "TabletLandscape" => $box_values_device_properties,
                                "MobileLandscape" => $box_values_device_properties,
                                "Laptop"          => $box_values_device_properties,
                                "WideScreen"      => $box_values_device_properties
                            ]
                        ]
                    ],
                    "single" => true,
                ]
            ],
            "onePageScrollNavigationTooltipBorderRadius" => [
                "post_type" => "", //post type name put empty for all
                "args" => [ //pass arguments or an empty array
                    "type"         => "object",
                    "show_in_rest" => [ //show in rest api
                        "schema" => [ //schema
                            "type" => "object",
                            "properties" => [
                                "Desktop"         => $box_values_device_properties,
                                "Tablet"          => $box_values_device_properties,
                                "Mobile"          => $box_values_device_properties,
                                "TabletLandscape" => $box_values_device_properties,
                                "MobileLandscape" => $box_values_device_properties,
                                "Laptop"          => $box_values_device_properties,
                                "WideScreen"      => $box_values_device_properties
                            ]
                        ]
                    ],
                    "single" => true,
                ]
            ],
            "onePageScrollNavigationTooltipBorderRadiusHover" => [
                "post_type" => "", //post type name put empty for all
                "args" => [ //pass arguments or an empty array
                    "type"         => "object",
                    "show_in_rest" => [ //show in rest api
                        "schema" => [ //schema
                            "type" => "object",
                            "properties" => [
                                "Desktop"         => $box_values_device_properties,
                                "Tablet"          => $box_values_device_properties,
                                "Mobile"          => $box_values_device_properties,
                                "TabletLandscape" => $box_values_device_properties,
                                "MobileLandscape" => $box_values_device_properties,
                                "Laptop"          => $box_values_device_properties,
                                "WideScreen"      => $box_values_device_properties
                            ]
                        ]
                    ],
                    "single" => true,
                ]
            ],
            "pageSettingsCustomCss" => [
                "post_type" => "",
                "args" => [
                    "type"         => "string",
                    "show_in_rest" => true,
                    "single" => true,
                ]
            ]
        ]);

        return $lists;
    }
}
