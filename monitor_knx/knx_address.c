/**
 * \file knx_address.c
 * \brief Utils function to manage physical and virtual KNX addresses
 * \author Emmanuel Bonin - Greenleaf
 */

#include "knx.h"

/**
 * \fn uint16_t readgaddr(char *str)
 * \param str logical KNX address to be converted to an uint16
 * \return The uint16 containing the logical KNX address
 *
 * \brief Converts a KNX logical address from a string of the form x/x/x to an uint16
 */
uint16_t readgaddr(char *str)
{
  uint32_t a = 0;
  uint32_t b = 0;
  uint32_t c = 0;

  sscanf(str, "%d/%d/%d", &a, &b, &c);
  return ((a & 0x1f) << 11 | (b & 0x07) << 8 | (c & 0xff));
}

/**
 * \fn uint32_t readHex (const char *addr)
 * \param addr Hexadecimal form of a number to be converted
 * \return uint32 containing the integer version of an hexadecimal number
 *
 * \brief Converts a number from hex to uint32
 */
uint32_t readHex (const char *addr)
{
  int i;
  sscanf (addr, "%x", &i);
  return i;
}

/**
 * \fn char *group2string(eibaddr_t addr)
 * \param addr KNX logical address stored in an integer to be converted in a string of the form x/x/x
 * \return The string containing the x/x/x form of the EIB address
 *
 * \brief Converts a KNX logical address to a string
 */
char *group2string(eibaddr_t addr)
{
  char *res;

  res = malloc(10);
  memset(&res[0], 0, 10);
  sprintf(res, "%d/%d/%d", (addr >> 11) & 0x1f, (addr >> 8) & 0x07, addr & 0xff);
  return (res);
}


/**
 * \fn void printHex (int len, uint8_t * data)
 * \param len The length of the buffer to print
 * \param data The bytes to print
 *
 * \brief Displays a buffer in hexadecimal
 */
void printHex (int len, uint8_t * data)
{
  int i;
  for (i = 0; i < len; i++)
    {
      printf ("%02X ", data[i]);
    }
  printf("\n");
}

/**
 * \fn uint16_t readaddr(char *str)
 * \param str Converts a string of the form x.x.x containing a physical KNX address to a uint16
 * \return The uint16 containing the physical address
 *
 * \brief Converts a KNX physical address to an uint16
 */
uint16_t readaddr(char *str)
{
  uint32_t	a = 0;
  uint32_t	b = 0;
  uint32_t	c = 0;

  sscanf(str, "%d.%d.%d", &a, &b, &c);
  return ((a & 0x0f) << 12 | (b & 0x0f) << 8 | (c & 0xff));
}

/**
 * \fn char *individual2string(uint16_t addr)
 * \param addr The uint16 containing the physical address
 * \return A string of the form x.x.x
 *
 * \brief Converts a physical address from an uint16 to a string of the form x.x.x
 */
char *individual2string(uint16_t addr)
{
  char *res;

  res = malloc(10);
  memset(&res[0], 0, 10);
  sprintf(res, "%d.%d.%d", (addr >> 12) & 0x0f, (addr >> 8) & 0x0f, (addr) & 0xff);
  return (res);
}
