/**
 * \file enocean_print.c
 * \brief Functions utils to print an Enocean_packet
 * \author Emmanuel Bonin - GreenLeaf
 */

#include "enocean.h"

/**
 * \fn void print_hex(uint8_t *buffer, int len)
 * \param buffer The buffer to be printed
 * \param len The length of the buffer
 *
 * \brief Dumps buffer on len bytes in hexadecimal format
 */
void print_hex(uint8_t *buffer, int len)
{
  int i = 0;

  while (i < len)
    {
      printf("%02X ", buffer[i]);
      i++;
    }
}

/**
 * \fn void print_packet(Enocean_packet packet)
 * \param packet The Enocean_packet to format and print
 *
 * \brief Displays a paquet
 */
void print_packet(Enocean_packet packet)
{
  int i;

  printf("========= PACKET ========\n");
  printf("%02X: Synchronization byte\n", packet.sync_byte);
  printf("%04X: Data length\n", packet.header.data_length);
  printf("%02X: Optional data length\n", packet.header.opt_data_length);
  printf("%02X: Packet type\n", packet.header.packet_type);
  printf("%02X: CRC8H\n", packet.CRC8H);
  i = 0;
  while (i < packet.header.data_length)
    {
      printf("%02X ", packet.data[i]);
      i++;
    }
  printf(": Data\n");
  i = 0;
  while (i < packet.header.opt_data_length)
    {
      printf("%02X ", packet.opt_data[i]);
      i++;
    }
  printf(": Optional data\n");
  printf("%02X: CRC8D\n", packet.CRC8D);
  printf("==========================\n\n");
}
