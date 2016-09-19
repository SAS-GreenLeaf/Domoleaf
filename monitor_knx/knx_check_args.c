/**
 * \file knx_check_args.c
 * \brief Function to check the command line arguments of the program
 * \author Emmanuel Bonin - Greenleaf
 */

#include "knx.h"

/**
 * \fn void check_args(int argc, char *argv[])
 * \param argc The argument count received
 * \param argv The argument values received
 *
 * \brief Check program's arguments and exits on error
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
