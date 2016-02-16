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

# define MAX_GROUP_VALUE_LEN  14
# define DATAGRAM_BUFFER_SIZE 128 /* Telegram buffer size */

# define READ_VAL 0x0	/* READ packet */
# define RESP_VAL 0x40	/* RESPONSE packet */
# define WRIT_VAL 0x80	/* WRITE packet */

/*
** KNX telegram
*/
typedef struct __attribute__((packed)) s_telegram
{
	uint8_t  control;
	uint16_t src_addr;
	uint16_t dst_addr;
	uint8_t  data_length;
	uint8_t  data[255];
} Telegram;

/*
** Connection to a slave daemon
*/
typedef struct __attribute__((packed)) s_slave
{
	uint8_t  buff[255];
	Telegram telegram;
	EIBConnection *conn;
	int      sock_fd;
} Slave;

/*
** Global socket (global to catch CTRL-C and close it)
*/
extern int g_sock;

/* address.c */
uint16_t readgaddr(char *addr);
uint32_t readHex(const char *addr);
char     *group2string(eibaddr_t addr);
char     *individual2string(eibaddr_t addr);
void     printHex(int len, uint8_t *data);
uint16_t readaddr(char *addr);

/* check_args.c */
void check_args(int argc, char *argv[]);

/* main.c */
uint8_t get_data_len(uint8_t byte);
int     groupwrite(EIBConnection *conn, char *argv[]); /* A changer */
int     connect_to_slave(void);
void    send_telegram_to_slave(int sock, Telegram *telegram);
void    handle_keyboard(int signum);
int     vbusmonitor(EIBConnection *conn);

/* print_fcts.c */
void print_bits(uint32_t value, int nb_bits);
void print_buf(uint8_t buff[4096]);
void print_buf_hex(void *buf, uint32_t len);

#endif
