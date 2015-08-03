#include "enocean.h"

/*
** Def log's colors
*/
char *log_colors[] =
{
	"\033[32m",
	"\033[33m",
	"\033[31m"
};

/*
** Def log types (3 types, 3 colors above)
*/
char		*log_titles[] =
{
	"DEBUG",
	"INFO",
	"ERROR"
};

/*
** Open / create log's file
*/
static FILE *open_enocean_log_file()
{
	static FILE *log_stream = NULL;

	if (log_stream == NULL)
	{
		if ((log_stream = fopen("/var/log/monitor_enocean.log", "w+")) == NULL)
		{
			perror("open log file");
			return (NULL);
		}
	}
	return (log_stream);
}

/*
** Write a message in log's file
*/
void enocean_log(Log_type type, const char *msg)
{
	FILE *log_stream;

	if ((log_stream = open_enocean_log_file()))
	{
		fprintf(log_stream, "[ %s%s\033[0m ]: %s\n", log_colors[type], log_titles[type], msg);
	}
}

/*
** Write packet's structure in log's file
*/
void enocean_log_packet(const Enocean_packet packet)
{
	FILE *log_stream;
	int i;

	if ((log_stream = open_enocean_log_file()))
	{
		fprintf(log_stream, "Sync byte      : %02x\n", packet.sync_byte);
		fprintf(log_stream, "Data length    : %04x\n", packet.header.data_length);
		fprintf(log_stream, "Opt data length: %02x\n", packet.header.opt_data_length);
		fprintf(log_stream, "Packet type    : %02x\n", packet.header.packet_type);
		fprintf(log_stream, "CRC8H          : %02x\n", packet.CRC8H);
		fprintf(log_stream, "[ Data BEGIN ]\n");

		for (i = 0; i < packet.header.data_length; i++)
		{
			fprintf(log_stream, "%02x ", packet.data[i]);
		}

		fprintf(log_stream, "[ Data END ]\n");
		fprintf(log_stream, "[ Opt data BEGIN ]\n");

		for (i = 0; i < packet.header.opt_data_length; i++)
		{
			fprintf(log_stream, "%02x ", packet.opt_data[i]);
		}

		fprintf(log_stream, "[ Opt data END ]\n");
		fprintf(log_stream, "\nCRC8D          : %02x\n", packet.CRC8D);
	}
}
