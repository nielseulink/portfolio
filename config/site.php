<?php

return [

    'social' => [
        'github' => 'https://github.com/nielseulink',
        'linkedin' => 'https://www.linkedin.com/in/nielseulink',
    ],

    'projects' => [
        [
            'name' => 'Button+ Node-RED',
            'description' => 'Node-RED module for controlling Button+ devices over MQTT, with nodes for button presses, LEDs, display updates, page switching and EVCC-style energy flow rendering.',
            'url' => 'https://github.com/nielseulink/buttonplus-nodered',
            'link_label' => 'View on GitHub',
            'tags' => ['Button+', 'Node-RED', 'JavaScript', 'MQTT', 'Smarthome', 'Home Assistant'],
        ],
        [
            'name' => 'HA PSA Car Controller',
            'description' => 'Home Assistant integration for the psa_car_controller project by @flobz. It publishes various vehicle status and control data and allows to control the car from Home Assistant.',
            'url' => 'https://github.com/nielseulink/ha_psa_controller',
            'link_label' => 'View on GitHub',
            'tags' => ['Home Assistant', 'Python', 'HACS'],
        ],
        [
            'name' => 'Eagle-Eye',
            'description' => 'Eagle-Eye is a product by EagleSCM that allows our customers to manage their full supply chain and logistics. As co-founder and fullstack developer I built the application and actively maintain and develop it.',
            'url' => 'https://eaglescm.com/en/eagle-eye',
            'link_label' => 'Visit website',
            'tags' => ['Laravel', 'Livewire', 'Flux UI'],
        ],
    ],

];
