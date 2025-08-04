<?php

namespace Config;

use CodeIgniter\Events\Events;
use CodeIgniter\Exceptions\FrameworkException;
use CodeIgniter\HotReloader\HotReloader;

/*
 * --------------------------------------------------------------------
 * Application Events
 * --------------------------------------------------------------------
 * Events allow you to tap into the execution of the program without
 * modifying or extending core files. This file provides a central
 * location to define your events, though they can always be added
 * at run-time, also, if needed.
 *
 * You create code that can execute by subscribing to events with
 * the 'on()' method. This accepts any form of callable, including
 * Closures, that will be executed when the event is triggered.
 *
 * Example:
 *      Events::on('create', [$myInstance, 'myMethod']);
 */

Events::on('pre_system', static function () {
    if (ENVIRONMENT !== 'testing') {
        if (ini_get('zlib.output_compression')) {
            return true;
            //throw FrameworkException::forEnabledZlibOutputCompression();
        }

        while (ob_get_level() > 0) {
            ob_end_flush();
        }

        ob_start(static fn ($buffer) => $buffer);
    }

    /*
     * --------------------------------------------------------------------
     * Debug Toolbar Listeners.
     * --------------------------------------------------------------------
     * If you delete, they will no longer be collected.
     */
    if (CI_DEBUG && ! is_cli()) {
        Events::on('DBQuery', 'CodeIgniter\Debug\Toolbar\Collectors\Database::collect');
        service('toolbar')->respond();
        // Hot Reload route - for framework use on the hot reloader.
        if (ENVIRONMENT === 'development') {
            service('routes')->get('__hot-reload', static function (): void {
                (new HotReloader())->run();
            });
        }
    }
});

Events::on(
    'DBQuery',
    static function (\CodeIgniter\Database\Query $query) {
        $sql = $query->getOriginalQuery();
        $lowerSql = strtolower($sql);
        if (!empty($lowerSql)) {
            if (strpos($lowerSql, 'select') === 0) {
                $operation = 'SELECT';
            } elseif (strpos($lowerSql, 'insert') === 0) {
                $operation = 'INSERT';
            } elseif (strpos($lowerSql, 'update') === 0) {
                $operation = 'UPDATE';
            } elseif (strpos($lowerSql, 'delete') === 0) {
                $operation = 'DELETE';
            } else {
                $operation = 'UNKNOWN';
            }
            $tablesAffected = ['ad_spaces','categories','fonts','general_settings','languages','language_translations','pages','polls','poll_votes','roles_permissions','routes','settings','themes','widgets'];
            foreach ($tablesAffected as $table) {
                if (strpos($lowerSql, $table) !== false && in_array($operation, ['INSERT', 'UPDATE', 'DELETE'])) {
                    resetCacheStatic(false);
                }
            }
        }
    }
);
