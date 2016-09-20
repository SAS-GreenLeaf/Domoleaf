/**
 * \file enocean.h
 * \brief Monitor EnOcean
 * \author Emmanuel Bonin - GreenLeaf
 */

#ifndef  __ENOCEAN_H__
# define __ENOCEAN_H__
# define _DEFAULT_SOURCE

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
# include <netinet/tcp.h>

/**
 * \enum e_log_type
 * \brief Different log types
 */
typedef enum e_log_type
  {
    LOG_DEBUG = 0x00,	/*!< Debugging logs */
    LOG_INFO  = 0x01,	/*!< Info logs */
    LOG_ERROR = 0x02	/*!< Error logs */
  } Log_type;		/*!< Log_type */

/**
 * \struct s_args
 * \brief Structure containing information used on the program launch
 */
typedef struct s_args
{
  int  daemon;		/*!< Flag at 1 if the program should be started as a daemon, 0 else */
  char *device;		/*!< Name of the device */
} t_args;		/*!< t_args */

/**
 * \struct s_packet_header
 * \brief EnOcean trame header
 *
 * Structure describing the header for an EnOcean packet.
 */
typedef struct __attribute__((packed)) s_packet_header
{
  uint16_t data_length;		/*!< The length of the data */
  uint8_t  opt_data_length;	/*!< The length of the optional data */
  uint8_t  packet_type;		/*!< The type of the packet */
} Enocean_packet_header;	/*!< Enocean_packet_header */

/**
 * \struct s_packet
 * \brief EnOcean generic trame
 */
typedef struct __attribute__((packed)) s_packet
{
  uint8_t               sync_byte;	/*!< Synchronisation byte */
  Enocean_packet_header header;		/*!< Header of the Enocean_packet */
  uint8_t               CRC8H;		/*!< First byte for control */
  uint8_t               *data;		/*!< Data of the packet */
  uint8_t               opt_data[255];	/*!< Optionnal data of the packet */
  uint8_t               CRC8D;		/*!< Second byte for control */
} Enocean_packet;			/*!< Enocean_packet */

/**
 * \enum e_event_code
 * \brief Different event codes
 */
typedef enum e_event_code
  {
    SA_RECLAIM_NOT_SUCCESSFUL = 1,
    SA_CONFIRM_LEARN          = 2,
    SA_LEARN_ACK              = 3,
    CO_READY	              = 4,
    CO_EVENT_SECURE_DEVICES   = 5,
    CO_DUTYCYCLE_LIMIT        = 6
  } Event_code;				/*!< Event_code */

/**
 * \enum e_common_command_code
 * \brief Differents common command code
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
  } Common_command_code;		/*!< Common_command_code */

/**
 * \enum e_smart_ack_command_code
 * \brief smart_ack_command codes
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
  } Smart_ack_command_code;	/*!< Smart_ack_command_code */

/**
 * \enum e_packet_type
 * \brief EnOcean trame types
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
  } Packet_type;		/*!< Packet_type */

/**
 * \brief Function pointer used to call a different function by trame type
 */
typedef void (*Pack_func)(Enocean_packet *);

/**
 * \struct s_packet_type_func
 * \brief Trame type and called function
 */
typedef struct __attribute__((packed)) s_packet_type_func
{
  Packet_type type;	/*!< The type of packet received */
  Pack_func   function; /*!< The callback called depending on the packet type */
} Packet_function;	/*!< Packet_function */

/**
 * \struct s_slave
 * \brief Slave daemon connection
 */
typedef struct __attribute__((packed)) s_slave
{
  int    sock_fd;		/*!< File descriptor of the socket to communicate with the slave */
  struct protoent *proto;	/*!< Prototype */
  struct sockaddr_in addr_in;	/*!< IP address of the slave */
} Slave;			/*!< Slave */


/* enocean_packet_functions.c */
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
void *treat_packet(void *data);
Slave *slave_init();
void  slave_delete(Slave *slave);
int   slave_send_data(Slave *slave, void *data, int len);

/* enocean_print.c */
void print_hex(uint8_t *buffer, int len);
void print_packet(Enocean_packet packet);

/* enocean_xfunctions.c */
int xtcgetattr(int fd, struct termios *t);
int xtcsetattr(int fd, struct termios *t);

/* enocean_init.c */
int init(const char *dev_name, struct termios *options, struct termios *backup);
int init_listen_slave_socket(const char *ip, uint16_t port);
char *get_interface_enocean();
/* char *strcpy_to_n(char *dest, const char *src, int n); */

/* enocean_log.c */
void enocean_log(Log_type type, const char *msg);
void enocean_log_packet(const Enocean_packet packet);

#endif
