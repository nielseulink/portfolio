<?php

test('legacy welcome route is replaced by personal site home page', function () {
    $this->get('/')
        ->assertSuccessful()
        ->assertDontSee("Let's get started");
});
