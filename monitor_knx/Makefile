SRC = knx_main.c \
      knx_address.c \
      knx_check_args.c \
      knx_print_fcts.c \

NAME = monitor_knx
NATIVE := $(shell dpkg --print-architecture)

OBJ = $(SRC:.c=.o)

CFLAGS += -W -Wall -Wextra $(IFLAGS) $(LDFLAGS) -ggdb -g3
IFLAGS += -I. -I/usr/local/include
LDFLAGS += -leibclient -lpthread

ifeq ($(ARCH), $(NATIVE))
	CC = gcc -o
endif
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
