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

LDFLAGS += -lpthread -lconfig

CC = gcc -o

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
