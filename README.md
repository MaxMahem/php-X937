php-X937
========
A php based X937 file library.

Features
--------
* Able to read and parse all record types of the  X937-2003.
  * ASCII and EBCDIC file support.
* Rudimentary validation of file values according to spec.
* Translation of some Predefined Field values (~50% coverage).
* Object Oriented, Iterator based Access.

To Do
-----
* File Writing Support
  * Ability to Generate Dummy Records
* Ability to Extract Image Files
  * Ability to perform automated IQA on these files.
* Increased coverage of fields with predefined values (~50% coverage, 114/272).
* Cash Letter, Bundle, and Check sub-types (composite of X937File probably)
* More Iterator options.
* Indexed File Option.
* UCD Support
* X100 Support
