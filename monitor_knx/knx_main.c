/**
 * \file knx_main.c
 * \brief The main file for the monitor KNX
 * \author Emmanuel Bonin - Greenleaf
 */

#include "knx.h"

/**
 * \brief Counter for the number of sent packet
 */
int sent = 0;

/**
 * \brief Global socket
 */
int g_sock;

/**
 * \fn uint8_t get_data_len(uint8_t byte)
 * \param byte The byte from which extract the 4 bits value of the data length
 * \return The data length (4 bits) stored on a byte
 *
 * \brief Extracts data length (4 bits) from a byte (8 bits)
 */
uint8_t get_data_len(uint8_t byte)
{
  uint8_t lg = 0;

  byte <<= 4;
  lg = byte >> 4;
  return (lg + 1);
}

/**
 * \fn int connect_to_slave()
 * \return The file descriptor of the open socket for communication
 *
 * \brief Connects to slave daemon on localhost by socket
 */
int connect_to_slave()
{
  int    sock;
  int    on = 1;
  struct protoent *proto;
  struct sockaddr_in addr_in;

  printf("Getting proto...\n");
  if ((proto = getprotobyname("TCP")) == NULL)
    {
      perror("getprotobyname");
      return (-1);
    }
  printf("Done.\nCreating socket...");
  if ((sock = socket(AF_INET, SOCK_STREAM, proto->p_proto)) == -1)
    {
      perror("creating socket to slave");
      return (-1);
    }
  printf("Done creating socket\n");
  setsockopt(sock, IPPROTO_TCP, TCP_NODELAY, (const char *)&on, sizeof(int));
  addr_in.sin_family = AF_INET;
  addr_in.sin_port = htons(4244);
  addr_in.sin_addr.s_addr = inet_addr("127.0.0.1");
  printf("Attempting to connect socket...\n");
  if (connect(sock, (const struct sockaddr *) &addr_in, sizeof(struct sockaddr)) == -1)
    {
      perror("Connection to slave");
      close(sock);
      return (-1);
    }
  printf("Done connecting socket\n");
  return (sock);
}

/**
 * \fn void send_telegram_to_slave(int sock, Telegram *telegram)
 * \param sock File descriptor of the socket
 * \param telegram The telegram to send
 *
 * \brief Writes a KNX telegram to the slave daemon
 */
void send_telegram_to_slave(int sock, Telegram *telegram)
{
  printf("Sending %d generated Telegram struct to slave...\n", sent++);
  if (write(sock, telegram, sizeof(*telegram)) <= 0)
    {
      perror("Sending telegram");
      close(sock);
      return ;
    }
  printf("Done.\n");
}

/**
 * \fn void handle_keyboard(int signum)
 * \param signum Signal number (unused here)
 *
 * \brief Catches CTRL-C to close the socket cleanly and exit the program
 */
void handle_keyboard(int __attribute__((unused)) signum)
{
  close(g_sock);
  exit(0);
}

/**
 * \fn void *vbusmonitor_thread(void *data)
 * \param data The data to use with the thread. Here a Slave structure
 * \return Status code of the execution of the function. (void *) 0 if OK
 *
 * \brief vbusmonitor thread
 *
 * Listen the KNX bus and send all trames to the slave daemon
 */
void *vbusmonitor_thread(void *data)
{
  Slave *slave;
  int len;
  uint16_t src_addr = 0;
  uint16_t dst_addr = 0;

  slave = (Slave *) data;
  if (EIBOpenVBusmonitor(slave->conn) == -1)
    {
      perror("EIBOpenVBusmonitor");
      return (void *) 1;
    }
  while (1)
    {
      /* signal(2, &handle_keyboard); */
      memset(slave->buff, 0, sizeof(slave->buff));
      memset(&slave->telegram, 0, sizeof(slave->telegram));
      if ((len = EIBGetBusmonitorPacket(slave->conn, sizeof(slave->buff), &slave->buff[0])) == -1)
	{
	  perror("getbusmonitorpacket");
	  return (void *) 1;
	}
      slave->telegram.control = slave->buff[0];
      memcpy(&src_addr, &slave->buff[1], 2);
      memcpy(&dst_addr, &slave->buff[3], 2);
      slave->telegram.src_addr = src_addr;
      slave->telegram.dst_addr = dst_addr;
      slave->telegram.data_length = get_data_len(slave->buff[5]);
      memcpy(&slave->telegram.data[0], &slave->buff[6], slave->telegram.data_length);
      slave->sock_fd = connect_to_slave();
      if (slave->sock_fd > 0)
	{
	  if (slave->buff[0] == 0xBC)
	    {
	      send_telegram_to_slave(slave->sock_fd, &slave->telegram);
	      printf("Data of %d long\n", slave->telegram.data_length);
	      print_buf_hex(&slave->telegram.data, slave->telegram.data_length);
	    }
	  close(slave->sock_fd);
	}
    }
  return ((void *) 0);
}

/**
 * \fn int vbusmonitor(EIBConnection *conn)
 * \param conn The EIBConnection to communicate with KNX bus
 * \return Status code of the execution of the function. 0 if OK, -1 on error
 *
 * \brief Creates a thread for vbusmonitor
 */
int vbusmonitor(EIBConnection *conn)
{
  pthread_t slave_thread;
  Slave slave;

  slave.conn = conn;
  printf("[ vbusmonitor ]: Creating thread for vbusmonitor...\n");
  while (1)
    {
      if ((pthread_create(&slave_thread, NULL, vbusmonitor_thread, &slave)) != 0)
	{
	  perror("pthread_create");
	  return (-1);
	}
      printf("[ vbusmonitor ]: Thread created. Joining...\n");
      if ((pthread_join(slave_thread, NULL)) != 0)
	{
	  perror("pthread_join");
	  return (-1);
	}
    }
  return (0);
}

/**
 * \fn int main(int __attribute__((unused)) argc,
 *              char __attribute__((unused)) *argv[])
 * \param argc The number of arguments the program received
 * \param argv The arguments the program received
 * \return
 *
 * To start the program as a daemon, add the --daemon argument.
 * You can also specify the path of the KNX device as second argument.
 */
int main(int __attribute__((unused)) argc,
         char __attribute__((unused)) *argv[])
{
  int  pid;
  FILE *pid_file;
  EIBConnection *conn;

  if (argc > 2 && strcmp(argv[2], "--daemon") == 0)
    {
      if ((pid = fork()) == -1)
	{
	  fprintf(stderr, "[ KNX ]: Error while starting daemon\n");
	  return (1);
	}
      if (pid == 0)
	{
	  check_args(argc, argv);
	  if ((conn = EIBSocketURL(argv[1])) == NULL)
	    {
	      perror("EIBSocketURL");
	      return (1);
	    }
	  vbusmonitor(conn);
	  EIBClose(conn);
	}
      else
	{
	  if ((pid_file = fopen("/var/run/monitor_knx.pid", "w")) == NULL)
	    {
	      fprintf(stderr, "[ KNX ]: Error while attempting to create pid file\n");
	      return (1);
	    }
	  fprintf(pid_file, "%d", pid);
	  fclose(pid_file);
	}
    }
  else
    {
      check_args(argc, argv);
      if ((conn = EIBSocketURL(argv[1])) == NULL)
	{
	  perror("EIBSocketURL");
	  return (1);
	}
      vbusmonitor(conn);
      EIBClose(conn);
    }

  return 0;
}
