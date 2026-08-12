<?php

$OAUTH_CONFIG = [
  'github' => [
    'client_id' => getenv('GITHUB_CLIENT_ID') ?? '',
    'client_secret' => getenv('GITHUB_CLIENT_SECRET') ?? '',
    'redirect_uri' => getenv('GITHUB_REDIRECT_URI') ?? '',
    'scope' => ["read:user", "user:email"],
  ]
];