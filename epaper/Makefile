DIR_Config   = ./src/lib/device
DIR_EPD      = ./src/lib/epaper
DIR_BIN      = ./bin

OBJ_C = $(wildcard ${DIR_EPD}/*.c)
OBJ_O = $(patsubst %.c,${DIR_BIN}/%.o,$(notdir ${OBJ_C}))
RPI_DEV_C = $(wildcard $(DIR_BIN)/dev_hardware_SPI.o $(DIR_BIN)/RPI_sysfs_gpio.o $(DIR_BIN)/DEV_Config.o )

DEBUG = -D DEBUG

USELIB_RPI = USE_WIRINGPI_LIB

LIB_RPI = -lwiringPi -lm 

DEBUG_RPI = -D $(USELIB_RPI) -D RPI

.PHONY : RPI clean

RPI:RPI_DEV RPI_epd 

TARGET = epd
CC = gcc
MSG = -g -O2 -Wall -fPIC
CFLAGS += $(MSG)

RPI_epd:${OBJ_O}
	echo $(@)
	mkdir -p $(DIR_BIN)/shared
	$(CC) -shared -o $(DIR_BIN)/shared/libepaper.so -lwiringPi $(OBJ_O) $(RPI_DEV_C)
    
${DIR_BIN}/%.o:$(DIR_EPD)/%.c
	$(CC) $(CFLAGS) -c  $< -o $@ -I $(DIR_Config) $(DEBUG)

RPI_DEV:
	mkdir -p $(DIR_BIN)
	$(CC) $(CFLAGS) $(DEBUG_RPI) -c  $(DIR_Config)/dev_hardware_SPI.c -o $(DIR_BIN)/dev_hardware_SPI.o $(LIB_RPI) $(DEBUG)
	$(CC) $(CFLAGS) $(DEBUG_RPI) -c  $(DIR_Config)/RPI_sysfs_gpio.c -o $(DIR_BIN)/RPI_sysfs_gpio.o $(LIB_RPI) $(DEBUG)
	$(CC) $(CFLAGS) $(DEBUG_RPI) -c  $(DIR_Config)/DEV_Config.c -o $(DIR_BIN)/DEV_Config.o $(LIB_RPI) $(DEBUG)

clean :
	rm $(DIR_BIN)/*.o $(DIR_BIN)/shared/*.so
