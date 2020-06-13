#define FFI_LIB "libwiringPi.so"

int wiringPiISR(int pin, int edgeType,  void (*function)(void));