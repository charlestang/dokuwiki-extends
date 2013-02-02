<?php

/**
 * DokuWiki Plugin progressbar (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Charles Tang <charlestang@foxmail.com>
 */
// must be run within Dokuwiki
defined('DOKU_INC') or die();
defined('DOKU_LF') or define('DOKU_LF', "\n");
defined('DOKU_TAB') or define('DOKU_TAB', "\t");
defined('DOKU_PLUGIN') or define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');

require_once DOKU_PLUGIN . 'syntax.php';

class syntax_plugin_progressbar extends DokuWiki_Syntax_Plugin {

    public function getType() {
        return 'substition';
    }

    public function getSort() {
        return 131;
    }

    /**
     * Handle the match
     * 
     * @param   $match   string    The text matched by the patterns
     * @param   $state   int       The lexer state for the match
     * @param   $pos     int       The character position of the matched text
     * @param   $handler Doku_Handler Reference to the Doku_Handler object
     * @return  array              Return an array with all data you want to use in render
     */
    public function handle($match, $state, $pos, &$handler) {
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
    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('<progress=(?:[0-9]{1,2}|100?)>', $mode, 'plugin_progressbar');
        $this->Lexer->addSpecialPattern('<progress=(?:\d+/\d+)>', $mode, 'plugin_progressbar');
    }

    /**
     * Create output
     * 
     * @param   $format   string        output format being rendered
     * @param   $renderer Doku_Renderer reference to the current renderer object
     * @param   $data     array         data created by handler()
     * @return  boolean                 rendered correctly?
     */
    public function render($format, &$renderer, $data) {
        if ($format!= 'xhtml')
            return false;
        if ($data[0] < 0) {
            $data[0] = 0;
        }
        if ($data[0] > 100) {
            $data[0] = 100;
        }
        $sizeRight = 100 - $data[0];

        $blank_pic_url = DOKU_URL . 'lib/images/blank.gif';

        if ($data[0] <= 0) {
            $leftBar = '';
        } else {
            $leftBar = <<< PROGRESS
    <span style="margin:0;padding:0;background-color:#74a6c9;height:8px;width:{$data[0]}px">
        <img src="{$blank_pic_url}" width="{$data[0]}" border="0" title="{$data[0]}%" 
            alt="{$data[0]}%" hspace="0" vspace="0" style="height:8px;" />
    </span>
PROGRESS;
        }

        if ($data[0] >= 100) {
            $rightBar = '';
        } else {
            $rightBar = <<< PROGRESS
    <span style="margin:0;padding:0;background-color: #dee7ec;height:8px;width:{$sizeRight}px">
        <img src="{$blank_pic_url}" width="{$sizeRight}" border="0" title="{$data[0]}%" 
            alt="{$data[0]}%" hspace="0" vspace="0" style="height:8px" />
    </span>
PROGRESS;
        }

        $renderer->doc .= '<span style="padding:0;height:8px;width:100px;">' . $leftBar . $rightBar . '</span>&nbsp;' . $data[0] . '%';
        return true;
    }

}
