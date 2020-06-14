SRC_DIR=./src
DST_DIR=./build
TMP_DIR=./tmp

SRCS = $(wildcard ${SRC_DIR}/*.svg)
COMPILED = $(patsubst %.svg,${DST_DIR}/%.GRAY,$(notdir ${SRCS}))

.PHONY: compile clean

compile:${DST_DIR} ${TMP_DIR} ${COMPILED}

${DST_DIR}:
	mkdir -p ${DST_DIR}

${TMP_DIR}:
	mkdir -p ${TMP_DIR}

$(DST_DIR)/%.GRAY:$(TMP_DIR)/%.png
	convert $< -depth 16 $@

$(TMP_DIR)/%.png:$(SRC_DIR)/%.svg
	rsvg-convert -w 176 -h 264 -f png -o $@ -b white $<

clean:
	rm $(DST_DIR)/*.GRAY