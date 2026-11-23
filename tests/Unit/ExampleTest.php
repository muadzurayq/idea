<?php

it('can visit the login page', function () {
    visit('/login')
        ->assertPathIs('/login');
});
