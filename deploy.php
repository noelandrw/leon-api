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
    ->set('remote_user', 'root')
    ->set('deploy_path', '/var/www/leon-api');

// Hooks
//before('deploy:symlink', 'artisan:migrate');
//after('deploy:failed', 'deploy:unlock');
