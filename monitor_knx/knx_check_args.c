#include "knx.h"

/*
** Check program's arguments
*/
void check_args(int argc, char *argv[])
{
	if (argc < 2)
	{
		fprintf(stderr, "Usage: %s url\n", argv[0]);
		exit(EXIT_FAILURE);
	}
	else if (strncmp(argv[1], "ip:", 3) != 0)
	{
		fprintf(stderr, "url param must be of the form ip:xxx.xxx.xxx.xxx\n");
		exit(EXIT_FAILURE);
	}
	else if (argc == 4)
	{
		sleep(atoi(argv[3]));
	}
}
