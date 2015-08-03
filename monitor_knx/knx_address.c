#include "knx.h"

/*
** Convert a KNX logical address to an uint16
*/
uint16_t readgaddr(char *str)
{
	uint32_t a = 0;
	uint32_t b = 0;
	uint32_t c = 0;

	sscanf(str, "%d/%d/%d", &a, &b, &c);
	return ((a & 0x1f) << 11 | (b & 0x07) << 8 | (c & 0xff));
}

/*
** Convert a number from hex to uint32
*/
uint32_t readHex (const char *addr)
{
	int i;
	sscanf (addr, "%x", &i);
	return i;
}

/*
** Convert a KNX logical address to a char
*/
char *group2string(eibaddr_t addr)
{
	char *res;

	res = malloc(10);
	memset(&res[0], 0, 10);
	sprintf(res, "%d/%d/%d", (addr >> 11) & 0x1f, (addr >> 8) & 0x07, addr & 0xff);
	return (res);
}

/*
** Display a buffer in hex
*/
void printHex (int len, uint8_t * data)
{
	int i;
	for (i = 0; i < len; i++)
	{
		printf ("%02X ", data[i]);
	}
	printf("\n");
}

/*
** Convert a KNX physical address to an uint16
*/
uint16_t readaddr(char *str)
{
	uint32_t	a = 0;
	uint32_t	b = 0;
	uint32_t	c = 0;

	sscanf(str, "%d.%d.%d", &a, &b, &c);
	return ((a & 0x0f) << 12 | (b & 0x0f) << 8 | (c & 0xff));
}

/*
** Convert a physical address from an uint16 to a char
*/
char *individual2string(uint16_t addr)
{
	char *res;

	res = malloc(10);
	memset(&res[0], 0, 10);
	sprintf(res, "%d.%d.%d", (addr >> 12) & 0x0f, (addr >> 8) & 0x0f, (addr) & 0xff);
	return (res);
}
