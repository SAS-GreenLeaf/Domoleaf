#!/usr/bin/python3

import logging;
import json;
from Logger import *;
from MysqlHandler import *;
from MasterSql import *;
import time;

LOG_FILE                       = '/var/log/domoleaf/domomaster.log'
TIME_INTERVAL                  = 600  #10min 
TIME_RANGE_TO_CALC             = 3600 #1h
TIME_BEFORE_TIME_TO_CALC       = 1800 #30min

#TEST_TIMESTAMP                  = 1448274600

class CalcLogs:

    def __init__(self, daemon):
        self.logger = Logger(False, LOG_FILE);
        self.logger.info('Init CalcLogs');
        self.sql = MasterSql();
        self.daemon = daemon;
        self.devices_list = {};
        self.devices_list_update();

    def devices_list_update(self):
        self.logger.info('Updating Logs');
        query = ('SELECT room_device.daemon_id, room_device_option.addr_plus, room_device_option.addr, '
                 'room_device.room_device_id, room_device_option.option_id, room_device.name '
                 'FROM room_device '
                 'JOIN room_device_option '
                 'ON room_device.room_device_id = room_device_option.room_device_id '
                 'WHERE daemon_id IS NOT NULL '
                 'ORDER BY room_device_id');
        res = self.sql.mysql_handler_personnal_query(query);
        self.devices_list = res;
        self.devices_list = self.get_tab_devices_list(res);

    def get_tab_devices_list(self, res):
        tab = {};
        for r in res:
            daemon_id = r[0];
            addr_plus = r[1];
            addr = r[2];
            if (daemon_id not in tab):
                tab[daemon_id] = {};
            if (addr_plus):
                if (daemon_id in tab and addr_plus not in tab[daemon_id]):
                    tab[daemon_id][addr_plus] = [];
                    r = r[3:];
                    tab[daemon_id][addr_plus].append(r);
            else:
                if (daemon_id in tab and addr not in tab[daemon_id]):
                    tab[daemon_id][addr] = [];
                    r = r[3:];
                    tab[daemon_id][addr].append(r);
        return tab;

    def get_only_good_logs(self, res):
        tab = [];
        for r in res:
            if (r[1] in self.devices_list[r[0]]):
                log = [];
                log.append(r[2]);
                log.append(r[3]);
                log.append(self.devices_list[r[0]][r[1]][0][0]);
                log.append(self.devices_list[r[0]][r[1]][0][1]);
                log.append(r[4]);
                log.append(r[5]);
                log.append(r[1]);
                log.append(r[0]);
                tab.append(log);
        return tab;

    def get_good_time_range(self, res):
        now = time.time();
        #now = TEST_TIMESTAMP;
        end_tr = now - TIME_BEFORE_TIME_TO_CALC - 1;
        init_tr = end_tr - TIME_RANGE_TO_CALC + 1;

        #self.logger.info('NOW = '+ time.strftime("%d %m %Y %H:%M:%S", time.localtime(now)));

        tab = [];
        for r in res:
            date = r[0];
            if (date <= end_tr):
                tab.append(r);
        return (tab);

    def cut_tab(self, tab):
        cut_tab = {};
        cut_dict = {};

        for log in tab:
            device_id = log[2];
            option_id = log[3];
            if (device_id not in cut_tab):
                cut_tab[device_id] = {};
            if (option_id not in cut_tab[device_id]):
                cut_tab[device_id][option_id] = [];
            cut_tab[device_id][option_id].append(log);
        
        for device in cut_tab:
            cut_dict[device] = {};
            for option in cut_tab[device]:
                cut_dict[device][option] = [];
                for log in cut_tab[device][option]:
                    cut_dict[device][option].append(log);

        return cut_dict;

    def calc_average_values(self, dictlogs):
        dictaverage = {};

        for time_r in dictlogs:
            dictaverage[time_r] = {};

            for device in dictlogs[time_r]:
                dictaverage[time_r][device] = {};

                for option in dictlogs[time_r][device]:
                    dictaverage[time_r][device][option] = [];
                    
                    t0 = -1;
                    t1 = 0;
                    avg = 0;
                    avg_elem = [];
                    val = 0;

                    for elem in dictlogs[time_r][device][option]:
                        if (t0 == -1):
                            t0 = elem[0];
                            val = elem[1];
                            avg_elem.append(t0);
                        else:
                            t1 = elem[0];
                            avg = avg + (t1 - t0) * val;
                            t0 = t1;
                            val = elem[1];
                    avg_elem.append(int(avg / TIME_INTERVAL));
                    avg_elem.append(device);
                    avg_elem.append(option);
                    dictaverage[time_r][device][option].append(avg_elem);

        return (dictaverage);


    def get_logs(self, test):
        if (test == 0):
            query = ('SELECT daemon_id, addr_dest, t_date, knx_value, type, addr_src '
                     'FROM knx_log '
                     'ORDER BY t_date');
            res = self.sql.mysql_handler_personnal_query(query);

        else:
            res = self.get_test_logs();
            
        return res;

    def cut_dict_time(self, dictlogs):
        now = time.time();
        #now =  TEST_TIMESTAMP;
        end_tr = now - TIME_BEFORE_TIME_TO_CALC - 1;
        init_tr = end_tr - TIME_RANGE_TO_CALC + 1;
        init = init_tr;
        end = init + TIME_INTERVAL - 1;

        dicttime = {};
        tmpdict = {};
        n = int(TIME_RANGE_TO_CALC / TIME_INTERVAL);
        for time_r in range(n):
            dicttime[time_r] = {};
            tmpdict[time_r] = {};
            for device in dictlogs:
                dicttime[time_r][device] = {};
                tmpdict[time_r][device] = {};

                for option in dictlogs[device]:
                    dicttime[time_r][device][option] = [];
                    tmpdict[time_r][device][option] = [];
                                        
                    first = tmpdict[time_r][device][option];
                    if (not first):
                        first = list(dictlogs[device][option][0]);
                        first[0] = init;
                    last = list(first);
                    dicttime[time_r][device][option].append(first);
                    
                    for log in dictlogs[device][option]:
                        date = log[0];
                        if (date >= init and date <= end):
                            dicttime[time_r][device][option].append(log);
                            last = list(log);
                        if (date > end):
                            break;
                    
                    last[0] = end + 1;
                    dicttime[time_r][device][option].append(last);
                    tmpdict[time_r][device][option] = list(last);
                            
            init = init + TIME_INTERVAL;
            end = end + TIME_INTERVAL;

        return dicttime;
        

    def save_graph_logs(self, dictlogs):

        query = ('INSERT INTO graphic_log '
                 '(date, value, room_device_id, option_id) '
                 'VALUES ');

        for time_r in dictlogs:
            for device in dictlogs[time_r]:
                for option in dictlogs[time_r][device]:
                    for log in dictlogs[time_r][device][option]:
                        query += '(' + str(log[0]) + ', ';
                        query += str(log[1]) + ', ';
                        query += str(log[2]) + ', ';
                        query += str(log[3]) + '), ';

        query = query[:-2];
        res = self.sql.mysql_handler_personnal_query(query);

    def delete_knx_logs(self, dictlogs):
        now = time.time();
        #now =  TEST_TIMESTAMP;
        end_tr = now - TIME_BEFORE_TIME_TO_CALC;

        last_logs = [];
        
        for device in dictlogs:
            for option in dictlogs[device]:
                log = dictlogs[device][option][-1];
                last_logs.append(log);

        query = ('DELETE FROM knx_log '
                 'WHERE t_date < ' + str(end_tr));
        res = self.sql.mysql_handler_personnal_query(query);

        query = ('INSERT INTO knx_log '
                 '(type, addr_src, addr_dest, knx_value, t_date, daemon_id) '
                 'VALUES ');
        for log in last_logs:
            query += '( ' + str(log[4]) + ', ';
            query += '\'' + str(log[5]) + '\', ';
            query += '\'' + str(log[6]) + '\', ';
            query += str(log[1]) + ', ';
            query += str(log[0]) + ', ';
            query += str(log[7]) + '), ';

        query = query[:-2];
        res = self.sql.mysql_handler_personnal_query(query);
        
    def sort_logs(self, connection):
        self.logger.info('Sorting Logs');

        try:
            logs = self.get_logs(1);
            
            tablogs = self.get_only_good_logs(logs);
            tablogs = self.get_good_time_range(tablogs);

            dictlogs = self.cut_tab(tablogs);
            dictlogstime = self.cut_dict_time(dictlogs);
            dictlogstime = self.calc_average_values(dictlogstime);
        
            self.save_graph_logs(dictlogstime);
            self.delete_knx_logs(dictlogs);
            self.logger.info('\n\n');
            
        except Exception as e:
            self.logger.error(e);


    def get_test_logs(self):
        res = [(1, '0/0/12', 1448268700, 1), (1, '0/1/23', 1448268800, 3515), (1, '0/0/14', 1448268850, 255), (1, '0/0/17', 1448268860, 1), (1, '0/1/1', 1448268900, 1786), (1, '0/1/1', 1448270540, 1786), (1, '0/1/1', 1448270661, 1788), (1, '0/1/23', 1448270719, 3548), (1, '0/1/1', 1448270781, 1790), (1, '0/1/1', 1448270901, 1792), (1, '0/1/1', 1448271141, 1796), (1, '0/0/12', 1448271148, 1), (1, '0/0/14', 1448271152, 255), (1, '0/1/23', 1448271155, 3598), (1, '0/0/17', 1448271160, 1), (1, '0/0/19', 1448271163, 255), (1, '0/1/23', 1448271167, 3398), (1, '0/0/12', 1448271173, 0), (1, '0/0/14', 1448271175, 0), (1, '0/0/12', 1448271179, 0), (1, '0/0/17', 1448271181, 0), (1, '0/0/19', 1448271184, 0), (1, '0/0/17', 1448271188, 0), (1, '0/0/12', 1448271229, 1), (1, '0/0/14', 1448271232, 255), (1, '0/0/17', 1448271235, 1), (1, '0/0/19', 1448271238, 251), (1, '0/0/19', 1448271241, 255), (1, '0/0/12', 1448271245, 0), (1, '0/0/14', 1448271247, 0), (1, '0/0/17', 1448271251, 0), (1, '0/0/19', 1448271253, 0), (1, '0/0/12', 1448271257, 1), (1, '0/0/14', 1448271259, 204), (1, '0/1/1', 1448271262, 1802), (1, '0/0/12', 1448271265, 1), (1, '0/0/14', 1448271268, 255), (1, '0/0/14', 1448271273, 59), (1, '0/0/14', 1448271274, 58), (1, '0/0/17', 1448271281, 1), (1, '0/0/19', 1448271284, 255), (1, '0/1/1', 1448271381, 1806), (1, '0/1/1', 1448271501, 1812), (1, '0/1/1', 1448271621, 1816), (1, '0/1/1', 1448271741, 1824), (1, '0/1/1', 1448272011, 1830), (1, '0/1/23', 1448272062, 3398), (1, '0/1/1', 1448272101, 1834), (1, '0/1/1', 1448272222, 1834), (1, '0/1/1', 1448272295, 1824), (1, '0/1/1', 1448272415, 1822), (1, '0/1/1', 1448272535, 1822), (1, '0/1/1', 1448272655, 1824), (1, '0/1/1', 1448272775, 1824), (1, '0/1/1', 1448272911, 1830), (1, '0/1/23', 1448272963, 3398), (1, '0/1/1', 1448273015, 1836), (1, '0/1/1', 1448273135, 1838), (1, '0/1/1', 1448273255, 1842), (1, '0/1/1', 1448273375, 1844), (1, '0/1/1', 1448273495, 1850), (1, '0/1/1', 1448273615, 1852), (1, '0/1/1', 1448273735, 1856), (1, '0/1/1', 1448273856, 1856), (1, '0/1/23', 1448273863, 3398), (1, '0/1/1', 1448273976, 1858), (1, '0/1/1', 1448274096, 1864), (1, '0/1/1', 1448274216, 1866), (1, '0/1/1', 1448274336, 1870), (1, '0/1/1', 1448274456, 1874), (1, '0/1/1', 1448274576, 1876), (1, '0/1/1', 1448274712, 1882), (1, '0/1/23', 1448274764, 3398), (1, '0/1/24', 1448274767, 3423), (1, '0/1/1', 1448274816, 1884), (1, '0/1/1', 1448274936, 1886), (1, '0/1/1', 1448275056, 1888), (1, '0/1/1', 1448275176, 1892), (1, '0/1/1', 1448275296, 1892), (1, '0/1/1', 1448275416, 1896), (1, '0/1/6', 1448275519, 0), (1, '0/1/8', 1448275522, 0)];
        return (res);
