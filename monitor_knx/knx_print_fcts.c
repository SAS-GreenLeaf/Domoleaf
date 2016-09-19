/**
 * \file knx_print_fcts.c
 * \brief Util function for printing KNX datas
 * \author Emmanuel Bonin - GreenLeaf
 */

#include "knx.h"

/**
 * \fn void print_bits(uint32_t c, int nb_bits)
 * \param c The byte of which print bits
 * \param nb_bits The number of bits to print
 *
 * \brief Displays "nb_bits" of an uint
 */
void print_bits(uint32_t c, int nb_bits)
{
  int i;

  i = nb_bits - 1;
  printf("%d: ", c);
  while (i >= 0)
    {
      printf("%d", (c >> i) % 2 ? 1 : 0);
      i--;
    }
  printf("\n");
}

/**
 * \fn void print_buf(uint8_t buff[4096])
 * \param buff The buffer from which display its bits
 *
 * \brief Displays all bits from a buffer
 */
void print_buf(uint8_t buff[4096])
{
  uint8_t  control;
  uint16_t src_addr;
  uint8_t a = 0, b = 0, c = 0;

  memcpy(&control, &buff[0], sizeof(control));
  memcpy(&src_addr, &buff[7], sizeof(src_addr));
  printf("src_addr: ");
  print_bits(src_addr, 16);
  printf("src_addr: ");
  print_bits(src_addr, 16);
  a = (src_addr >> 12) & 0x1f;
  b = (src_addr >> 8) & 0x07;
  c = (src_addr & 0xff);
  printf("a: ");
  print_bits(a, 4);
  printf("b: ");
  print_bits(b, 4);
  printf("c: ");
  print_bits(c, 8);
  printf("%d: %d.%d.%d\n", src_addr, a, b, c);
}

/**
 * \fn void print_buf_hex(void *buf, uint32_t len)
 * \param buf The buffer from which display its bytes
 * \param len The length of the buffer to print
 *
 * \brief Displays len bytes from a buf in hexadecimal
 */
void print_buf_hex(void *buf, uint32_t len)
{
  uint8_t *tmp = buf;
  uint32_t i;

  i = 0;
  while (i < len)
    {
      printf("\\x%02x", tmp[i]);
      i++;
    }
  printf("\n");
}
