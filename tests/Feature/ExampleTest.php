<?php

it('returns a successful response for the welcome page', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
