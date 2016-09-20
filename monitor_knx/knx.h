/**
 * \file knx.h
 * \brief Monitor KNX
 * \author Emmanuel Bonin - GreenLeaf
 */

#ifndef  __KNX_H__
# define __KNX_H__

# include <stdio.h>
# include <stdlib.h>
# include <time.h>

# include <sys/select.h>
# include <sys/time.h>
# include <sys/types.h>
# include <unistd.h>

# include <pthread.h>
# include <signal.h>
# include <netdb.h>
# include <arpa/inet.h>
# include <stdint.h>
# include <string.h>
# include <netinet/tcp.h>

# include "eibclient.h"

/**
 * \def MAX_GROUP_VALUE_LEN
 * \brief Maximum length of the value of a group data
 */
# define MAX_GROUP_VALUE_LEN  14

/**
 * \def DATAGRAM_BUFFER_SIZE
 * \brief The size of a telegram
 */
# define DATAGRAM_BUFFER_SIZE 128

/**
 * \def READ_VAL
 * \brief The value corresponding to a READ packet
 */
# define READ_VAL 0x0

/**
 * \def RESP_VAL
 * \brief The value corresponding to a RESP packet
 */
# define RESP_VAL 0x40

/**
 * \def WRIT_VAL
 * \brief The value corresponding to a WRITE packet
 */
# define WRIT_VAL 0x80


/**
 * \struct s_telegram
 * \brief KNX telegram structure
 */
typedef struct __attribute__((packed)) s_telegram
{
  uint8_t  control;	/*!< Control byte of the telegram */
  uint16_t src_addr;	/*!< Source address */
  uint16_t dst_addr;	/*!< Destination address */
  uint8_t  data_length;	/*!< The length of the data */
  uint8_t  data[255];	/*!< The data of the telegram */
} Telegram;		/*!< Telegram */

/**
 * \struct s_slave
 * \brief Connection to a slave daemon
 */
typedef struct __attribute__((packed)) s_slave
{
  uint8_t  buff[255];	/*!< Buffer for communication */
  Telegram telegram;	/*!< Telegram structure */
  EIBConnection *conn;	/*!< Connection with EIB client */
  int      sock_fd;	/*!< File descriptor of the slave socket */
} Slave;		/*!< Slave */

/**
 * \def g_sock
 * \brief Global socket (global to catch CTRL-C and close it)
 */
extern int g_sock;

/* knx_address.c */
uint16_t readgaddr(char *addr);
uint32_t readHex(const char *addr);
char     *group2string(eibaddr_t addr);
char     *individual2string(eibaddr_t addr);
void     printHex(int len, uint8_t *data);
uint16_t readaddr(char *addr);

/* knx_check_args.c */
void check_args(int argc, char *argv[]);

/* knx_main.c */
uint8_t get_data_len(uint8_t byte);
int     connect_to_slave(void);
void    send_telegram_to_slave(int sock, Telegram *telegram);
void    handle_keyboard(int signum);
int     vbusmonitor(EIBConnection *conn);

/* knx_print_fcts.c */
void print_bits(uint32_t value, int nb_bits);
void print_buf(uint8_t buff[4096]);
void print_buf_hex(void *buf, uint32_t len);

#endif
