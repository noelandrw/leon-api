<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'leon-api');

// Project repository
set('repository', 'git@github.com:noelandrw/leon-api.git');


//add('shared_files', []);
//add('shared_dirs', []);
//add('writable_dirs', []);


// Hosts

host('165.22.107.49')
    ->user('deployer')
    ->identityFile('~/.ssh/deployerkey')
    ->set('deploy_path', '/var/www/leon-api');

// Hooks

after('deploy:failed', 'deploy:unlock');
// Migrate database before symlink new release.

//before('deploy:symlink', 'artisan:migrate');