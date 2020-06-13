# Thermometer display

This project aims to read the temperature readings of our greenhouse from the InfluxDB and display it on an e-paper display, that is connected to a Raspberry Pi.

This application uses PHP to render the view and communicates with the display via a library, that is accessed via the FFI feature of PHP 7.4.

The library code is taken and adapted from [the examples of the manufacturer](https://github.com/waveshare/e-Paper).

## Requirements

The epaper library requires WiringPi, that can be installed using:

```sh
sudo apt-get install wiringpi
```

Additionally `make` and `gcc` are required.

On the PHP side, the imagick extension is required.
To install the extension, the following can be used:

```sh
sudo apt-get install libmagickwand-dev
sudo pecl install imagick
```

## Build
To build the libepaper library use the following command:

```sh
cd epaper
make
```
