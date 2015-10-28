#include "enocean.h"

/*
** Global flag for the main loop
*/
int g_flag;

/*
** Reinit select 'slave_listen_sock' socket's
** Put read and optionally file descriptor in Les "fd_set"
*/
int init_select(int enocean_dev, int slave_listen_sock,
                fd_set *rfds, fd_set __attribute__((unused)) *wfds)
{
	struct timeval timeout;
	int    ret;

	FD_ZERO(rfds);
	FD_ZERO(wfds);
	FD_SET(enocean_dev, rfds);
	FD_SET(slave_listen_sock, rfds);
	timeout.tv_sec = 0;
	timeout.tv_usec = 50;
	ret = select(slave_listen_sock + 1, rfds, wfds, NULL, &timeout);

	return (ret);
}

/*
** Read input stream from USB key
** Fill buffer and return full buffer with the content of
** the telegram after packet treatment
*/
void monitor_enocean(int enocean_dev)
{
	int len;
	static uint8_t buffer[255];
	static int cpt = 0;
	static uint16_t data_len = 0;
	static uint8_t opt_data_len = 0;
	static uint8_t packet_type = 0;
	Enocean_packet packet;

	if (buffer[0] != 0x0 && buffer[0] != 0x55)
	{
		memset(buffer, 0, 255);
		cpt = 0;
		data_len = 0;
		return;
	}

	while ((len = read(enocean_dev, &buffer[cpt], 255)) > 0)
	{
		cpt += len;
		if (cpt >= 5 && data_len == 0)
		{
			memcpy(&data_len, &buffer[1], sizeof(data_len));
			data_len = ntohs(data_len);
			opt_data_len = buffer[3];
			packet_type = buffer[4];
		}
		else if (cpt >= 7 + data_len + opt_data_len)
		{
			buffer[cpt] = 0;
			packet = create_packet(buffer, data_len, opt_data_len, packet_type);
			enocean_log(LOG_INFO, "Received data on bus");
			thread_treat_packet(&packet);
			cpt = 0;
			memset(buffer, 0, 256);
			data_len = 0;
			break;
		}
	}
}

/*
** Function called after CTRL-C
** Define "g_flag" at 0 to exit loop
*/
void sig_int(int __attribute__((unused)) signum)
{
	g_flag = 0;
}

/*
** Main loop
*/
void run(int enocean_dev, int slave_listen_sock)
{
	int    ret_select;
	fd_set rfds;
	fd_set wfds;

	g_flag = 1;
	while (g_flag)
	{
		signal(SIGINT, sig_int);
		if ((ret_select = init_select(enocean_dev, slave_listen_sock, &rfds, &wfds)) > 0)
		{
			if (FD_ISSET(enocean_dev, &rfds))
			{
				monitor_enocean(enocean_dev);
			}
			else if (FD_ISSET(slave_listen_sock, &rfds))
			{
				/* TODO: Do function who will receive datas
				 * from slave daemon on socket */
				printf("hi\n");
			}
		}
	}
	printf("closing open device\n");
}

/*
** Fill structure 'args' to treat arguments
*/
void check_args(t_args *args, int argc, char *argv[])
{
	int i;

	for (i = 1; i < argc; i++)
	{
		if (strcmp(argv[i], "--daemon") == 0)
		{
			args->daemon = 1;
		}
		else if (strncmp(argv[i], "/dev", 4) == 0)
		{
			args->device = argv[i];
		}
	}
}

int main(int argc, char *argv[])
{
	int enocean_dev;
	int slave_listen_sock;
	struct termios options;
	struct termios	backup;
	const char *dev_name = get_interface_enocean();
	int    pid;
	FILE   *pid_file;
	t_args args;

	if (dev_name == NULL)
	{
		dev_name = "/dev/ttyUSB0";
	}
	memset(&args, 0, sizeof(args));
	check_args(&args, argc, argv);
	if (args.daemon)
	{
		if ((pid = fork()) == -1)
		{
			fprintf(stderr, "[ ENOCEAN ]: Error while attempting to start daemon\n");
			return (1);
		}

		if (pid == 0)
		{
			if ((enocean_dev = init((args.device == NULL ? dev_name : args.device), &options, &backup)) == -1)
			{
				return (1);
			}

			enocean_log(LOG_INFO, "Successfully initialized");

			/* TODO : use configuration file */
			if ((slave_listen_sock = init_listen_slave_socket("127.0.0.1", 4248)) == -1)
			{
				return (1);
			}
			run(enocean_dev, slave_listen_sock);
			if (!xtcsetattr(enocean_dev, &backup))
				return (2);
			close(enocean_dev);
		}
		else
		{
			if ((pid_file = fopen("/var/run/monitor_enocean.pid", "w")) == NULL)
			{
				perror("fopen");
				return (1);
			}
			fprintf(pid_file, "%d", pid);
			fclose(pid_file);
		}
	}
	else
	{
		if ((enocean_dev = init((args.device == NULL ? dev_name : args.device), &options, &backup)) == -1)
		{
			return (1);
		}

		enocean_log(LOG_INFO, "Successfully initialized");

		/* TODO : use configuration file */
		if ((slave_listen_sock = init_listen_slave_socket("127.0.0.1", 4248)) == -1) {
			return (1);
		}

		run(enocean_dev, slave_listen_sock);

		if (!xtcsetattr(enocean_dev, &backup))
		{
			return (2);
		}
		close(enocean_dev);
	}
	return (0);
}
