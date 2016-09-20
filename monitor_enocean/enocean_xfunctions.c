/**
 * \file enocean_xfunctions.c
 * \brief Utils functions checking execution of sys calls
 * \author Emmanuel Bonin - GreenLeaf
 */

#include "enocean.h"

/**
 * \fn int xtcgetattr(int fd, struct termios *t)
 * \param fd File descriptor on which get terminal attributes
 * \param t Pointer on structure termios in which store the result
 * \return 0 if OK, 1 on error
 *
 * \brief Gets default terminal's attributes
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

/**
 * \fn int xtcsetattr(int fd, struct termios *t)
 * \param fd File descriptor on which set the terminal attributes
 * \param t Pointer on structure from which restore the terminal attributes
 * \return 0 if OK, 1 on error
 *
 * \brief Reassigns the new attributes to the terminal
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
