#include "enocean.h"

/*
** Init daemon and connection to EnOcean dongle
** Set shell attributs to read input stream
*/
int init(const char *dev_name, struct termios *options, struct termios *backup)
{
	int enocean_dev, flag;

	flag = 0;
	while (!flag)
	{
		flag = 1;
		if ((enocean_dev = open(dev_name, O_RDWR | O_NDELAY)) == -1)
		{
			fprintf(stderr, "[ ERROR in %s at %s:%d ]: Unable to open %s. Maybe permission denied or device missing.\n", __FUNCTION__, __FILE__, __LINE__, dev_name);
			flag = 0;
		}
		if (!xtcgetattr(enocean_dev, options))
		{
			fprintf(stderr, "[ ERROR in %s at %s:%d ]: Unable to tcgetattr\n", __FUNCTION__, __FILE__, __LINE__);
			flag = 0;
		}
		if (!xtcgetattr(enocean_dev, backup))
		{
			fprintf(stderr, "[ ERROR in %s at %s:%d ]: Unable to tcgetattr\n", __FUNCTION__, __FILE__, __LINE__);
			flag = 0;
		}
		if (!xtcsetattr(enocean_dev, options))
		{
			fprintf(stderr, "[ ERROR in %s at %s:%d ]: Unable to tcsetattr\n", __FUNCTION__, __FILE__, __LINE__);
			flag = 0;
		}
		sleep(1);
	}

	return (enocean_dev);
}

/*
** Init socket who listen input connections from slave daemon
*/
int init_listen_slave_socket(const char *ip, uint16_t port)
{
	int    sock;
	struct protoent *proto;
	struct sockaddr_in addr_in;

	if ((proto = getprotobyname("TCP")) == NULL)
	{
		perror("getprotobyname");
		return (-1);
	}
	if ((sock = socket(AF_INET, SOCK_STREAM, proto->p_proto)) == -1)
	{
		perror("socket");
		return (-1);
	}
	addr_in.sin_family = AF_INET;
	addr_in.sin_port = htons(port);
	addr_in.sin_addr.s_addr = inet_addr(ip);
	if (bind(sock, (const struct sockaddr *) &addr_in, sizeof(addr_in)) == -1)
	{
		perror("bind");
		return (-1);
	}
	if (listen(sock, 42) == -1)
	{
		perror("listen");
		return (-1);
	}
	return (sock);
}
