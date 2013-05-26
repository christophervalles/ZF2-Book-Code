<?php

return array(
    'roles' => array(
        'guest',
        'member'
    ),
    'permissions' => array(
        'guest' => array(
            'users-signup',
            'users-login'
        ),
        'member' => array(
            'users-logout'
        )
    )
);