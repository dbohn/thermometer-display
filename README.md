# Thermometer display

This project aims to read the temperature readings of our greenhouse from the InfluxDB and display it on an e-paper display.

This application uses PHP to render the view and communicates with the display via a library, that is accessed via the FFI feature of PHP 7.4.

The library code is taken and adapted from [the examples of the manufacturer](https://github.com/waveshare/e-Paper).