#define FFI_LIB "./epaper/bin/shared/libepaper.so"

void DEV_Module_Init(void);
void DEV_Module_Exit(void);
void EPD_2IN7_Init(void);
void EPD_2IN7_Clear(void);
void EPD_2IN7_Display(const uint8_t *Image);
void EPD_2IN7_Sleep(void);

void EPD_2IN7_Init_4Gray(void);
void EPD_2IN7_4GrayDisplay(const uint8_t *Image);