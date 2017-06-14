php-X937
========
A php based X937 file library.

Features
--------
* Able to parse 'arbitrary' variations on the X937 specification, so long as it is provided a proper XML specification file.
* Supports both ASCII and EBCDIC information, with translation between the two.
* Rudimentary validation of file values according to spec.
* Object Oriented, Iterator based Access.
* Sample Human readable writer.
* Sample ASCII writer.
* Able to perform modifications to records.

To Do
-----
* Perform automated IQA on image files.
* Cash Letter, Bundle, and Check sub-types (composite of X937File probably)
* Indexed File Option.
* Better handling of decoration and validation options
* Add query methods?

Check test.php for example usage.
