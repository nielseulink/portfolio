<?php

use Illuminate\Support\Str;

test('fenced code blocks render as pre', function () {
    $html = Str::markdown(<<<'MD'
Here is some text.

```yaml
base_topic: homeassistant/statestream
publish_attributes: true
```

More text.
MD);

    expect($html)
        ->toContain('<pre>')
        ->toContain('language-yaml')
        ->toContain('base_topic');
});

test('inline code uses single backticks not fenced blocks', function () {
    $html = Str::markdown('Use `mqtt_statestream: !include mqtt_statestream.yaml` in configuration.yaml.');

    expect($html)
        ->toContain('<code>mqtt_statestream: !include mqtt_statestream.yaml</code>')
        ->not->toContain('<pre>');
});

test('heading before yaml fence keeps language-yaml class', function () {
    $html = Str::markdown(<<<'MD'
#### mqtt_statestream.yaml

```yaml
base_topic: foo
```
MD);

    expect($html)->toContain('language-yaml');
});
