Id to Hash
==========

A PHP implementation for generating hashes from numbers, very useful for creating
short urls such as those created by Tiny URL- and bit.ly-like URLs.

This is entirely based on the implementation by Michael Fogleman for Python,
it can be found here: https://github.com/Alir3z4/python-short_url

The following is taken verbatim from his implementation, but should hold true here:

    "A bit-shuffling approach is used to avoid generating consecutive, predictable
    URLs.  However, the algorithm is deterministic and will guarantee that no
    collisions will occur.

    The URL alphabet is fully customizable and may contain any number of
    characters(...)

    The block size specifies how many bits will be shuffled.  The lower BLOCK_SIZE
    bits are reversed.  Any bits higher than BLOCK_SIZE will remain as is.
    BLOCK_SIZE of 0 will leave all bits unaffected and the algorithm will simply
    be converting your integer to a different base.

    The intended use is that incrementing, consecutive integers will be used as
    keys to generate the short URLs.  For example, when creating a new URL, the
    unique integer ID assigned by a database could be used to generate the URL
    by using this module.  Or a simple counter may be used.  As long as the same
    integer is not used twice, the same short URL will not be generated twice.

    The module supports both encoding and decoding of URLs. The min_length
    parameter allows you to pad the URL if you want it to be a specific length."

I should add here the major change from the original implementation:

Whereas there was only one alphabet used this library requires two, that is because
the original implementation, whilst allowing for padding, used the character that
represented 0 to pad the generated string. Here, we have a specific alphabet used
for padding, thus making the guessing of any sort of pattern harder.

But don't worry for the function is still deterministic and reversible given the
original parameters inputted.

*Sample Usage:*

::

    $hasher = new IdToHash([
        'alphabet'=>'6c423ZWUGdFQVDHNRT7MvtgkKXm1iqC', //my alphabet goes here
        'nullAlphabet'=>'LjropOAf95anYSslBeIJ0PwxubzyhE', //my null alphabet goes here
        'block_size'=> 24
    ]);

    echo $hasher->encode(25);
    // FKQgX

    echo $hasher->encode(25, 10);
    // ropOAFKQgX

    echo $hasher->decode('FKQgX');
    // 25

    echo $hasher->decode('ropOAFKQgX');
    // 25

Also, there's a Javascript file that should help you with generating your alphabets, it's
also available here to save you the fuss: https://jsfiddle.net/UDany/f8782jw3/


Tests
-----

Sadly, no standardized pretty tests, but there is a Test method tha allows you to test that
it's deterministic and reversible.


----

========== ======
Source	  https://github.com/udany/IdToHash
Issues	  https://github.com/udany/IdToHash/issues
Author	  Daniel Andrade
License	  MIT
========== ======

