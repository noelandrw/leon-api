<?php

namespace Deployer;

require 'recipe/laravel.php';

// Set SSH Multiplexing
set('ssh_multiplexing', true);

// Set default branch
set('branch', 'master');

// Set git_tty
set('git_tty', false);

// Set php binary file path
set('bin/php', '/usr/bin/php8.1');

// Project name
set('application', 'leon-api');

// Project repository
set('repository', 'https://github.com/noelandrw/leon-api.git');

// Shared files/dirs between deploys
add('shared_files', ['.env']);
add('shared_dirs', ['public/files', 'storage']);
add('shared_dirs', ['public/images', 'storage']);

// Writable dirs by web server
add('writable_dirs', []);

set('keep_releases', 2);

// Hosts
host('production')
    ->setHostname('165.22.107.49') //need to be configured
    ->set('forward_agent',false)
    ->set('remote_user', 'root')
    ->set('port', 22)
    ->set('deploy_path', '/var/www/{{application}}')
    ->setLabels([
        'type' => 'app',
        'env' => 'production',
    ]);
    
task('deploy:vendors', function () {
    run('cd {{release_path}} && {{bin/php}} /usr/bin/composer update --verbose --prefer-dist --no-progress --no-interaction --optimize-autoloader');
});

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

/**
 * Default main deploy task from recipe
 * desc('Deploys your project');
 * task('deploy', [
 *     'deploy:prepare',
 *     'deploy:vendors',
 *     'artisan:storage:link',
 *     'artisan:config:cache',
 *     'artisan:route:cache',
 *     'artisan:view:cache',
 *     'artisan:event:cache',
 *     'artisan:migrate',
 *     'deploy:publish',
 * ]);
 */

before('artisan:config:cache', 'artisan:clear-compiled');
before('deploy:success', 'restart:services');