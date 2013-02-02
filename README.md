dokuwiki-extends
================

This is the place I host the dokuwiki-extends I used and developed.

Plugins
-------

### Progressbar

This plugin display a progress bar on your wiki. The progress is defined by the integer number.

The first type, you can use the percentage, that a number between 0 to 100

> `<progress=87>`

or you can use two number divide style

> `<progress=34/105>`

the first number should be smaller than the second one, and both of them should be integer.

### Booknote

This plugin grab data from douban.com and show basic book info on your wiki, include title, pages, rating,
and you can insert some note for the book.

> `<booknote isbn=9787547011607> This is a very nice book.</booknote>`
