SRC = enocean_main.c \
      enocean_init.c \
      enocean_packet_functions.c \
      enocean_print.c \
      enocean_log.c \
      enocean_xfunctions.c \

OBJ = $(SRC:.c=.o)

NAME = monitor_enocean


CFLAGS += -W -Wall -Wextra -ansi -pedantic
CFLAGS += $(IFLAGS)
CFLAGS += $(LDFLAGS)
IFLAGS += -I.
LDFLAGS += -lpthread

CC = gcc -o
ifeq ($(ARCH),arm64)
	CC = aarch64-linux-gnu-gcc -o
endif
ifeq ($(ARCH),armel)
	CC = arm-linux-gnueabi-gcc -o
endif
ifeq ($(ARCH),armhf)
	CC = arm-linux-gnueabihf-gcc -o
endif
ifeq ($(ARCH),i386)
	CC = gcc -m32 -o
endif
ifeq ($(ARCH),mips)
	CC = mips-linux-gnueabihf-gcc -o
endif
ifeq ($(ARCH),mipsel)
	CC = mipsel-linux-gnueabihf-gcc -o
endif
ifeq ($(ARCH),powerpc)
	CC = powerpc-linux-gnu-gcc -o
endif
ifeq ($(ARCH),ppc64el)
	CC = powerpc64le-linux-gnueabihf-gcc -o
endif

RM = rm -f

$(NAME) : $(OBJ)
		$(CC) $(NAME) $(OBJ) $(CFLAGS)

all : $(NAME)

clean :
		$(RM) $(OBJ)

fclean : clean
		$(RM) $(NAME)

re : fclean all

.PHONY : all clean fclean re
