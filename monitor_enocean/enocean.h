#ifndef  __ENOCEAN_H__
# define __ENOCEAN_H__
# define _DEFAULT_SOURCE

# include <libconfig.h>
# include <signal.h>
# include <netdb.h>
# include <pthread.h>
# include <sys/select.h>
# include <sys/time.h>
# include <sys/types.h>
# include <arpa/inet.h>
# include <string.h>
# include <unistd.h>
# include <fcntl.h>
# include <termios.h>
# include <time.h>
# include <stdint.h>
# include <stdio.h>
# include <stdlib.h>

# define CONF_FILENAME   "/etc/domoleaf/monitor_enocean.cfg"
# define CONF_PORT_ENTRY "port"
# define CONF_ADDR_ENTRY "address"

/*
** Different log types
*/
typedef enum e_log_type
{
	LOG_DEBUG = 0x00,
	LOG_INFO  = 0x01,
	LOG_ERROR = 0x02
} Log_type;

/*
** Structure containing information used on the program launch
*/
typedef struct s_args
{
	int  daemon;
	char *device;
} t_args;

/*
** EnOcean trame header
*/
typedef struct __attribute__((packed)) s_packet_header
{
	uint16_t data_length;
	uint8_t  opt_data_length;
	uint8_t  packet_type;
} Enocean_packet_header;

/*
** EnOcean generic trame
*/
typedef struct __attribute__((packed)) s_packet
{
	uint8_t               sync_byte;
	Enocean_packet_header header;
	uint8_t               CRC8H;
	uint8_t               data[255];
	uint8_t               opt_data[255];
	uint8_t               CRC8D;
} Enocean_packet;

/*
** events codes
*/
typedef enum e_event_code
{
	SA_RECLAIM_NOT_SUCCESSFUL = 1,
	SA_CONFIRM_LEARN          = 2,
	SA_LEARN_ACK              = 3,
	CO_READY	              = 4,
	CO_EVENT_SECURE_DEVICES   = 5,
	CO_DUTYCYCLE_LIMIT        = 6
} Event_code;

/*
** common_command codes
*/
typedef enum e_common_command_code
{
	CO_WR_SLEEP                   = 1,
	CO_WR_RESET                   = 2,
	CO_RD_VERSION                 = 3,
	CO_RD_SYS_LOG                 = 4,
	CO_WR_SYS_LOG                 = 5,
	CO_WR_BIST                    = 6,
	CO_WR_IDBASE                  = 7,
	CO_RD_IDBASE                  = 8,
	CO_WR_REPEATER                = 9,
	CO_RD_REPEATER                = 10,
	CO_WR_FILTER_ADD              = 11,
	CO_WR_FILTER_DEL              = 12,
	CO_WR_FILTER_DEL_ALL          = 13,
	CO_WR_FILTER_ENABLE           = 14,
	CO_RD_FILTER                  = 15,
	CO_WR_WAIT_MATURITY	          = 16,
	CO_WR_SUBTEL                  = 17,
	CO_WR_MEM                     = 18,
	CO_RD_MEM                     = 19,
	CO_RD_MEM_ADDRESS             = 20,
	CO_RD_SECURITY                = 21,
	CO_WR_SECURITY                = 22,
	CO_WR_LEARNMODE               = 23,
	CO_RD_LEARNMODE               = 24,
	CO_WR_SECUREDEVICE_ADD        = 25,
	CO_WR_SECUREDEVICE_DEL        = 26,
	CO_RD_SECUREDEVICE_BY_INDEX   = 27,
	CO_WR_MODE                    = 28,
	CO_RD_NUMSECUREDEVICES        = 29,
	CO_RD_SECUREDEVICE_BY_ID      = 30,
	CO_WR_SECUREDEVICE_ADD_PSK    = 31,
	CO_WR_SECUREDEVICE_SENDTEACHIN= 32,
	CO_WR_TEMPORARY_RLC_WINDOW    = 33,
	CO_RD_SECUREDEVICE_PSK        = 34,
	CO_RD_DUTYCYLE_LIMIT          = 35
  } Common_command_code;

/*
** smart_ack_command codes
*/
typedef enum e_smart_ack_command_code
{
	SA_WR_LEARNMODE     = 1,
	SA_RD_LEARNMODE     = 2,
	SA_WR_LEARNCONFIRM  = 3,
	SA_WR_CLIENTLEARNRQ = 4,
	SA_WR_RESET         = 5,
	SA_RD_LEARNEDCLIENTS= 6,
	SA_WR_RECLAIMS      = 7,
	SA_WR_POSTMASTER    = 8
} Smart_ack_command_code;

/*
** EnOcean trame types
*/
typedef enum e_packet_type
{
	RADIO_ERP1        = 0x01,
	RESPONSE          = 0x02,
	RADIO_SUB_TEL     = 0x03,
	EVENT             = 0x04,
	COMMON_COMMAND    = 0x05,
	SMART_ACK_COMMAND = 0x06,
	REMOTE_MAN_COMMAND= 0x07,
	RADIO_MESSAGE     = 0x09,
	RADIO_ERP2        = 0x0A
} Packet_type;

/*
** Pointer on function, used to call a different function by trame type
*/
typedef void (*Pack_func)(Enocean_packet *);

/*
** Trame type and called function
*/
typedef struct __attribute__((packed)) s_packet_type_func
{
	Packet_type type;
	Pack_func   function;
} Packet_function;

/*
** Slave daemon connection
*/
typedef struct __attribute__((packed)) s_slave
{
	int    sock_fd;
	struct protoent *proto;
	struct sockaddr_in addr_in;
} Slave;

/* slave.c */
Slave *slave_init();
void  slave_delete(Slave *slave);
int   slave_send_data(Slave *slave, void *data, int len);

/* packet_functions.c */
void radio_erp1(Enocean_packet __attribute__((unused)) *packet);
void response(Enocean_packet __attribute__((unused)) *packet);
void radio_sub_tel(Enocean_packet __attribute__((unused)) *packet);
void event(Enocean_packet __attribute__((unused)) *packet);
void common_command(Enocean_packet __attribute__((unused)) *packet);
void smart_ack_command(Enocean_packet __attribute__((unused)) *packet);
void remote_man_command(Enocean_packet __attribute__((unused)) *packet);
void radio_message(Enocean_packet __attribute__((unused)) *packet);
void radio_erp2(Enocean_packet __attribute__((unused)) *packet);
Enocean_packet create_packet(uint8_t *buffer, uint16_t data_len, uint8_t opt_data_len, uint8_t packet_type);
void *thread_treat_packet(Enocean_packet *packet);
void *tread_packet(void *data);

/* print.c */
void print_hex(uint8_t *buffer, int len);
void print_packet(Enocean_packet packet);

/* xfunctions.c */
int xtcgetattr(int fd, struct termios *t);
int xtcsetattr(int fd, struct termios *t);

/* init.c */
int init(const char *dev_name, struct termios *options, struct termios *backup);
int init_listen_slave_socket(const char *ip, uint16_t port);
char *get_interface_enocean();

/* enocean_log.c */
void enocean_log(Log_type type, const char *msg);
void enocean_log_packet(const Enocean_packet packet);

#endif
