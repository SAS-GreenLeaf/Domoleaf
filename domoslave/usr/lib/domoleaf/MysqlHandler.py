#!/usr/bin/python3

import mysql.connector;

class MysqlHandler:
    """
    Mysql Master daemon management class.
    Built with 'username', 'password' and 'database' for the connection to the server.
    """
    def __init__(self, username, passwd, db):
        self.connection = mysql.connector.connect(unix_socket = '/var/run/mysqld/mysqld.sock', user = username, password=passwd, host='localhost', database=db);

    def get_field_names_from_table(self, table):
        """
        Takes as parameter the 'table' name of a table
        return the field names.
        """
        query = 'DESCRIBE '+table;
        cursor = self.connection.cursor(buffered=True);
        res = [];
        append = res.append;
        cursor.execute(query);
        for item in cursor:
            append(item[0]);
        return res;

    def personnal_query(self, query):
        """
        Function used to send a personnal query, which is not proposed by this class.
        """
        cursor = self.connection.cursor(buffered=True);
        cursor.execute(query);
        res = [];
        append = res.append;
        for item in cursor:
            append(item);
        return res;

    def insert_datas_in_table(self, table, field_names, data_values):
        """
        Inserts 'data_values' in 'field_names' in the table 'table'.
        """
        cursor = self.connection.cursor(buffered=False);
        query = "INSERT INTO "+table+" (";
        i = 0;
        field_len = len(field_names)
        while i < field_len - 1:
            query += field_names[i]+', ';
            i += 1;
        query += field_names[field_len - 1]+") VALUES (";
        i = 0;
        while i < field_len - 1:
            query += "%s, ";
            i += 1;
        query += "%s)";
        cursor.execute(query, data_values);

    def update_datas_in_table(self, table: str, data_values_ref: dict, data_to_update: dict):
        """
        Updates data in table 'table'. If data having to be inserted do not exist, they are created and inserted.
        'data_values_ref' is a dict containing the field names to update.
        'data_to_update' is a dict containing the data to insert.
        """
        cursor = self.connection.cursor(buffered=True);
        try:
            query_insert = "INSERT INTO "+table+" (";
            for data_name in data_values_ref.keys():
                query_insert += data_name+", ";
            for data_name in data_to_update.keys():
                query_insert += data_name+", ";
            query_insert = query_insert[:len(query_insert) - 2];
            query_insert += ") VALUES (";
            i = 0;
            while i < len(data_values_ref) + len(data_to_update):
                query_insert += "%s, ";
                i += 1;
            query_insert = query_insert[:len(query_insert) - 2];
            query_insert += ")";
            data = tuple(data_values_ref.values()) + tuple(data_to_update.values());
            cursor.execute(query_insert, data);
        except Exception as e:
            query_update = "UPDATE "+table+" SET ";
            for data_name in data_to_update.keys():
                data = data_to_update[data_name];
                query_update += data_name+"=\""+data+"\", ";
            query_update = query_update[:len(query_update) - 2];
            query_update += " WHERE ";
            for data_name in data_values_ref.keys():
                data = data_values_ref[data_name];
                query_update += data_name+"=\""+data+"\" AND ";
            query_update = query_update[:len(query_update) - 5];
            cursor.execute(query_update);

    def reset_table(self, table):
        """
        Erase content of the table 'table'
        """
        query = "DELETE FROM "+table;
        cursor = self.connection.cursor(buffered=True);
        cursor.execute(query);

    def get_datas_from_table_with_names(self, table: str, names: list):
        """
        Retrieves fields values from a table.
        'table' is the table name.
        'names' is a list containing the field names to retrieve.
        """
        res = [];
        append = res.append;
        query = "SELECT ";
        for name in names:
            query += name+", ";
        query = query[:len(query) - 2];
        query += " FROM "+table;
        cursor = self.connection.cursor(buffered=True);
        cursor.execute(query);
        for item in cursor:
            append(item);
        return res;

    def get_all_datas_from_table(self, table: str):
        """
        Retrieves all data from the table 'table'.
        """
        res = [];
        append = res.append;
        query = "SELECT * FROM "+table;
        cursor = self.connection.cursor(buffered=True);
        cursor.execute(query);
        for item in cursor:
            append(item);
        return res;

    def updatedb(self):
        """
        Commits changes done to database.
        """
        self.connection.commit();

    def close(self):
        """
        Close connection to mysql server.
        """
        self.connection.close();
