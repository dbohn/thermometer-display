#define FFI_LIB "libwiringPi.so"

int wiringPiISR(int pin, int edgeType,  void (*function)(void));
void pinMode(int pin, int mode);
void pullUpDnControl(int pin, int pud);