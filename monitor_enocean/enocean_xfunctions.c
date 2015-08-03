#include "enocean.h"

/*
** Get default terminal's attributes
*/
int xtcgetattr(int fd, struct termios *t)
{
	if (tcgetattr(fd, t) == -1)
	{
		fprintf(stderr, "[ ERROR ]: Trying to get attributes\n");
		return (0);
	}
	return (1);
}

/*
** Reassigns the new attributes to the terminal
*/
int xtcsetattr(int fd, struct termios *t)
{
	t->c_cflag = B57600 | CS8 | CLOCAL | CREAD;
	t->c_iflag = IGNPAR | ICRNL | PARENB;
	t->c_oflag = 0;
	t->c_lflag = 0;
	if (tcsetattr(fd, TCSANOW, t) == -1)
	{
		fprintf(stderr, "[ ERROR ]: Trying to set attributes\n");
		return (0);
	}
	return (1);
}
