SRC = knx_main.c \
      knx_address.c \
      knx_check_args.c \
      knx_print_fcts.c \

NAME = monitor_knx

OBJ = $(SRC:.c=.o)

CFLAGS += -W -Wall -Wextra $(IFLAGS) $(LDFLAGS) -ggdb -g3

IFLAGS += -I. -I/usr/local/include

LDFLAGS += -leibclient -lpthread

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
