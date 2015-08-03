/*
**  GNU Pth - The GNU Portable Threads
**  Copyright (c) 1999-2006 Ralf S. Engelschall <rse@engelschall.com>
**
**  This file is part of GNU Pth, a non-preemptive thread scheduling
**  library which can be found at http://www.gnu.org/software/pth/.
**
**  This library is free software; you can redistribute it and/or
**  modify it under the terms of the GNU Lesser General Public
**  License as published by the Free Software Foundation; either
**  version 2.1 of the License, or (at your option) any later version.
**
**  This library is distributed in the hope that it will be useful,
**  but WITHOUT ANY WARRANTY; without even the implied warranty of
**  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
**  Lesser General Public License for more details.
**
**  You should have received a copy of the GNU Lesser General Public
**  License along with this library; if not, write to the Free Software
**  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
**  USA, or contact Ralf S. Engelschall <rse@engelschall.com>.
**
**  pth_time.c: Pth time calculations
*/
                             /* ``Real programmers confuse
                                  Christmas and Halloween
                                  because DEC 25 = OCT 31.''
                                             -- Unknown     */
#include "pth_p.h"

#if cpp
#if defined(HAVE_CLOCK_MONOTONIC) && (defined(HAVE_CLOCK_GETTIME) || defined(HAVE___NR_CLOCK_GETTIME))
#define USE_INT_CLOCK
#else
#undef USE_INT_CLOCK
#endif

typedef struct {
  long t_sec;
  long t_usec;
} pth_itime_t;

#define PTH_TIME_NOW  (pth_itime_t *)(0)
#define PTH_TIME_ZERO &pth_time_zero
#define PTH_TIME(sec,usec) { sec, usec }
#define pth_time_equal(t1,t2) \
        (((t1).t_sec == (t2).t_sec) && ((t1).t_usec == (t2).t_usec))
#endif /* cpp */

/* a global variable holding a zero time */
intern pth_itime_t pth_time_zero = { 0L, 0L };

/* sleep for a specified amount of microseconds */
intern void pth_time_usleep(unsigned long usec)
{
#ifdef HAVE_USLEEP
    usleep((unsigned int )usec);
#else
    struct timeval timeout;
    timeout.tv_sec  = usec / 1000000;
    timeout.tv_usec = usec - (1000000 * timeout.tv_sec);
    while (pth_sc(select)(1, NULL, NULL, NULL, &timeout) < 0 && errno == EINTR) ;
#endif
    return;
}

/* calculate: t1 = t2 */
#if cpp
#if defined(HAVE_GETTIMEOFDAY_ARGS1)
#define __gettimeofday(t) gettimeofday(t)
#else
#define __gettimeofday(t) gettimeofday(t, NULL)
#endif
#define pth_time_set(t1,t2) \
    do { \
        if ((t2) == PTH_TIME_NOW) \
        { \
            pth_get_int_time((t1)); \
        } else { \
            (t1)->t_sec  = (t2)->t_sec; \
            (t1)->t_usec = (t2)->t_usec; \
        } \
    } while (0)

#define pth_zero_time(t1) \
   do { \
     (t1)->tv_sec = 0; (t1)->tv_usec = 0; \
   } while (0)

#ifdef USE_INT_CLOCK

#define pth_itime_to_time(t1, t2) \
   do { \
     pth_itime_t ti, te; \
     pth_get_int_time (&ti); \
     pth_get_ext_time (&te); \
     pth_time_sub(&te, &ti); \
     pth_time_add(&te, (t2)); \
     (t1)->tv_sec = te.t_sec; (t1)->tv_usec = te.t_usec; \
   } while (0)

#define pth_time_to_itime(t1, t2) \
   do { \
     pth_itime_t ti, te; \
     pth_get_int_time (&ti); \
     pth_get_ext_time (&te); \
     (t1)->t_sec = (t2)->tv_sec; (t1)->t_usec = (t2)->tv_usec; \
     pth_time_sub((t1), &te); \
     pth_time_add((t1), &ti); \
   } while (0)
#else

#define pth_itime_to_time(t1, t2) \
   do { \
     (t1)->tv_sec = (t2)->t_sec; (t1)->tv_usec = (t2)->t_usec; \
   } while (0)

#define pth_time_to_itime(t1, t2) \
   do { \
     (t1)->t_sec = (t2)->tv_sec; (t1)->t_usec = (t2)->tv_usec; \
   } while (0)

#endif

#define pth_itime_to_time_iv(t1, t2) \
   do { \
     (t1)->tv_sec = (t2)->t_sec; (t1)->tv_usec = (t2)->t_usec; \
   } while (0)

#define pth_time_to_itime_iv(t1, t2) \
   do { \
     (t1)->t_sec = (t2)->tv_sec; (t1)->t_usec = (t2)->tv_usec; \
   } while (0)

#endif /* cpp */

#if defined(HAVE_CLOCK_MONOTONIC) && !defined(HAVE_CLOCK_GETTIME) && defined(HAVE___NR_CLOCK_GETTIME)

#include <sys/syscall.h>

static int my_clock_gettime(int clock_id, struct timespec *ts)
{
  return syscall(__NR_clock_gettime, clock_id, ts);
}

#endif

#if defined(HAVE_CLOCK_MONOTONIC) && defined(HAVE_CLOCK_GETTIME)
#define my_clock_gettime clock_gettime
#endif

intern void pth_get_int_time(pth_itime_t *tv)
{
#ifdef USE_INT_CLOCK
  pth_time_t t1;
  struct timespec ts;
  int res;

  res = my_clock_gettime(CLOCK_MONOTONIC, &ts);
  if (!res)
    {
      tv->t_sec = ts.tv_sec;
      tv->t_usec = ts.tv_nsec / 1000;
      return;
    }

  __gettimeofday(&t1);
  tv->t_sec = t1.tv_sec;
  tv->t_usec = t1.tv_usec;
#else
  pth_get_ext_time(tv);
#endif
}

intern void pth_get_ext_time(pth_itime_t *tv)
{
  pth_time_t t1;
  __gettimeofday(&t1);
  tv->t_sec = t1.tv_sec;
  tv->t_usec = t1.tv_usec;
}

/* time value constructor */
pth_time_t pth_time(long sec, long usec)
{
    pth_time_t t;

    t.tv_sec  = sec;
    t.tv_usec = usec;
    return t;
}

/* time value constructor */
intern pth_itime_t pth_itime(long sec, long usec)
{
    pth_itime_t tv;
    pth_time_t t;

    tv.t_sec  = sec;
    tv.t_usec = usec;
    return tv;
}

/* timeout value constructor */
pth_time_t pth_timeout(long sec, long usec)
{
    pth_itime_t tv;
    pth_itime_t tvd;
    pth_time_t t;

    pth_time_set(&tv, PTH_TIME_NOW);
    tvd.t_sec  = sec;
    tvd.t_usec = usec;
    pth_time_add(&tv, &tvd);
    pth_itime_to_time(&t, &tv);
    return t;
}

/* timeout value constructor */
intern pth_itime_t pth_itimeout(long sec, long usec)
{
    pth_itime_t tv;
    pth_itime_t tvd;

    pth_time_set(&tv, PTH_TIME_NOW);
    tvd.t_sec  = sec;
    tvd.t_usec = usec;
    pth_time_add(&tv, &tvd);
    return tv;
}

void pth_int_time(struct timespec * ts)
{
  pth_itime_t tv;
  pth_time_set(&tv, PTH_TIME_NOW);
  ts->tv_sec = tv.t_sec;
  ts->tv_nsec = ((long)tv.t_usec) * 1000;
}

/* calculate: t1 <=> t2 */
intern int pth_time_cmp(pth_itime_t *t1, pth_itime_t *t2)
{
    int rc;

    rc = t1->t_sec - t2->t_sec;
    if (rc == 0)
         rc = t1->t_usec - t2->t_usec;
    return rc;
}

/* calculate: t1 = t1 + t2 */
#if cpp
#define pth_time_add(t1,t2) \
    (t1)->t_sec  += (t2)->t_sec; \
    (t1)->t_usec += (t2)->t_usec; \
    if ((t1)->t_usec > 1000000) { \
        (t1)->t_sec  += 1; \
        (t1)->t_usec -= 1000000; \
    }
#endif

/* calculate: t1 = t1 - t2 */
#if cpp
#define pth_time_sub(t1,t2) \
    (t1)->t_sec  -= (t2)->t_sec; \
    (t1)->t_usec -= (t2)->t_usec; \
    if ((t1)->t_usec < 0) { \
        (t1)->t_sec  -= 1; \
        (t1)->t_usec += 1000000; \
    }
#endif

/* calculate: t1 = t1 / n */
intern void pth_time_div(pth_itime_t *t1, int n)
{
    long q, r;

    q = (t1->t_sec / n);
    r = (((t1->t_sec % n) * 1000000) / n) + (t1->t_usec / n);
    if (r > 1000000) {
        q += 1;
        r -= 1000000;
    }
    t1->t_sec  = q;
    t1->t_usec = r;
    return;
}

/* calculate: t1 = t1 * n */
intern void pth_time_mul(pth_itime_t *t1, int n)
{
    t1->t_sec  *= n;
    t1->t_usec *= n;
    t1->t_sec  += (t1->t_usec / 1000000);
    t1->t_usec  = (t1->t_usec % 1000000);
    return;
}

/* convert a time structure into a double value */
intern double pth_time_t2d(pth_itime_t *t)
{
    double d;

    d = ((double)t->t_sec*1000000 + (double)t->t_usec) / 1000000;
    return d;
}

/* convert a time structure into a integer value */
intern int pth_time_t2i(pth_itime_t *t)
{
    int i;

    i = (t->t_sec*1000000 + t->t_usec) / 1000000;
    return i;
}

/* check whether time is positive */
intern int pth_time_pos(pth_itime_t *t)
{
    if (t->t_sec > 0 && t->t_usec > 0)
        return 1;
    else
        return 0;
}

