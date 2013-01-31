<?php

/* DokuWiki Progressbar plugin
 * Internal version 1.0.0
 * 
 * Copyright (C) 2013 Charles Tang 
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if (!defined('DOKU_INC'))
    define('DOKU_INC', realpath(dirname(__FILE__) . '/../../') . '/');
if (!defined('DOKU_PLUGIN'))
    define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
require_once(DOKU_PLUGIN . 'syntax.php');

class syntax_plugin_progressbar extends DokuWiki_Syntax_Plugin {

    /**
     * return some info
     */
    function getInfo() {
        return array
            (
            'author' => 'Charles Tang',
            'email' => 'charletang@foxmail.com',
            'date' => '2013-01-31',
            'name' => 'Progressbar',
            'desc' => 'Makes progress bars on wiki pages.',
            'url' => 'https://github.com/charlestang/dokuwiki-extends',
        );
    }

    /**
     * What kind of syntax are we?
     */
    function getType() {
        return 'substition';
    }

    /**
     * Where to sort in?
     */
    function getSort() {
        return 999;
    }

    /**
     * Handle the match
     */
    function handle($match, $state, $pos, &$handler) {
        $match = substr($match, 10, -1);
        if (false !== strpos($match, '/')) {
            $nums = explode('/', $match, 2);
            return array(round($nums[0] / $nums[1] * 100, 1));
        }
        return array($match);
    }

    /**
     * Connect pattern to lexer
     */
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('<progress=(?:[0-9]{1,2}|100?)>', $mode, 'plugin_progressbar');
        $this->Lexer->addSpecialPattern('<progress=(?:\d+/\d+)>', $mode, 'plugin_progressbar');
    }

    /**
     * Create output
     */
    function render($mode, &$renderer, $data) {
        if ($data[0] < 0) {
            $data[0] = 0;
        }
        if ($data[0] > 100) {
            $data[0] = 100;
        }
        $sizeLeft = 100 - $data[0];

        $renderer->doc .= '<span style="padding:0;height:8px;width: 100px;">' . ($data[0] <= 0 ? '' : '<span style="margin:0;padding:0;background-color:#74a6c9; height:8px; width:' . $data[0] . '"><img src="' . dirname($_SERVER['PHP_SELF']) . 'lib/images/blank.gif" width="' . $data[0] . '" border="0" title="' . $data[0] . '%" alt="' . $data[0] . '%" hspace="0" vspace="0" style="height:8px" /></span>') . ($data[0] >= 100 ? '' : '<span style="margin:0;padding:0;background-color: #dee7ec;height:8px;width:' . $sizeLeft . '"><img src="' . dirname($_SERVER['PHP_SELF']) . 'lib/images/blank.gif" width="' . $sizeLeft . '" border="0" title="' . $data[0] . '%" alt="' . $data[0] . '%" hspace="0" vspace="0" style="height:8px" /></span>') . '</span>&nbsp;' . $data[0] . '%';

        return true;
    }

}
