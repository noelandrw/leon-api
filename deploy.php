<?php
namespace Deployer;

require 'recipe/laravel.php';
require 'contrib/npm.php';

// Set SSH Multiplexing
set('ssh_multiplexing', true);

// Set default branch
set('branch', 'master');

// Set git_tty
set('git_tty', false);

// Project name
set('application', 'leon-api');

// Project repository
set('repository', 'git@github.com:noelandrw/leon-api.git');

// Set php binary file path
set('bin/php', '/usr/bin/php8.1');

// Shared files/dirs between deploys
add('shared_files', ['.env']);
add('shared_dirs', ['public/files', 'storage']);
add('shared_dirs', ['public/images', 'storage']);

// Writable dirs by web server
add('writable_dirs', []);

set('keep_releases', 2);

// Hosts
host('production')
    ->setHostname('165.22.107.49')
    ->set('remote_user', 'root')
    ->set('deploy_path', '/var/www/{{application}}')
    ->set('port', 22)
    ->setLabels([
        'type' => 'app',
        'env' => 'production',
    ]);

task('artisan:clear-compiled', function () {
    run('{{bin/php}} {{release_path}}/artisan clear-compiled');
});

task('restart:web', function () {
    run('sudo service php8.1-fpm restart');
    run('sudo service nginx restart');
})->select('type=app');

task('restart:workers', function () {
    run('{{bin/php}} {{release_path}}/artisan queue:restart');
})->select('type=app');

task('restart:services', ['restart:web', 'restart:workers']);

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Hooks
before('artisan:config:cache', 'artisan:clear-compiled');
before('deploy:success', 'restart:services');

task('assets:generate', function() {
    cd('{{release_path}}');
    run('npm install');
    run('npm run dev');
  })->desc('Assets generation');

//after('deploy:update_code', 'npm:install');
after('npm:install','assets:generate');
