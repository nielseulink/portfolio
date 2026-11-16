<?php

return [

    'social' => [
        'github' => 'https://github.com/nielseulink',
        'linkedin' => 'https://www.linkedin.com/in/nielseulink',
    ],

    /*
     * The contributions section lists merged pull requests by this user in repositories of others. Owners listed here
     * are left out (business work).
     */
    'github' => [
        'username' => 'nielseulink',
        'excluded_contribution_owners' => ['Eagle-SCM'],
    ],

    /*
     * Projects on /projects. `repository` (owner/name of a public repository) adds GitHub stats; `status` set to
     * `coming-soon` shows the project without a link.
     */
    'projects' => [
        [
            'name' => 'Button+ Node-RED',
            'description' => 'Node-RED module for controlling Button+ devices over MQTT, with nodes for button presses, LEDs, display updates and page switching.',
            'url' => 'https://github.com/nielseulink/buttonplus-nodered',
            'repository' => 'nielseulink/buttonplus-nodered',
            'link_label' => 'View on GitHub',
            'tags' => ['Button+', 'Node-RED', 'JavaScript', 'MQTT', 'Smarthome', 'Home Assistant'],
        ],
        [
            'name' => 'HA PSA Car Controller',
            'description' => 'Home Assistant integration for the psa_car_controller project by @flobz. It publishes various vehicle status and control data and allows to control the car from Home Assistant.',
            'url' => 'https://github.com/nielseulink/ha_psa_controller',
            'repository' => 'nielseulink/ha_psa_controller',
            'link_label' => 'View on GitHub',
            'tags' => ['Home Assistant', 'Python', 'HACS'],
        ],
        [
            'name' => 'APsystems ECU for Home Assistant',
            'description' => 'Home Assistant integration that reads APsystems ECU micro inverters over Modbus TCP (SunSpec), with system totals and one device per inverter for power, energy, voltage and temperature.',
            'url' => 'https://github.com/nielseulink/ha_apsystems_ecu_modbus',
            'repository' => 'nielseulink/ha_apsystems_ecu_modbus',
            'link_label' => 'View on GitHub',
            'tags' => ['Home Assistant', 'Python', 'HACS', 'Modbus', 'Energy'],
        ],
        [
            'name' => 'Deelwaarder',
            'description' => 'Shares the cost of subscriptions with friends: every billing period each participant gets a payment request and a link that shows how their share is built up. An AI assistant manages it over MCP.',
            'status' => 'coming-soon',
            'tags' => ['Laravel', 'Livewire', 'Flux UI', 'MCP'],
        ],
        [
            'name' => 'Mealplanner',
            'description' => 'Recipes, freezer stock, weekly meal plans and shopping lists in one app, with an MCP server for AI assistants and a Home Assistant integration.',
            'status' => 'coming-soon',
            'tags' => ['Laravel', 'Livewire', 'Flux UI', 'MCP', 'Home Assistant'],
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
