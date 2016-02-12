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
	int    on = 1;
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
	setsockopt(sock, IPPROTO_TCP, TCP_NODELAY, (const char *)&on, sizeof(int));
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

char *strcpy_to_n(char *dest, const char *src, int n)
{
	int x;

	x = 0;
	if (!dest || !src)
	{
		return (NULL);
	}
	while (dest[x] != '\0')
	{
		x++;
	}
	while (src[n] != '\0')
	{
		dest[x] = src[n];
		x = x + 1;
		n = n + 1;
	}
	if (dest[x - 1] == '\n')
	{
		dest[x - 1] = '\0';
	}
	dest[x] = '\0';
	return (dest);
}

char *get_interface_enocean()
{
	FILE *file;
	char line[128];
	char *interface;

	if ((file = fopen("/etc/domoleaf/slave.conf", "r")) == NULL)
	{
		fprintf(stderr, "Error for open /etc/domoleaf/slave.conf\n");
		return (NULL);
	}
	while (fgets(line, 128, file) != NULL)
	{
		if (strncmp(line, "[enocean]", 9) == 0)
		{
			while (fgets(line, 128, file) != NULL)
			{
				if (strncmp(line, "interface = ", 12) == 0)
				{
					if (strlen(line) > 13)
					{
						interface = malloc((sizeof(char) * strlen(line)) - 6);
						memset(interface, '\0', strlen(line) - 6);
						strcpy(interface, "/dev/");
						strcpy_to_n(interface, line, 12);
						fclose(file);
						if (strcmp(interface, "/dev/none") == 0)
						{
							fprintf(stderr, "No interface has been enter\n");
							exit(EXIT_FAILURE);
						}
						return (interface);
					}
				}
			}
		}
	}
	fclose(file);
	fprintf(stderr, "Error while reading /etc/domoleaf/slave.conf\n");
	return (NULL);
}
