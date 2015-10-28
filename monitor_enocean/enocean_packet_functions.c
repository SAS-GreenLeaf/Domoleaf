#include "enocean.h"

/*
** Pointer array used to call a different function by packet type
*/
Packet_function g_packet_function[] =
{
	{RADIO_ERP1, &radio_erp1},
	{RESPONSE, &response},
	{RADIO_SUB_TEL, &radio_sub_tel},
	{EVENT, &event},
	{COMMON_COMMAND, &common_command},
	{SMART_ACK_COMMAND, &smart_ack_command},
	{REMOTE_MAN_COMMAND, &remote_man_command},
	{RADIO_MESSAGE, &radio_message},
	{RADIO_ERP2, &radio_erp2},
};

/*
** Open, read and store configuration's information
*/
int get_enocean_conf(int *port, char **addr)
{
	FILE *file;
	char line[128];
	char *tmp;

	if ((file = fopen("/etc/domoleaf/slave.conf", "r")) == NULL)
	{
		fprintf(stderr, "Error for open /etc/domoleaf/slave.conf\n");
		return (-1);
	}
	while (fgets(line, 128, file) != NULL)
	{
		if (strncmp(line, "[enocean]", 9) == 0)
		{
			while (fgets(line, 128, file) != NULL)
			{
				if (strncmp(line, "port = ", 7) == 0)
				{
					if (strlen(line) > 8)
					{
						tmp = malloc((sizeof(char) * strlen(line)) - 6);
						memset(tmp, '\0', strlen(line) - 6);
						strcpy_to_n(tmp, line, 7);
						*port = atoi(tmp);
						free(tmp);
						*addr = "127.0.0.1";
						fclose(file);
						return (0);
					}
				}
			}
		}
	}
	fclose(file);
	fprintf(stderr, "Error while reading /etc/domoleaf/slave.conf\n");
	return (-1);
}

/*
** Init connection to slave's daemon
*/
Slave *slave_init()
{
	Slave *slave;
	char  *addr;
	int    port;

	addr = malloc(32);
	memset(addr, 0, 32);
	if(get_enocean_conf(&port, &addr) == -1)
	{
		return (NULL);
	}

	if((slave = malloc(sizeof(*slave))) == NULL)
	{
		perror("malloc");
		return (NULL);
	}
	if((slave->proto = getprotobyname("TCP")) == NULL)
	{
		perror("getprotobyname");
		return (NULL);
	}
	if((slave->sock_fd = socket(AF_INET, SOCK_STREAM, slave->proto->p_proto)) == -1)
	{
		perror("socket");
		return (NULL);
	}

	slave->addr_in.sin_family = AF_INET;
	slave->addr_in.sin_port = htons(port);
	slave->addr_in.sin_addr.s_addr = inet_addr(addr);
	free(addr);

	if(connect(slave->sock_fd, (const struct sockaddr *) &slave->addr_in, sizeof(struct sockaddr)) == -1)
	{
		perror("Connection to slave");
		slave_delete(slave);
		return (NULL);
	}

	return (slave);
}

/*
** Close and free ressources used by slave connection
*/
void slave_delete(Slave *slave)
{
	close(slave->sock_fd);
	free(slave);
}

/*
** Write datas on slave's socket
*/
int slave_send_data(Slave *slave, void *data, int size)
{
	if (write(slave->sock_fd, data, size) == -1)
	{
		perror("Sending data to slave");
		return (-1);
	}
	return (0);
}

/*
** Dump an Radio ERP1 EnOcean packet
*/
static void dump_radio_erp1(Enocean_packet __attribute__((unused)) *packet)
{
	printf("RADIO_ERP1\n");
	printf("0x%02X      : Sync_byte\n", packet->sync_byte);
	printf("%02X %02X     : Data length\n", packet->header.data_length >> 4, packet->header.data_length);
	printf("0x%02X      : Optional data length\n", packet->header.opt_data_length);
	printf("0x%02X      : Packet type\n", packet->header.packet_type);
	printf("0x%02X      : CRC8H\n", packet->CRC8H);
	printf("0x%02X      : R-ORG\n", packet->data[0]);
	printf("0x%02X      : Value\n", packet->data[1]);
	printf("0x%08X: Sender ID\n", (uint32_t) packet->data[2]);
	printf("0x%02X      : Status\n", packet->data[6]);
	printf("0x%02X      : SubTelNum\n", packet->opt_data[0]);
	printf("0x%08X: Destination ID\n", (uint32_t) packet->opt_data[1]);
	printf("0x%02X      : dBm\n", packet->opt_data[5]);
	printf("0x%02X      : SecurityLevel\n", packet->opt_data[6]);
	printf("0x%02X      : CRC8D\n", packet->CRC8D);
	printf("Initializing slave...\n");
}

/*
** All theses function are pointer array's callbacks
** Only ERP1 is available, for others : TODO
*/
void radio_erp1(Enocean_packet __attribute__((unused)) *packet)
{
	Slave	 *slave;

	dump_radio_erp1(packet);

	slave = slave_init();
	printf("Slave initialized successfully\n");
	if (slave)
	{
		printf("Sending datas to slave...\n");
		slave_send_data(slave, (void *) packet, sizeof(*packet));
		printf("Data sent successfully\n");
		slave_delete(slave);
	}
}

void response(Enocean_packet __attribute__((unused)) *packet)
{
	printf("%s\n", __FUNCTION__);
}

void radio_sub_tel(Enocean_packet __attribute__((unused)) *packet)
{
	printf("%s\n", __FUNCTION__);
}

void event(Enocean_packet __attribute__((unused)) *packet)
{
	printf("%s\n", __FUNCTION__);
}

void common_command(Enocean_packet __attribute__((unused)) *packet)
{
	printf("%s\n", __FUNCTION__);
}

void smart_ack_command(Enocean_packet __attribute__((unused)) *packet)
{
	printf("%s\n", __FUNCTION__);
}

void remote_man_command(Enocean_packet __attribute__((unused)) *packet)
{
	printf("%s\n", __FUNCTION__);
}

void radio_message(Enocean_packet __attribute__((unused)) *packet)
{
	printf("%s\n", __FUNCTION__);
}

void radio_erp2(Enocean_packet __attribute__((unused)) *packet)
{
	printf("%s\n", __FUNCTION__);
}
/*
** Callbacks end
*/

/*
** Put read buffer's information into Enocean_packet structure
*/
Enocean_packet create_packet(uint8_t *buffer, uint16_t data_len, uint8_t opt_data_len, uint8_t packet_type)
{
	Enocean_packet res;

	memset(&res, 0, sizeof(res));
	res.sync_byte = buffer[0];
	res.header.data_length = data_len;
	res.header.opt_data_length = opt_data_len;
	res.header.packet_type = packet_type;
	res.CRC8H = buffer[5];
	memcpy(&res.data[0], &buffer[6], res.header.data_length);
	memcpy(&res.opt_data[0], &buffer[6 + res.header.data_length], res.header.opt_data_length);
	res.CRC8D = buffer[6 + res.header.data_length + res.header.opt_data_length];

	return (res);
}

/*
** Call function and callback relative to its "Type"
*/
void *treat_packet(void *data)
{
	int i=0, length;
	Enocean_packet	*packet;

	length = sizeof(g_packet_function);
	packet = (Enocean_packet *) data;

	while (i < length)
	{
		if (packet->header.packet_type == g_packet_function[i].type)
		{
			g_packet_function[i].function(packet);
			return ((void *) 0);
		}
		i++;
	}

	return ((void *) -1);
}

/*
** Launch 2nd thread who get and treat packets
*/
void *thread_treat_packet(Enocean_packet *packet)
{
	pthread_t thread;
	void *retval;

	retval = malloc(4);
	printf("\nCreating thread to send data to slave\n");

	if (pthread_create(&thread, NULL, &treat_packet, packet) != 0)
	{
		fprintf(stderr, "[ MONITOR ENOCEAN ]: Error while creating thread for treating trame\n");
		return ((void *) -1);
	}

	if (pthread_join(thread, &retval) != 0)
	{
		fprintf(stderr, "[ MONITOR ENOCEAN ]: Error while joining thread.\n");
		return ((void *) -1);
	}

	printf("Exiting thread, data should now be sent\n");

	return (retval);
}
