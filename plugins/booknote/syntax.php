<?php

/**
 * DokuWiki Plugin booknote (Syntax Component)
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

class syntax_plugin_booknote extends DokuWiki_Syntax_Plugin {

    public function getType() {
        return 'container';
    }

    public function getPType() {
        return 'normal';
    }

    public function getSort() {
        return 15;
    }

    public function getAllowedTypes() {
        return array('formatting', 'substition');
    }

    public function connectTo($mode) {
        $this->Lexer->addEntryPattern('<booknote(?:[^>]*)>', $mode, 'plugin_booknote');
    }

    public function postConnect() {
        $this->Lexer->addExitPattern('</booknote>', 'plugin_booknote');
    }

    public function handle($match, $state, $pos, &$handler) {
        $data = array();

        static $i;

        switch ($state) {
            case DOKU_LEXER_UNMATCHED:
                $data['state'] = DOKU_LEXER_UNMATCHED;
                $data['content'] = $match;
                break;
            case DOKU_LEXER_ENTER:
                $data['state'] = DOKU_LEXER_ENTER;
                preg_match('#<booknote([^>]*)>#i', $match, $matches);
                $pairs = explode(' ', trim($matches[1]));
                foreach ($pairs as $pair) {
                    $temp = explode('=', $pair);
                    $data['params'][trim($temp[0])] = trim($temp[1]);
                }

                break;
            case DOKU_LEXER_EXIT:
                $data['state'] = DOKU_LEXER_EXIT;
                break;
        }

        return $data;
    }

    public function render($mode, &$renderer, $data) {
        if ($mode != 'xhtml') {
            return false;
        }

        switch ($data['state']) {
            case DOKU_LEXER_UNMATCHED:
                $renderer->doc .= str_replace(DOKU_LF, '<br/>', $data['content']);
                break;
            case DOKU_LEXER_ENTER:
                $bookinfo = $this->doHttpRequest($data['params']['isbn']);
                if (false !== $bookinfo) {
                    $fileName = $this->getFileName($data['params']['isbn']);
                    file_put_contents($fileName, $bookinfo);
                    $bookinfo = json_decode($bookinfo, true);
                    $book = <<< BOOK
        <span style="clear:both"></span>
        <table style="float:left;width:280px">
            <tbody>
                <tr><td rowspan="5"><img src="{$bookinfo['image']}" /></td><td><a target="_blank" href="{$bookinfo['alt']}">《{$bookinfo['title']}》</a></td></tr>
                <tr><td>评分：{$bookinfo['rating']['average']}/{$bookinfo['rating']['max']}</td></tr>
                <tr><td>作者：{$bookinfo['author'][0]}</td></tr>
                <tr><td>页数：{$bookinfo['pages']}</td></tr>
                <tr><td>ISBN：{$bookinfo['isbn13']}</td></tr>
            </tbody>
        </table>
        <div style="margin-left:280px;padding-left:10px;">
BOOK;
                $renderer->doc .= $book;
                }
                break;
            case DOKU_LEXER_EXIT:
                $renderer->doc .= '</div><span style="display:block;width:100%;clear:both"></span>';
                break;
        }

        return true;
    }

    public function doHttpRequest($isbn) {

        $fileName = $this->getFileName($isbn);
        if (file_exists($fileName)) {
            return file_get_contents($fileName);
        }

        $url = 'https://api.douban.com/v2/book/isbn/' . $isbn;

        $curl = curl_init($url);
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 10,
        );
        if (curl_setopt_array($curl, $options)) {
            $result = curl_exec($curl);
        }
        curl_close($curl);
        return $result;
    }

    public function getFileName($isbn) {
        return DOKU_PLUGIN .'booknote/books/isbn_' . $isbn;
    }

}

// vim:ts=4:sw=4:et:
